#!/bin/bash
set -e

echo "Start installation (v27)"


wait_apt() {
    while fuser /var/lib/dpkg/lock-frontend >/dev/null 2>&1 || \
          fuser /var/lib/apt/lists/lock >/dev/null 2>&1; do
        echo "Waiting for apt lock..."
        sleep 3
    done
}

domain_name=$1
melbis_version=$2
if [ -z "$domain_name" ]; then
    echo "Error: Domain name is required."    
    exit 1
fi

echo "Detected Melbis Version: $melbis_version"

echo "Cleaning up previous installation..."
crontab -r 2>/dev/null || true
cd /var/melbis 2>/dev/null && docker compose down --volumes --remove-orphans 2>/dev/null || true
sleep 2
if mountpoint -q /var/melbis/cache; then
    umount /var/melbis/cache 2>/dev/null || umount -f /var/melbis/cache 2>/dev/null || umount -l /var/melbis/cache
    sleep 1
fi
rm -rf /var/melbis/{db,www,certs,certbot,trick,logs,cache}
rm -f /var/melbis/docker-compose.yml /var/melbis/nginx.conf /var/melbis/my.cnf /var/melbis/.credentials

echo "Configuring systemd journal log limit to 200M..."
sed -i -E 's/^#?SystemMaxUse=.*/SystemMaxUse=200M/' /etc/systemd/journald.conf
grep -q "^SystemMaxUse=200M" /etc/systemd/journald.conf || echo "SystemMaxUse=200M" >> /etc/systemd/journald.conf || true
systemctl restart systemd-journald

echo "Configuring 4GB swap space..."
if [ ! -f /swapfile ]; then
    fallocate -l 4G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
    echo "Swap configured successfully."
else
    echo "Swapfile already exists. Skipping."
fi


echo "Starting installation Melbis Shop for: $domain_name"

echo "Updating package list..."
apt-get update

echo "Installing prerequisites..."
wait_apt && apt-get install -y ca-certificates curl gnupg

echo "Adding Docker's official GPG key..."
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor --yes -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg

echo "Setting up the Docker repository..."
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  tee /etc/apt/sources.list.d/docker.list > /dev/null

echo "Updating package list with Docker repository..."
apt-get update

echo "Installing Docker engine and plugins..."
wait_apt && apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Prepare file structure
echo "Creating base directories..."
mkdir -p /var/melbis/{db,www,certs,certbot,trick,logs/apache,logs/nginx}

# Generate self-signed SSL certificate
echo "Generating self-signed SSL certificate..."
openssl req -x509 -newkey rsa:4096 -keyout /var/melbis/certs/privkey.pem -out /var/melbis/certs/fullchain.pem -days 3650 -nodes -subj "/CN=$domain_name"


echo "Configuring 1024MB RAM disk for web cache..."
mkdir -p /var/melbis/cache
mount -t tmpfs -o size=1024m tmpfs /var/melbis/cache
if ! grep -q "/var/melbis/cache" /etc/fstab; then
    echo 'tmpfs /var/melbis/cache tmpfs size=1024m,noatime 0 0' >> /etc/fstab
fi

echo "Configuring log rotation..."
cat <<'EOF'> /etc/logrotate.d/melbis
/var/melbis/logs/apache/*.log /var/melbis/logs/nginx/*.log {
daily
rotate 7
compress
delaycompress
missingok
notifempty
copytruncate
}
EOF

echo "Generating secure passwords for MySQL..."
mysql_root_pw=$(openssl rand -hex 16)
mysql_db_pw=$(openssl rand -hex 8)

echo "Creating docker-compose.yml..."
cat > /var/melbis/docker-compose.yml << EOF
services:
  db:
    image: mysql:8.4
    container_name: melbis_db
    environment:
      MYSQL_ROOT_PASSWORD: ${mysql_root_pw}
      MYSQL_DATABASE: melbis
      MYSQL_USER: melbis
      MYSQL_PASSWORD: ${mysql_db_pw}
      MYSQL_CHARACTER_SET_SERVER: utf8mb4
      MYSQL_COLLATION_SERVER: utf8mb4_unicode_ci
    volumes:
      - /var/melbis/db:/var/lib/mysql
      - /var/melbis/my.cnf:/etc/mysql/conf.d/my.cnf
    restart: always
    logging:
      driver: "none"

  web:
    depends_on:
      - db
    image: melbis/melbis-shop:v${melbis_version}
    container_name: melbis_shop
    volumes:
      - /var/melbis/www/:/var/www/html/
      - /var/www/html/core
      - /var/melbis/cache:/var/www/html/core/cache
      - /var/melbis/trick:/var/www/html/core/trick
      - /var/melbis/logs/apache:/var/log/apache2
    restart: always
    logging:
      driver: "none"

  nginx:
    image: nginx:latest
    container_name: melbis_proxy
    volumes:
      - /var/melbis/nginx.conf:/etc/nginx/nginx.conf
      - /var/melbis/certs:/etc/nginx/certs
      - /var/melbis/certbot:/etc/nginx/certbot
      - /var/melbis/logs/nginx:/var/log/nginx
    ports:
      - 80:80
      - 443:443
    depends_on:
      - web
    restart: always
    logging:
      driver: "none"
EOF

echo "Creating nginx.conf..."
cat <<'EOF'> /var/melbis/nginx.conf
events {}
http {
    server {
        listen 80;
        listen [::]:80;
        listen 443 ssl;
        listen [::]:443 ssl;
        client_max_body_size 64M;
        ssl_certificate /etc/nginx/certs/fullchain.pem;
        ssl_certificate_key /etc/nginx/certs/privkey.pem;
        location /.well-known/ {
            root /etc/nginx/certbot;
        }

        location / {
            proxy_pass http://web:80;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;

            proxy_buffer_size           128k;
            proxy_buffers               4 256k;
            proxy_busy_buffers_size     256k;

            proxy_connect_timeout       300;
            proxy_send_timeout          300;
            proxy_read_timeout          300;
            send_timeout                300;

        }
    }
}
EOF

echo "Creating my.cnf for MySQL..."
cat <<'EOF'> /var/melbis/my.cnf
[mysqld]

disable_log_bin

innodb_buffer_pool_size = 128M
innodb_buffer_pool_instances = 1

key_buffer_size = 512M

concurrent_insert = 2

read_buffer_size = 256K
read_rnd_buffer_size = 1M
sort_buffer_size = 2M

bulk_insert_buffer_size = 32M
myisam_sort_buffer_size = 128M

table_open_cache = 4096
table_definition_cache = 1536
open_files_limit = 65535

EOF

echo "Downloading and extracting Melbis Shop base files..."
wait_apt && apt-get install -y unzip
rm -rf /tmp/melbis-shop-master /tmp/master.zip
wget -q https://github.com/melbis/melbis-shop/archive/refs/heads/master.zip -O /tmp/master.zip
unzip -qo /tmp/master.zip -d /tmp/

mv /tmp/melbis-shop-master/files /var/melbis/www/
mv /tmp/melbis-shop-master/profiles /var/melbis/www/
mv /tmp/melbis-shop-master/templates /var/melbis/www/
mv /tmp/melbis-shop-master/units /var/melbis/www/
mv /tmp/melbis-shop-master/.htaccess /var/melbis/www/
mv /tmp/melbis-shop-master/index.php /var/melbis/www/
mv /tmp/melbis-shop-master/cron.php /var/melbis/www/
mv /tmp/melbis-shop-master/admin.here /var/melbis/www/
mv /tmp/melbis-shop-master/robots.txt /var/melbis/www/

rm -rf /tmp/master.zip /tmp/melbis-shop-master


echo "Creating Melbis Shop config.json..."
melbis_debug_key=$(openssl rand -hex 8)
cat > /var/melbis/www/config.json << EOF
{
    "MELBIS_DB_HOST_NAME": "db",
    "MELBIS_DB_USER_NAME": "melbis",
    "MELBIS_DB_USER_PASS": "${mysql_db_pw}",
    "MELBIS_DB_NAME": "melbis",
    "MELBIS_DB_NICK": "ms",
    "MELBIS_DB_CHARSET": "utf8mb4",
    "MELBIS_DB_COMMAND": "SET sql_mode = CONCAT(@@sql_mode, ',NO_UNSIGNED_SUBTRACTION');",
    "MELBIS_DB_ENGINE": "MyISAM",
    "MELBIS_DB_ENGINE_TEMP": "Memory",
    "MELBIS_TIME_ZONE": "UTC",
    "MELBIS_LANG": "en",
    "MELBIS_CHARSET": "UTF-8",
    "MELBIS_DESKTOP_CHARSET": "WIN1251",
    "MELBIS_CACHE": true,
    "MELBIS_TEMPLATE": "default",
    "MELBIS_ROOT": "/",
    "MELBIS_BUILD": "1",
    "MELBIS_DEBUG_CODE": "${melbis_debug_key}",
    "MELBIS_USER_LOG": false,
    "MELBIS_IP_LIST": "",
    "MELBIS_BACKUP_TIME_BEGIN": "05:00:00",
    "MELBIS_BACKUP_TIME_END": "05:30:00"
}
EOF

echo "Starting Melbis Shop containers..."
cd /var/melbis
docker compose up -d

echo "Installing Certbot..."
wait_apt && apt-get install -y certbot

echo "Waiting 5 seconds for Nginx container to start completely..."
sleep 5

echo "Requesting Let's Encrypt SSL certificate for ${domain_name}..."
certbot certonly --webroot --non-interactive --keep-until-expiring --register-unsafely-without-email --agree-tos -w /var/melbis/certbot -d "${domain_name}"

echo "Applying new SSL certificates..."
cp -Lf /etc/letsencrypt/live/${domain_name}/* /var/melbis/certs/

echo "Reloading Nginx with new SSL certificate..."
cd /var/melbis
nginx_ready=0
for i in $(seq 1 30); do    
    if docker compose exec -T nginx nginx -t </dev/null>/dev/null 2>&1; then
        nginx_ready=1
        break
    fi
    echo "Waiting for Nginx... ($i/30)"
    sleep 2
done
if [ $nginx_ready -eq 0 ]; then
    echo "WARNING: Nginx did not become ready in time, forcing restart..."    
    docker compose restart nginx </dev/null
else    
    docker compose exec -T nginx nginx -s reload </dev/null
fi

echo "Configuring automatic SSL renewal in cron..."
CRON_JOB="0 3 * * * certbot renew --quiet && cp -Lf /etc/letsencrypt/live/${domain_name}/* /var/melbis/certs/ && cd /var/melbis && docker compose restart nginx"
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo "Configuring melbis shop cron job..."
APP_CRON="* * * * * /usr/bin/docker exec melbis_shop php /var/www/html/cron.php > /dev/null 2>&1"
(crontab -l 2>/dev/null || true; echo "$APP_CRON") | crontab -

echo "Configuring fail2ban..."
wait_apt && apt-get install -y fail2ban

cat > /etc/fail2ban/jail.d/01-custom-defaults.local <<'EOF'
[DEFAULT]
bantime.increment = true
bantime.factor = 2
bantime = 60m
findtime = 10m
maxretry = 5
EOF

systemctl enable fail2ban
systemctl restart fail2ban

echo "Configuring UFW (Firewall)..."
wait_apt && apt-get install -y ufw
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

echo "================================================="
echo "Installation complete!"
echo "Melbis Shop is now running in the background."
echo "================================================="
echo "MELBIS_SETUP_SUCCESSFUL"

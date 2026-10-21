#!/bin/bash

melbis_version=$1

echo "Start system verify (v10)"

echo "Detected Melbis Version: $melbis_version"

echo "================================================="
echo " System Health Status"
echo "================================================="
echo ""

# Uptime and server load
echo "--- Load Average & Uptime ---"
uptime
echo ""

# RAM usage
echo "--- Memory Usage ---"
free -h
echo ""

# Free disk space on root
echo "--- Disk Space (/) ---"
df -h /
echo ""

echo "================================================="
echo " Melbis Shop Storage"
echo "================================================="
echo ""

# Check directory sizes
echo "--- Directory Sizes ---"
if [ -d "/var/melbis" ]; then
    du -sh /var/melbis
    du -sh /var/melbis/db 2>/dev/null || echo "0B    /var/melbis/db"
    du -sh /var/melbis/www/files 2>/dev/null || echo "0B    /var/melbis/www/files"    
    du -sh /var/melbis/cache 2>/dev/null || echo "0B    /var/melbis/cache"
    du -sh /var/melbis/trick 2>/dev/null || echo "0B    /var/melbis/trick"
    du -sh /var/melbis/log 2>/dev/null || echo "0B    /var/melbis/log"
else
    echo "Directory /var/melbis not found."
fi
echo ""

echo "================================================="
echo " SSL Certificate"
echo "================================================="
echo ""
cert=/var/melbis/certs/fullchain.pem
if [ -f "$cert" ]; then
    openssl x509 -in "$cert" -noout -subject -enddate
    end=$(date -d "$(openssl x509 -in "$cert" -noout -enddate | cut -d= -f2)" +%s)
    days=$(( (end - $(date +%s)) / 86400 ))
    if [ "$days" -lt 0 ]; then
        echo "WARNING: the certificate nginx uses has expired"
    elif [ "$days" -lt 20 ]; then
        echo "WARNING: the certificate nginx uses expires in $days days"
    else
        echo "Days left: $days"
    fi
else
    echo "WARNING: no certificate at $cert"
fi
if [ -x /etc/letsencrypt/renewal-hooks/deploy/melbis.sh ]; then
    echo "Renewal hook: installed"
else
    echo "WARNING: no renewal hook - a renewed certificate never reaches nginx"
fi
echo ""
echo "================================================="
echo "MELBIS_VERIFY_SUCCESSFUL"
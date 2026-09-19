# Server

The **"Server"** window manages the store's server directly over SSH: it installs and updates it, shows its state, runs commands, and edits configuration files. Everything is done as **root**, so the section is available to the store owner and is meant for one-off jobs rather than for everyday operation.

The progress of any operation is visible in the **log** at the bottom of the window; the **"Save log"** button writes it out to a text file. The connection is opened for the duration of the operation and closed when it finishes.

## Connection

The **"Connection"** tab holds the parameters shared by the whole window: the server's **IP address**, the **root password**, and the store's **domain name**. The password is not saved: the field is cleared when the window closes.

## Installation

The **"Installation"** tab, the **"Install server"** button — a full automatic installation: the application downloads the project's setup script and deploys the server for the given domain and the current version of the platform.

> **Warning!** Installation deletes all current settings and store data. The
> application asks for confirmation, but once started the operation cannot be
> cancelled.

## Maintenance

The **"Maintenance"** tab:

* **"Update server"** — updating the server to the version matching the current
  version of the application. The result is unpredictable if the server was
  configured by hand, so make a backup before updating.
* **"Status report"** — server diagnostics. It changes nothing and only writes
  information about the state of the store's services to the log.

## Console

The **"Console"** tab runs arbitrary commands: a command is typed into the **"Run command"** field and sent with the **"Send"** button or the Enter key, and the reply goes to the log.

The **"Inside the web container"** checkbox runs the command not on the server itself but inside the store's container, as the web server's user — this is how the engine's environment is checked (versions, file permissions, availability of PHP extensions).

> **Careful!** Without this checkbox the command runs on the server as root.

## Settings

The **"Settings"** tab edits the server's configuration files: **Nginx**, the **MySQL database**, and the **Docker containers**. The order of work: choose the file, press **"Load from server"**, edit the text, press **"Save to server"**.

Only the file that was loaded in this same window can be saved — this rules out writing your edits into someone else's file. The server keeps the previous contents next to it with the `.bak` extension, so one step back is always available.

The **"Restart server"** button stops and starts the store's containers again — this is what applies the edits you have made. The store is unavailable for that time.

> **Careful!** A mistake in a configuration file can stop the store from working.

## What Stands on the Server After Installation

This is how the server looks right after the installation from the program. A real store server may be arranged differently: other versions, edits of one's own in the configuration files, an external database, several sites on one machine. So every value below is a sample rather than a fact about your server until it has been checked on the "Settings" tab.

The installation, update and report scripts the program does not keep. At the press of a button it sends the server one command, and the server itself downloads `setup.sh`, `update.sh` or `verify.sh` from the branch of its own version (`v<version>`) of the public repository [melbis/melbis-shop](https://github.com/melbis/melbis-shop). The `Dockerfile` is there as well — what the store's image is built from. If the server has no way out to GitHub, none of these buttons will work.

### The Folders

The whole household of the store lies in `/var/melbis`:

| Path | What it is |
|---|---|
| `www/` | the site's root: `index.php`, `units/`, `templates/`, `files/`, `config.json` |
| `db/` | the MySQL database files |
| `cache/` | a disk in RAM (1 GB) for the file cache; after a server restart it is empty, and that is normal |
| `trick/`, `log/` | the engine's `core/trick` and `core/log` folders; the apache and nginx logs are written there too |
| `certs/`, `certbot/` | the Let's Encrypt certificate and the folder for its verification |
| `docker-compose.yml`, `nginx.conf`, `my.cnf` | the three files that are edited on the "Settings" tab |

### The Containers

| Container | Image | What is inside |
|---|---|---|
| `melbis_db` | `mysql:8.4` | the database and the `melbis` user, the `utf8mb4` encoding |
| `melbis_shop` | `melbis/melbis-shop:v<version>` | `php:8.3-apache` with the curl, mysqli, xml, zip, iconv, gd, intl and APCu extensions and the ionCube Loader; the engine's core in the image is encoded |
| `melbis_proxy` | `nginx:latest` | ports 80 and 443, encryption, passing the requests on to apache |

The containers have no logs of their own: everything is written as files into `/var/melbis/log`.

On the machine itself there are a 4 GB swap file, a daily log rotation that keeps seven copies, `fail2ban`, the `ufw` firewall with SSH, 80 and 443 open, and two cron jobs: renewing the certificate at 3:00 and the store's `cron.php` once a minute (see "[Task Scheduler](../Dev/cron.md)").

### The Default Limits

| Limit | Value | Where it is set | What happens above it |
|---|---|---|---|
| request size | 64 MB | `client_max_body_size` in `nginx.conf` | nginx answers 413, the request does not reach PHP |
| upload size in PHP | 100 MB | `post_max_size`, `upload_max_filesize` of the image | unreachable: the nginx limit fires earlier |
| files in one request | 100 | `max_file_uploads` of the image | the request is rejected as a whole |
| script run time | 30 s | `max_execution_time` of the image | a long import breaks off, though the proxy waits 300 s |
| script memory | 512 MB | `memory_limit` of the image | the script stops with an error |
| APCu | 64 MB | `apc.shm_size` of the image | the static query cache and the Smart statistics live here; after a container restart it is empty |
| file cache | 1 GB | the disk in memory `/var/melbis/cache` | |
| MySQL index buffer | 512 MB | `key_buffer_size` in `my.cnf` | the main buffer of the MyISAM tables; `innodb_buffer_pool_size` (128 MB) hardly works here |

The memory of a minimal server is already spread out, and the reserve is held by the swap. Raising the MySQL buffers "for speed" on such a machine is a straight path to the system starting to stop processes. Resources are added by changing the hosting plan, not by editing `my.cnf`.

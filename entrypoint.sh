#!/bin/bash
set -e

mkdir -p /var/www/html/profiles /var/www/html/templates /var/www/html/units /var/www/html/files

chown -R www-data:www-data /var/www/html

exec "$@"

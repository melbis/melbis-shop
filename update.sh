#!/bin/bash
set -e

melbis_version=$1

echo "Start melbis shop update (v15)"

echo "Detected Melbis Version: $melbis_version"

WORK_DIR="/var/melbis"
if [ ! -d "$WORK_DIR" ]; then
    echo "Error: Directory $WORK_DIR not found."
    exit 1
fi
cd "$WORK_DIR"

echo "Starting update process for Melbis Shop..."

echo "Stopping containers gracefully..."
docker compose down --timeout 30

echo "Waiting for resources to be released..."
sleep 3

echo "Pulling the latest images..."
docker compose pull

echo "Starting containers..."
docker compose up -d
docker exec melbis_shop chown -R www-data:www-data /var/www/html

echo "Cleaning up old Docker images and volumes..."
docker image prune -af
docker volume prune -f

echo "Updating OS packages..."
export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" upgrade

echo "================================================="
echo "Update complete! Melbis Shop is back online."
echo "================================================="
echo "MELBIS_UPDATE_SUCCESSFUL"
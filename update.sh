#!/bin/bash
set -e

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

echo "Docker disk usage before cleanup:"
docker system df

echo "Cleaning up old Docker images and volumes..."
docker image prune -af
docker volume prune -f

echo "Docker disk usage after cleanup:"
docker system df

echo "================================================="
echo "Update complete! Melbis Shop is back online."
echo "================================================="
echo "MELBIS_UPDATE_SUCCESSFUL"
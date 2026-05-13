#!/bin/bash

echo "Start system verify (v7)"

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
else
    echo "Directory /var/melbis not found."
fi
echo ""
echo "================================================="
echo "MELBIS_VERIFY_SUCCESSFUL"
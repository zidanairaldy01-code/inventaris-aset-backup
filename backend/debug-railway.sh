#!/bin/bash

echo "========================================"
echo "Railway Database Connection Debug"
echo "========================================"
echo ""

echo "Checking environment variables..."
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "DB_PORT: ${DB_PORT:-not set}"
echo "DB_DATABASE: ${DB_DATABASE:-not set}"
echo "DB_USERNAME: ${DB_USERNAME:-not set}"
echo "DB_PASSWORD: ${DB_PASSWORD:+[HIDDEN]}"
echo ""

echo "Checking Railway MySQL variables..."
echo "MYSQLHOST: ${MYSQLHOST:-not set}"
echo "MYSQLPORT: ${MYSQLPORT:-not set}"
echo "MYSQLDATABASE: ${MYSQLDATABASE:-not set}"
echo "MYSQLUSER: ${MYSQLUSER:-not set}"
echo "MYSQLPASSWORD: ${MYSQLPASSWORD:+[HIDDEN]}"
echo ""

echo "Checking MySQL URL..."
echo "MYSQL_URL: ${MYSQL_URL:-not set}"
echo "DATABASE_URL: ${DATABASE_URL:-not set}"
echo ""

# Determine actual connection details
ACTUAL_HOST="${DB_HOST:-${MYSQLHOST:-127.0.0.1}}"
ACTUAL_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
ACTUAL_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-railway}}"
ACTUAL_USER="${DB_USERNAME:-${MYSQLUSER:-root}}"

echo "========================================"
echo "Actual connection will use:"
echo "Host: $ACTUAL_HOST"
echo "Port: $ACTUAL_PORT"
echo "Database: $ACTUAL_DATABASE"
echo "User: $ACTUAL_USER"
echo "========================================"
echo ""

# Test connection
echo "Testing database connection..."
php artisan db:show 2>&1 || echo "❌ Connection failed!"

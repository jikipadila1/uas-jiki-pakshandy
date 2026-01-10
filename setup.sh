#!/bin/bash

echo "========================================"
echo "Setup UAS Jiki Pak Shandy - CodeIgniter 4"
echo "========================================"
echo ""

# Check if composer exists
if command -v composer &> /dev/null; then
    echo "[1/5] Composer found. Installing dependencies..."
    composer install --no-interaction
else
    echo "[1/5] Composer not found. Checking if vendor folder exists..."
    if [ ! -d "vendor" ]; then
        echo "ERROR: Vendor folder not found and Composer is not installed!"
        echo "Please install Composer from https://getcomposer.org/download/"
        echo "Or download the project with vendor folder included from GitHub."
        exit 1
    fi
    echo "Vendor folder found - skipping composer install"
fi

echo ""
echo "[2/5] Creating writable folders..."
mkdir -p writable/cache writable/logs writable/session writable/uploads

echo ""
echo "[3/5] Setting permissions..."
chmod -R 777 writable

echo ""
echo "[4/5] Clearing cache..."
rm -rf writable/cache/*

echo ""
echo "[5/5] Checking database configuration..."
if [ ! -f ".env" ]; then
    echo "WARNING: .env file not found!"
    echo "Please copy .env.example to .env and configure your database settings."
fi

echo ""
echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "Next steps:"
echo "1. Create a database named 'census_db' in MySQL/phpMyAdmin"
echo "2. Import 'census_db.sql' to the database"
echo "3. Check database settings in .env file"
echo "4. Run: php spark serve"
echo "5. Open browser: http://localhost:8080"
echo ""
echo "For detailed instructions, read INSTALL.md"
echo "========================================"

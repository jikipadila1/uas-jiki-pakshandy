@echo off
echo ========================================
echo Setup UAS Jiki Pak Shandy - CodeIgniter 4
echo ========================================
echo.

REM Check if composer exists
where composer >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [1/5] Composer found. Installing dependencies...
    call composer install --no-interaction
) else (
    echo [1/5] Composer not found. Checking if vendor folder exists...
    if not exist "vendor" (
        echo ERROR: Vendor folder not found and Composer is not installed!
        echo Please install Composer from https://getcomposer.org/download/
        echo Or download the project with vendor folder included from GitHub.
        pause
        exit /b 1
    )
    echo Vendor folder found - skipping composer install
)

echo.
echo [2/5] Creating writable folders...
if not exist "writable\cache" mkdir writable\cache
if not exist "writable\logs" mkdir writable\logs
if not exist "writable\session" mkdir writable\session
if not exist "writable\uploads" mkdir writable\uploads

echo.
echo [3/5] Setting permissions...
icacls "writable" /grant "Users:(OI)(CI)F" /T 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Warning: Could not set permissions automatically.
    echo Please run this script as Administrator if needed.
)

echo.
echo [4/5] Clearing cache...
if exist "writable\cache\*" del /Q "writable\cache\*" 2>nul

echo.
echo [5/5] Checking database configuration...
if not exist ".env" (
    echo WARNING: .env file not found!
    echo Please copy .env.example to .env and configure your database settings.
)

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Create a database named 'census_db' in MySQL/phpMyAdmin
echo 2. Import 'census_db.sql' to the database
echo 3. Check database settings in .env file
echo 4. Run: php spark serve
echo 5. Open browser: http://localhost:8080
echo.
echo For detailed instructions, read INSTALL.md
echo ========================================
pause

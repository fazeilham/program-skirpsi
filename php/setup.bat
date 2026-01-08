@echo off
REM Biyai Finance Tracker - Setup Script for Windows

echo.
echo ================================
echo Biyai Finance Tracker - Setup
echo ================================
echo.

REM Check if data folder exists
if not exist "data" (
    echo Creating data folder...
    mkdir data
    echo OK - Data folder created
) else (
    echo OK - Data folder exists
)

REM Create transaksi.json if not exists
if not exist "data\transaksi.json" (
    echo Creating transaksi.json...
    (
        echo.
    ) > data\transaksi.json
    (
        echo [] 
    ) > data\transaksi.json
    echo OK - Data file created
) else (
    echo OK - Data file exists
)

echo.
echo ================================
echo Setup Complete!
echo ================================
echo.
echo Start the application with:
echo.
echo 1. Using PHP built-in server (requires PHP in PATH):
echo    php -S localhost:8000
echo.
echo 2. Or use XAMPP/WAMP/LAMP and access:
echo    http://localhost/php/login.php
echo.
echo Demo Account:
echo    Email: admin@example.com
echo    Password: admin123
echo.
echo ================================
echo.
pause

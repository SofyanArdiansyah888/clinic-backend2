@echo off
echo ========================================
echo    Clinic Backend 2 Installation
echo ========================================
echo.

echo Installing Composer dependencies...
composer install

echo.
echo Copying environment file...
copy .env.example .env

echo.
echo Please edit the .env file with your database credentials
echo Then run: php artisan key:generate
echo And: php artisan migrate
echo.

pause

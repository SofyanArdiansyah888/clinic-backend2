@echo off
echo ========================================
echo    Clinic Backend Database Setup
echo ========================================
echo.

echo Step 1: Clearing caches...
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

echo.
echo Step 2: Running migrations...
php artisan migrate --force

echo.
echo Step 3: Creating storage link (if needed)...
php artisan storage:link

echo.
echo Database setup completed!
echo.
echo Your application should now be ready to use.
echo.

pause

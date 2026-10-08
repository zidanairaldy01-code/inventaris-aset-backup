@echo off
echo ==========================================
echo  Generate Laravel APP_KEY for Railway
echo ==========================================
echo.
php artisan key:generate --show
echo.
echo Copy output di atas ke Railway Environment Variable: APP_KEY
echo.
pause

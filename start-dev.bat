@echo off
title Laravel Dev Server
echo Starting PHP server...
start "PHP Server" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul
echo Starting NPM dev server...
start "NPM Dev" cmd /k "npm run dev"
timeout /t 2 /nobreak >nul
echo Opening browser...
start http://127.0.0.1:8000
echo All servers started and browser opened! Close this window to stop.
pause

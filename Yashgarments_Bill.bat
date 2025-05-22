@echo off
REM Start Apache server
start /min "XAMPP Server" "C:\xampp\xampp_start.exe"

REM Wait for the server to start
timeout /t 5 /nobreak >nul

REM Open the PHP application in default web browser
start http://localhost/Online_Invoicing/index.php
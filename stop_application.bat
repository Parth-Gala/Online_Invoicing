@echo off
REM Stop apache server
echo Stopping Apache server...
taskkill /IM httpd.exe /F

REM Wait to ensure Apache has stopped
timeout /t 3 /nobreak >nul

REM Stop MySQL server
echo Stopping MySQL server...
taskkill /IM mysqld.exe /F

REM Wait to ensure MySQL has stopped
timeout /t 3 /nobreak >nul

echo Checking if ports are freed...
netstat -ano | findstr "80"
netstat -ano | findstr "3306"

echo Apache and MySQL servers stopped. Closing the App.

REM Display copyright notice
echo.
echo Copyright (c)2024 @ParthGala. All rights reserved.

REM Wait for 5 secs
timeout /t 4 /nobreak >nul

REM Close the command prompt window
exit
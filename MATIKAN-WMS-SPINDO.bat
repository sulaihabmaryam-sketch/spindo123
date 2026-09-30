@echo off
title MATIKAN SERVER WMS SPINDO
color 0C
cls

echo ======================================================================
echo           MENGHENTIKAN SERVER WMS SPINDO
echo ======================================================================
echo.
echo Menghentikan proses server PHP di background...
taskkill /F /IM php.exe >nul 2>&1

echo.
echo ======================================================================
echo   STATUS : SERVER TELAH DIMATIKAN / NONAKTIF
echo ======================================================================
echo.
echo Server WMS Spindo sudah berhenti.
ping -n 3 127.0.0.1 > nul
exit /b 0

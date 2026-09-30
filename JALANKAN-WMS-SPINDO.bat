@echo off
title SISTEM WMS & LAYOUT GUDANG PIPA SPINDO
color 0A
cls

echo ======================================================================
echo           SISTEM WMS & LAYOUT GUDANG PIPA - PT SPINDO
echo ======================================================================
echo.

:: Otomatis mendeteksi PHP jika diletakkan di C:\php atau di dalam folder project (prioritaskan PHP 8.5)
set "PATH=C:\php\php-8.5.4-Win32-vs17-x64;C:\php;%~dp0php;%~dp0..\php;%PATH%"

echo  [1/2] Menyiapkan server WMS Spindo...
cd /d "%~dp0"

:: Mendeteksi IP Lokal Komputer secara otomatis
set "LOCAL_IP=127.0.0.1"
for /f "delims=" %%i in ('powershell -NoProfile -Command "(Get-NetRoute -DestinationPrefix 0.0.0.0/0 -ErrorAction SilentlyContinue | Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty IPAddress -First 1)"') do (
    if not "%%i"=="" set "LOCAL_IP=%%i"
)

echo.
echo ======================================================================
echo   STATUS : SERVER AKTIF & SIAP DIGUNAKAN!
echo ======================================================================
echo.
echo   * Alamat Akses Komputer Ini & Komputer Gudang Lain:
echo     http://%LOCAL_IP%:8000
echo.
echo ======================================================================
echo   PENTING: JANGAN TUTUP JENDELA INI SELAMA APLIKASI DIGUNAKAN!
echo ======================================================================
echo.

:: Membuka browser otomatis menggunakan IP lokal aktif
start http://%LOCAL_IP%:8000

:: Menjalankan server WMS Spindo (dapat diakses seluruh jaringan lokal)
php -S 0.0.0.0:8000 -t public server.php
pause

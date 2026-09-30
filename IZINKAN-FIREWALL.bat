@echo off
title IZINKAN AKSES JARINGAN WMS SPINDO (PORT 8000)
color 0A
cls

echo ======================================================================
echo     MENGIZINKAN AKSES JARINGAN LOKAL UNTUK WMS SPINDO (PORT 8000)
echo ======================================================================
echo.

:: Cek Hak Administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo [PERINGATAN] File ini membutuhkan Hak Akses Administrator!
    echo.
    echo Silakan tutup jendela ini, lalu:
    echo 1. Klik KANAN pada file: IZINKAN-FIREWALL.bat
    echo 2. Pilih "Run as administrator"
    echo.
    pause
    exit /b 1
)

echo Membuka Port 8000 pada Windows Firewall...
netsh advfirewall firewall add rule name="Laravel WMS Spindo (Port 8000)" dir=in action=allow protocol=TCP localport=8000 >nul

echo.
echo ======================================================================
echo   SUKSES! Port 8000 telah diizinkan pada Windows Firewall.
echo   Komputer / HP lain di jaringan lokal sekarang dapat mengakses:
echo   http://192.168.8.8:8000
echo ======================================================================
echo.
pause
exit /b 0

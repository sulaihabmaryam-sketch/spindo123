@echo off
title SISTEM WMS DAN DENAH GUDANG SPINDO
color 0A
cls

:: Ambil direktori project tanpa trailing backslash
set "PROJ_DIR=%~dp0"
if "%PROJ_DIR:~-1%"=="\" set "PROJ_DIR=%PROJ_DIR:~0,-1%"
cd /d "%PROJ_DIR%"

:: 1. Deteksi IP aktif komputer (sesuai ipconfig / jaringan aktif)
set "LOCAL_IP=127.0.0.1"
for /f "delims=" %%i in ('powershell -NoProfile -Command "(Get-NetRoute -DestinationPrefix 0.0.0.0/0 -ErrorAction SilentlyContinue | Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty IPAddress -First 1)"') do (
    if not "%%i"=="" set "LOCAL_IP=%%i"
)

:: 2. Cari lokasi PHP
set "PHP_EXE="
if exist "C:\php\php-8.5.4-Win32-vs17-x64\php.exe" set "PHP_EXE=C:\php\php-8.5.4-Win32-vs17-x64\php.exe"
if not defined PHP_EXE if exist "C:\php\php.exe" set "PHP_EXE=C:\php\php.exe"
if not defined PHP_EXE if exist "%PROJ_DIR%\php\php.exe" set "PHP_EXE=%PROJ_DIR%\php\php.exe"
if not defined PHP_EXE if exist "D:\php\php-8.5.4-Win32-vs17-x64\php.exe" set "PHP_EXE=D:\php\php-8.5.4-Win32-vs17-x64\php.exe"
if not defined PHP_EXE if exist "D:\php\php.exe" set "PHP_EXE=D:\php\php.exe"
if not defined PHP_EXE set "PHP_EXE=php.exe"

:: 3. Cek apakah server port 8000 sudah berjalan
netstat -ano | findstr :8000 | findstr LISTENING > nul
if %ERRORLEVEL% equ 0 (
    echo ======================================================================
    echo   SERVER WMS SPINDO SUDAH AKTIF!
    echo ======================================================================
    echo.
    echo   Membuka alamat IP: http://%LOCAL_IP%:8000
    start http://%LOCAL_IP%:8000
    ping -n 3 127.0.0.1 > nul
    exit /b 0
)

echo ======================================================================
echo           SISTEM WMS DAN DENAH GUDANG PIPA - PT SPINDO
echo ======================================================================
echo.
echo   * Status Server : AKTIF DAN SIAP DIGUNAKAN
echo   * Alamat IP WMS : http://%LOCAL_IP%:8000
echo.
echo ======================================================================
echo   PENTING: JANGAN TUTUP JENDELA INI SELAMA APLIKASI DIGUNAKAN!
echo   (Jika selesai bekerja, klik shortcut 'Matikan Server' di Desktop)
echo ======================================================================
echo.

:: 4. Buka browser otomatis menggunakan IP aktif (IPConfig)
echo Membuka browser ke http://%LOCAL_IP%:8000 ...
start http://%LOCAL_IP%:8000

:: 5. Jalankan server PHP (jika server dimatikan, jendela ini otomatis menutup)
"%PHP_EXE%" -S 0.0.0.0:8000 -t "%PROJ_DIR%\public" "%PROJ_DIR%\server.php"

exit /b 0

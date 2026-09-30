@echo off
title PASANG SHORTCUT DESKTOP WMS SPINDO
color 0A
cls

echo ======================================================================
echo       MEMBUAT SHORTCUT APLIKASI WMS SPINDO DI DESKTOP
echo ======================================================================
echo.

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ws = New-Object -ComObject WScript.Shell; " ^
  "$desktop = [Environment]::GetFolderPath('Desktop'); " ^
  "if (-not $desktop) { $desktop = $ws.SpecialFolders('Desktop') }; " ^
  "$oldUrl = Join-Path $desktop 'WMS Denah dan Stok Spindo.url'; " ^
  "if (Test-Path $oldUrl) { Remove-Item -Force $oldUrl }; " ^
  "$shortcutApp = Join-Path $desktop 'WMS Denah dan Stok Spindo.lnk'; " ^
  "$s1 = $ws.CreateShortcut($shortcutApp); " ^
  "$s1.TargetPath = '%~dp0BUKA-WMS-SPINDO.bat'; " ^
  "$s1.WorkingDirectory = '%~dp0'; " ^
  "$s1.Description = 'Buka Aplikasi WMS Denah dan Stok Spindo (Auto Start Server)'; " ^
  "$s1.IconLocation = 'shell32.dll,14'; " ^
  "$s1.Save(); " ^
  "$shortcutStop = Join-Path $desktop 'Matikan Server WMS Spindo.lnk'; " ^
  "$s2 = $ws.CreateShortcut($shortcutStop); " ^
  "$s2.TargetPath = '%~dp0MATIKAN-WMS-SPINDO.bat'; " ^
  "$s2.WorkingDirectory = '%~dp0'; " ^
  "$s2.Description = 'Matikan Server WMS Spindo'; " ^
  "$s2.IconLocation = 'shell32.dll,131'; " ^
  "$s2.Save(); " ^
  "Write-Host '   -> Folder Desktop:' $desktop; " ^
  "Write-Host '   -> Shortcut Buka WMS: SUKSES'; " ^
  "Write-Host '   -> Shortcut Matikan WMS: SUKSES'"

echo.
echo ======================================================================
echo   SUKSES! 2 SHORTCUT TELAH DIBUAT DI DESKTOP:
echo ======================================================================
echo.
echo   1. [WMS Denah dan Stok Spindo]
echo      -> Klik 2x: Server otomatis aktif + browser langsung terbuka!
echo.
echo   2. [Matikan Server WMS Spindo]
echo      -> Klik 2x: Mematikan server jika sudah selesai digunakan.
echo.
pause

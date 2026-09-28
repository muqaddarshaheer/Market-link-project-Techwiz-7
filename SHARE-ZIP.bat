@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"

echo.
echo === MarketLink — READY ZIP for sharing ===
echo This ZIP includes vendor/ so friends can run without Composer.
echo.

set "STAGE=%TEMP%\MarketLink-READY-stage"

for /f "usebackq delims=" %%D in (`powershell -NoProfile -Command "[Environment]::GetFolderPath('Desktop')"`) do set "DESKTOP=%%D"
if not defined DESKTOP set "DESKTOP=%USERPROFILE%\Desktop"
if not exist "%DESKTOP%" set "DESKTOP=%CD%"
set "OUT=%DESKTOP%\MarketLink-READY.zip"

if exist "%STAGE%" rmdir /s /q "%STAGE%"
mkdir "%STAGE%"

echo Copying project (with vendor)...
robocopy "%CD%" "%STAGE%" /E /NFL /NDL /NJH /NJS /nc /ns /np /XD .git node_modules .idea .vscode .fleet .zed /XF .env composer.phar composer-setup.php "*.zip"

if exist "%STAGE%\storage\framework\install.lock" del /f /q "%STAGE%\storage\framework\install.lock"
if exist "%STAGE%\storage\logs" del /f /q "%STAGE%\storage\logs\*.log" 2>nul
if exist "%STAGE%\storage\framework\views" del /f /q "%STAGE%\storage\framework\views\*.php" 2>nul
if exist "%STAGE%\storage\framework\sessions" del /f /q "%STAGE%\storage\framework\sessions\*" 2>nul
if exist "%STAGE%\bootstrap\cache" del /f /q "%STAGE%\bootstrap\cache\*.php" 2>nul

if not exist "%STAGE%\vendor\autoload.php" (
  echo.
  echo ERROR: vendor\ folder missing on THIS PC.
  echo Run setup.bat once here, then run SHARE-ZIP.bat again.
  pause
  exit /b 1
)

echo Writing HOW-TO-RUN.txt ...
(
  echo MarketLink — READY ZIP
  echo.
  echo 1^) Unzip into C:\xampp\htdocs\
  echo 2^) XAMPP: start Apache + MySQL
  echo 3^) Open browser: http://localhost/YOUR-FOLDER-NAME/
  echo.
  echo Pehli dafa site khulte hi database + demo data auto ban jayega.
  echo.
  echo Demo login:
  echo   farmer@marketlink.com / Farmer@123
  echo   customer@marketlink.com / Customer@123
  echo   admin@marketlink.com / Admin@123
) > "%STAGE%\HOW-TO-RUN.txt"

if exist "%OUT%" del /f /q "%OUT%"

echo Zipping to:
echo   %OUT%
echo (may take 1–2 min^)...
powershell -NoProfile -Command "Compress-Archive -Path '%STAGE%\*' -DestinationPath '%OUT%' -CompressionLevel Optimal -Force"
if errorlevel 1 (
  echo Desktop zip failed — saving inside project folder...
  set "OUT=%CD%\MarketLink-READY.zip"
  if exist "!OUT!" del /f /q "!OUT!"
  powershell -NoProfile -Command "Compress-Archive -Path '%STAGE%\*' -DestinationPath '%CD%\MarketLink-READY.zip' -CompressionLevel Optimal -Force"
  if errorlevel 1 (
    echo Compress failed.
    pause
    exit /b 1
  )
)

rmdir /s /q "%STAGE%" 2>nul

echo.
echo DONE.
echo Send this file to testers:
echo   %OUT%
echo.
echo Unzip → htdocs → Apache+MySQL ON → open localhost/folder/
echo.
pause

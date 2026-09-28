@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo.
echo === MarketLink — READY ZIP for sharing ===
echo This ZIP includes vendor/ so friends can run without Composer.
echo.

set STAGE=%TEMP%\MarketLink-READY-stage
set OUT=%USERPROFILE%\Desktop\MarketLink-READY.zip

if exist "%STAGE%" rmdir /s /q "%STAGE%"
mkdir "%STAGE%"

echo Copying project (with vendor)...
robocopy "%CD%" "%STAGE%" /E /NFL /NDL /NJH /NJS /nc /ns /np ^
  /XD .git node_modules .idea .vscode .fleet .zed ^
  /XF .env composer.phar composer-setup.php "*.zip"

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
  echo 3^) Open browser: http://localhost/MarketLink-READY/
  echo    ^(folder name jo unzip ke baad ho^)
  echo.
  echo Pehli dafa site khulte hi database + demo data auto ban jayega.
  echo.
  echo Demo login:
  echo   farmer@marketlink.com / Farmer@123
  echo   customer@marketlink.com / Customer@123
  echo   admin@marketlink.com / Admin@123
) > "%STAGE%\HOW-TO-RUN.txt"

if exist "%OUT%" del /f /q "%OUT%"

echo Zipping to Desktop (may take 1–2 min^)...
powershell -NoProfile -Command "Compress-Archive -Path '%STAGE%\*' -DestinationPath '%OUT%' -CompressionLevel Optimal -Force"
if errorlevel 1 (
  echo Compress failed.
  pause
  exit /b 1
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

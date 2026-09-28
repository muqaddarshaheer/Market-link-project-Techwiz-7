@echo off
setlocal EnableExtensions EnableDelayedExpansion
cd /d "%~dp0"

echo.
echo === MarketLink — one-click setup (ZIP / XAMPP) ===
echo.

set "PHP_BIN="

REM Prefer any PHP 8.2+ on PATH (Winget / newer install) over old XAMPP 8.0
for /f "delims=" %%P in ('where php 2^>nul') do (
  if not defined PHP_BIN (
    "%%P" -r "exit(PHP_VERSION_ID>=80200?0:1);" 2>nul
    if not errorlevel 1 set "PHP_BIN=%%P"
  )
)

if not defined PHP_BIN if exist "C:\xampp\php\php.exe" (
  "C:\xampp\php\php.exe" -r "exit(PHP_VERSION_ID>=80200?0:1);" 2>nul
  if not errorlevel 1 set "PHP_BIN=C:\xampp\php\php.exe"
)
if not defined PHP_BIN if exist "C:\XAMPP\php\php.exe" (
  "C:\XAMPP\php\php.exe" -r "exit(PHP_VERSION_ID>=80200?0:1);" 2>nul
  if not errorlevel 1 set "PHP_BIN=C:\XAMPP\php\php.exe"
)

if not defined PHP_BIN (
  echo ERROR: PHP 8.2+ not found.
  echo MarketLink needs PHP 8.2 or 8.3.
  echo Install XAMPP PHP 8.2+ from https://www.apachefriends.org
  echo Or install PHP 8.3 and add it to PATH, then run setup.bat again.
  pause
  exit /b 1
)

echo Using PHP: %PHP_BIN%
"%PHP_BIN%" -v
echo Make sure XAMPP MySQL is RUNNING, then wait...
echo.

"%PHP_BIN%" -d max_execution_time=0 setup-cli.php
set EXITCODE=%ERRORLEVEL%

echo.
if not "%EXITCODE%"=="0" (
  echo Setup FAILED. Fix the error above and run setup.bat again.
  pause
  exit /b %EXITCODE%
)

for %%I in ("%CD%") do set FOLDER=%%~nxI

echo.
echo ========================================
echo  SUCCESS — site is ready
echo ========================================
echo.
echo Open in browser:
echo   http://localhost/%FOLDER%/
echo.
echo Demo logins:
echo   farmer@marketlink.com   / Farmer@123
echo   customer@marketlink.com / Customer@123
echo   admin@marketlink.com    / Admin@123
echo.
pause

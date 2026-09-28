@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo.
echo === MarketLink — one-click setup (ZIP / XAMPP) ===
echo.

set PHP_BIN=
if exist "C:\xampp\php\php.exe" set PHP_BIN=C:\xampp\php\php.exe
if exist "C:\XAMPP\php\php.exe" set PHP_BIN=C:\XAMPP\php\php.exe
if "%PHP_BIN%"=="" (
  where php >nul 2>nul && set PHP_BIN=php
)
if "%PHP_BIN%"=="" (
  echo ERROR: PHP not found. Install XAMPP first.
  echo Expected: C:\xampp\php\php.exe
  pause
  exit /b 1
)

echo Using PHP: %PHP_BIN%
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

@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo.
echo === MarketLink setup (XAMPP / GitHub ZIP) ===
echo.

set PHP_BIN=
if exist "C:\xampp\php\php.exe" set PHP_BIN=C:\xampp\php\php.exe
if exist "C:\XAMPP\php\php.exe" set PHP_BIN=C:\XAMPP\php\php.exe
if "%PHP_BIN%"=="" (
  where php >nul 2>nul && set PHP_BIN=php
)
if "%PHP_BIN%"=="" (
  echo ERROR: PHP not found. Install XAMPP and start again.
  echo Expected: C:\xampp\php\php.exe
  pause
  exit /b 1
)

echo Using PHP: %PHP_BIN%
echo.

if not exist ".env" (
  if exist ".env.example" (
    copy /Y ".env.example" ".env" >nul
    echo Created .env
  )
)

if not exist "vendor\autoload.php" (
  if not exist "composer.phar" (
    echo Downloading Composer...
    "%PHP_BIN%" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    if errorlevel 1 (
      echo Could not download Composer. Check internet.
      pause
      exit /b 1
    )
    "%PHP_BIN%" composer-setup.php
    del composer-setup.php >nul 2>nul
  )
  echo Installing vendor packages...
  "%PHP_BIN%" composer.phar install --no-interaction --prefer-dist
  if errorlevel 1 (
    echo composer install failed.
    pause
    exit /b 1
  )
) else (
  echo vendor\ already exists
)

echo Generating app key...
"%PHP_BIN%" artisan key:generate --force

if not exist "storage\framework" mkdir "storage\framework"
if not exist "storage\logs" mkdir "storage\logs"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"
echo. > "storage\framework\install.lock"

echo.
echo DONE.
echo 1^) XAMPP: start Apache + MySQL
echo 2^) phpMyAdmin: create database "marketlink"
echo 3^) Run:  "%PHP_BIN%" artisan migrate --seed
echo 4^) Open: http://localhost/%CD:~3%\
echo    or:    http://localhost/Market-link-project-Techwiz-7-main/
echo.
pause

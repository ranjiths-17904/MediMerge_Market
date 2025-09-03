@echo off
echo ========================================
echo    MediMerge Website Deployment
echo ========================================
echo.

echo Checking XAMPP installation...
if not exist "C:\xampp\xampp-control.exe" (
    echo ERROR: XAMPP not found in C:\xampp
    echo Please install XAMPP first
    pause
    exit /b 1
)

echo Starting XAMPP services...
cd /d "C:\xampp"
start /min xampp-control.exe

echo.
echo Waiting for XAMPP to start...
timeout /t 5 /nobreak >nul

echo.
echo ========================================
echo Deployment Steps:
echo ========================================
echo.
echo 1. In XAMPP Control Panel, start:
echo    - Apache (Port 80)
echo    - MySQL (Port 3306)
echo.
echo 2. Open your browser and go to:
echo    http://localhost/MediMerge-Market/mini%%20world%%20project/setup_database.php
echo.
echo 3. After database setup, visit:
echo    http://localhost/MediMerge-Market/mini%%20world%%20project/medico.html
echo.
echo 4. Admin login:
echo    Username: TheAdmin
echo    Password: Admin@MM
echo.
echo ========================================
echo.

echo Opening deployment guide...
start DEPLOYMENT.md

echo.
echo Press any key to open XAMPP Control Panel...
pause >nul

echo Opening XAMPP Control Panel...
start "C:\xampp\xampp-control.exe"

echo.
echo Deployment script completed!
echo Please follow the steps above to complete setup.
pause

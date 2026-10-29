@echo off
cls
echo ==============================================================
echo  KELOMPOK 3 - COURSE SERVICE (PORT 8103 - MODE LAN)
echo ==============================================================
echo  IP Server LAN             : 10.194.169.115
echo  Port Server ini           : 8103 (bind ke 0.0.0.0:8103)
echo.
echo  URL API Course:
echo    - http://10.194.169.115:8103/api/courses
echo    - http://10.194.169.115:8103/api/health
echo    - http://10.194.169.115:8103/index.php
echo.
echo  Entri services.json untuk Gateway:
echo    "course": "http://10.194.169.115:8103"
echo ==============================================================
echo  Server sedang berjalan... JANGAN TUTUP JENDELA INI!
echo ==============================================================
echo.
cd /d "%~dp0course-service"
"C:\xampp\php\php.exe" -S 0.0.0.0:8103 router.php
pause

@echo off
cls
echo ==============================================================
echo       INFORMASI KONEKSI API LAN (LOCAL AREA NETWORK)
echo ==============================================================
echo.
echo IP Komputer saat ini:
ipconfig | findstr /i "IPv4"
echo.
echo ==============================================================
echo Petunjuk:
echo 1. Pastikan laptop ini dan laptop Gateway/Kelompok lain terhubung
echo    ke Wi-Fi atau hotspot yang SAMA.
echo 2. Gunakan IP Wi-Fi di atas untuk registrasi ke Kelompok 1:
echo    {"course": "http://[IP-WIFI-ANDA]:8103"}
echo 3. Jalankan server dengan klik: jalankan-course-8103-lan.bat
echo ==============================================================
echo.
pause


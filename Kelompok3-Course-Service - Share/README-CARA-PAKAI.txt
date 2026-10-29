KELOMPOK 3 - COURSE SERVICE (PAKET BENAR)
============================================

ISI PAKET (jangan diratakan, biarkan strukturnya seperti ini):
  course-service/index.php, db.php, .htaccess, router.php
  database/course_db.sql
  postman/Kelompok3-Course-Service-v2-Gateway.postman_collection.json
  jalankan-course-8103.bat        (satu laptop, 127.0.0.1:8103)
  jalankan-course-8103-lan.bat    (lintas laptop, 0.0.0.0:8103)
  jalankan-course-8203.bat        (uji ubah port, hanya untuk tes)
  registrasi-course-SATU-LAPTOP.json
  registrasi-course-LINTAS-LAPTOP.json
  Panduan_Demo_Kelompok3_v2_Gateway.docx
  README-Gateway-Registry.txt

ENDPOINT COURSE (tidak berubah):
  GET  /api/health
  GET  /api/courses
  GET  /api/courses/{id}
  POST /api/courses   {"code":"PTIK303","name":"Basis Data","credits":3}
  DELETE /api/courses/{id}
  Database: course_db, tabel courses (code/name/credits)
  Data awal: PTIK301 Sistem Terdistribusi (3), PTIK302 Pemrograman Web (3)

CARA PAKAI 1 - UJI MANDIRI XAMPP:
  1. Salin HANYA index.php, db.php, .htaccess dari course-service/
     ke C:\xampp\htdocs\Kelompok3-Course-Service\
  2. Start Apache + MySQL di XAMPP, import database/course_db.sql
     lewat phpMyAdmin.
  3. Buka http://localhost/Kelompok3-Course-Service/api/courses
     dan /api/health di browser/Postman.

CARA PAKAI 2 - SATU LAPTOP (Gateway juga di laptop ini):
  1. Klik dua kali jalankan-course-8103.bat (jangan ditutup selama demo).
  2. Kirim ke Kelompok 1 isi registrasi-course-SATU-LAPTOP.json:
     {"course":"http://127.0.0.1:8103"}

CARA PAKAI 3 - LINTAS LAPTOP (Gateway di laptop Kelompok 1):
  1. Tutup dulu jendela 8103 yang lama (port tidak bisa dipakai dua kali).
  2. Klik dua kali jalankan-course-8103-lan.bat (0.0.0.0:8103),
     izinkan lewat Windows Firewall kalau diminta.
  3. Kedua laptop harus di Wi-Fi yang SAMA.
  4. Kirim ke Kelompok 1 isi registrasi-course-LINTAS-LAPTOP.json:
     {"course":"http://10.194.169.115:8103"}
     Alamat IP yang digunakan: 10.194.169.115.

CARA PAKAI 4 - GATEWAY 10.194.169.115 DENGAN PORT 4001 & PORT 8103:
  Kondisi demo:
  - Laptop Gateway (Kelompok 1) : 10.194.169.115 (Port 4001 / Port 8103)
  - Course Service              : 10.194.169.115 (Port 8103 / Port 4001)

  Langkah menjalankan:
  A. Buka XAMPP Control Panel, Start MySQL (database course_db).
  B. Jalankan Server Course (pilih salah satu):
     - DUAL PORT (Recommended): Klik jalankan-course-dual-4001-dan-8103.bat
       (Membuka port 4001 dan 8103 sekaligus di laptop ini).
     - PORT 8103 saja: Klik jalankan-course-8103-lan.bat
     - PORT 4001 saja: Klik jalankan-course-4001.bat
  C. Kirim ke Kelompok 1 untuk services.json mereka:
     - Jika pakai port 8103: {"course":"http://10.194.169.115:8103"}
     - Jika pakai port 4001: {"course":"http://10.194.169.115:4001"}
  D. Di Postman, gunakan Environment:
     - postman/Gateway-10.194.169.115-Port4001.postman_environment.json
       (jika Gateway di port 4001)
     - postman/Gateway-10.194.169.115-Port8103.postman_environment.json
       (jika Gateway di port 8103)

YANG TIDAK ADA DI PAKET INI (sengaja):
  Folder gateway-sementara TIDAK dimasukkan. Itu hanya alat latihan
  di laptop sendiri, BUKAN Gateway asli Kelompok 1, dan jangan
  dikirim seolah-olah itu kerjaan Kelompok 1.
  Postman API key TIDAK diperlukan - Course ini tidak pakai API key.

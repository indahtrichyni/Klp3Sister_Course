<?php
// Router untuk PHP built-in server (mengikat 0.0.0.0 agar dapat diakses lintas LAN).
// Cara pakai:
//   C:\xampp\php\php.exe -S 0.0.0.0:8103 router.php
// Atau klik jalankan-course-8103-lan.bat
// Semua request (termasuk /api/courses dan /api/health) diteruskan ke index.php

require __DIR__ . "/index.php";

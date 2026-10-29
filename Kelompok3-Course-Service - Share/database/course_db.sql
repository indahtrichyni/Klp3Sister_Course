CREATE DATABASE IF NOT EXISTS course_db;
USE course_db;

CREATE TABLE IF NOT EXISTS courses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    credits TINYINT UNSIGNED NOT NULL
);

INSERT IGNORE INTO courses (code, name, credits)
VALUES
('PTIK301', 'Sistem Terdistribusi', 3),
('PTIK302', 'Pemrograman Web', 3);

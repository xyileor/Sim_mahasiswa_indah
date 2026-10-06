<?php
declare(strict_types=1);

/*
 * Koneksi database.
 * Sesuaikan username/password MySQL dengan XAMPP/Laragon/server Anda.
 */
// LOKAL (XAMPP): host 127.0.0.1, user root, password kosong.
// INFINITYFREE: isi dengan data dari menu MySQL Databases (lihat README.md).
const DB_HOST = '127.0.0.1';  // contoh hosting: sql123.infinityfree.com
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'universitas_semantik';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi db.php.');
}

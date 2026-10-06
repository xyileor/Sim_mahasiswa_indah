# Universitas Semantik — CRUD + RBAC (Tema Pink)

Modul CRUD lengkap: Universitas, Fakultas, Program Studi, Mahasiswa, Pengguna. Audit Log bersifat baca saja.
Hak akses: `admin` (semua), `operator` (kelola mahasiswa), `dosen`/`mahasiswa`/`publik` (hanya lihat).

Login awal: `admin` / `password` dan `operator` / `password`. **Segera ganti password setelah login pertama.**

## Menjalankan lokal (XAMPP)
1. Salin folder ke `htdocs/universitas`.
2. Buka phpMyAdmin, impor `dump_universitas_semantik.sql`.
3. Buka `http://localhost/universitas/`.

## Tutorial upload ke InfinityFree

### 1. Buat akun dan hosting
1. Daftar di infinityfree.com, lalu verifikasi email.
2. Klik **Create Account**, pilih subdomain gratis (mis. `kampusku.rf.gd`) atau domain sendiri.
3. Tunggu status akun **Active**, lalu buka **Control Panel**.

### 2. Buat database
1. Di Control Panel pilih **MySQL Databases**.
2. Isi nama database (mis. `universitas`), klik **Create Database**.
3. Catat 4 data: **MySQL Hostname** (mis. `sql123.infinityfree.com`), **Database Name** (mis. `if0_12345678_universitas`), **Username** (mis. `if0_12345678`), dan **Password** (password akun hosting, ada di tab **Account** pada panel client).

### 3. Impor database
1. Di daftar database, klik **phpMyAdmin**, lalu login.
2. Pilih database `if0_..._universitas` di panel kiri.
3. Tab **Import**, pilih file **`dump_infinityfree.sql`** (bukan file dump yang lain; file ini sudah tanpa `CREATE DATABASE`/`USE` yang tidak diizinkan di hosting gratis), lalu klik **Import**.

### 4. Sesuaikan koneksi
Edit `db.php` sebelum upload:
```php
const DB_HOST = 'sql123.infinityfree.com';
const DB_USER = 'if0_12345678';
const DB_PASS = 'password_anda';
const DB_NAME = 'if0_12345678_universitas';
```

### 5. Upload file
1. Di Control Panel pilih **Online File Manager** (atau pakai FTP dengan FileZilla; data FTP ada di panel client).
2. Masuk ke folder **`htdocs`** (hapus `index2.html` bawaan).
3. Upload **isi** folder aplikasi (bukan folder pembungkusnya) sehingga `index.html`, `login.php`, `crud.php`, dan folder `assets/` serta `partials/` langsung berada di `htdocs`.
4. Folder `dump_*.sql` sebaiknya **dihapus dari server** setelah impor selesai (berisi data akun).

### 6. Uji
Buka `https://kampusku.rf.gd/`, login, lalu coba tambah, ubah, dan hapus data mahasiswa.

## Masalah umum
- **Koneksi database gagal**: cek ulang 4 data di `db.php`; hostname bukan `localhost`.
- **Halaman kosong / error 500**: pastikan PHP 8.0 atau lebih baru (menu **Change PHP Version** atau **Software**) karena kode memakai fitur PHP 8.
- **Tabel tidak ada**: impor belum berhasil; ulangi langkah 3 dan lihat pesan error phpMyAdmin.
- **Perubahan tidak muncul**: cache browser, tekan Ctrl+F5.
- Pada akun gratis, awal aktif bisa butuh beberapa menit sampai domain dapat dibuka.

## Daftar halaman
- `index.html` halaman depan (menu scroll, login), `publik.php` data agregat publik tanpa login
- `dashboard.php`, `mahasiswa.php`, `prodi.php`, `fakultas.php`, `universitas.php`, `pengguna.php`, `audit.php` (butuh login)
- `rdf.php` ekspor data RDF (Turtle), `sparql.php` kueri contoh SPARQL
- `cek.php` diagnostik sementara. **Hapus dari server setelah dipakai.**

Catatan: InfinityFree tidak mengizinkan `CREATE VIEW`, jadi dashboard memakai query biasa.

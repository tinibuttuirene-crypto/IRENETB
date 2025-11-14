<!-- PROJECT BADGES -->
<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-blue?logo=php" alt="PHP Version" />
  <img src="https://img.shields.io/badge/MySQL-Compatible-orange?logo=mysql" alt="MySQL" />
  <img src="https://img.shields.io/badge/JWT-Authentication-green?logo=jsonwebtokens" alt="JWT" />
  <img src="https://img.shields.io/badge/Postman-Tested-success?logo=postman" alt="Postman" />
</p>

<h1 align="center">🧩 API PHP NATIVE IRENET — Praktikum Pemrograman Jaringan</h1>

<p align="center">
  RESTful API menggunakan <b>PHP Native tanpa framework</b>.<br/>
  Dibuat untuk memenuhi tugas <b>Modul Praktikum Pemrograman Jaringan</b>.
</p>

---

## 📘 Deskripsi Proyek

API ini dibangun dari nol menggunakan PHP Native (tanpa framework) dengan konsep MVC sederhana.  
Mendukung fitur:
- Routing manual
- Autentikasi JWT (login dan middleware)
- CRUD User dengan PDO
- Upload file aman (gambar/pdf)
- Rate limiting per IP/token
- Pagination dan validasi input
- CORS Middleware
- Dokumentasi OpenAPI-lite & Postman Collection

---

## ⚙ Prasyarat

| Komponen | Versi/Deskripsi |
|-----------|----------------|
| PHP | ≥ 8.0 |
| Database | MySQL / MariaDB |
| Server Lokal | Laragon / XAMPP / PHP built-in |
| Postman | Untuk testing API |
| Composer (opsional) | Untuk autoload PSR-4 |

---

## 🚀 Cara Menjalankan

### 1️⃣ Persiapan Proyek
Salin folder ke direktori server kamu:
```
C:\laragon\www\api-php-native-irenet
```

### 2️⃣ Import Database
Buat database apiphp, lalu jalankan SQL berikut di phpMyAdmin:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Tambahkan user admin:

```sql
INSERT INTO users (name,email,password_hash,role)
VALUES (
  'Admin',
  'irene@example.com',
  '$2y$10$brj0BiVc.L3WDXniwIOLmO6i',
  'admin'
);
```

(Ganti `$2y$10$brj0BiVc.L3WDXniwIOLmO6i` dengan hasil `password_hash('Cinta_sejati_takkan_pernah_pudar_oleh_waktu', PASSWORD_DEFAULT)`.)

### 3️⃣ Jalankan Server
Jalankan server lokal:

```bash
php -S localhost:8000 -t public
```

Lalu akses:

```
http://localhost:8000/api/v1/health
```

### 4️⃣ Akun Default

```
Email: irene@example.com
Password: Cinta_sejati_takkan_pernah_pudar_oleh_waktu
```

## 📂 Struktur Folder

```
API-PHP-NATIVE-IRENET/
│
├── config/
│   └── env.php
│
├── logs/
│  
│
├── public/
│   ├── .htaccess
│   └── index.php
│
├── src/
│   ├── Config/
│   │   └── Database.php
│   │
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── BaseController.php
│   │   ├── HealthController.php
│   │   ├── UploadController.php
│   │   ├── UserController.php
│   │   └── VersionController.php
│   │
│   ├── Helpers/
│   │   ├── Jwt.php
│   │   ├── JwtHelper.php
│   │   ├── RateLimiter.php
│   │   └── Response.php
│   │
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php
│   │   └── CorsMiddleware.php
│   │
│   ├── Repositories/
│   │   └── UserRepository.php
│   │
│   └── Validator/
│       └── Validator.php
│
├── uploads/
│   ├── .htaccess
│   ├── 72f52d9547b733a7.png
│   ├── 897608739cb5c365.png
│   ├── ac5d7e0ff7ce9e81.pdf
│   └── fa0057c94d0c69db.txt
│
├── API PHP Native.postman_collection.json
├── api-contract.php
├── CHANGELOG.md
├── composer.json
├── create_token.php
├── jwt.php
├── openapi_lite.yaml
├── README.md
└── verify_token.php
```

## 🧠 Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🔹 Routing Manual | Mengatur endpoint API di public/index.php |
| 🔹 Response JSON | Format response konsisten (Response::json()) |
| 🔹 JWT Auth | Login menghasilkan token JWT, dicek lewat middleware |
| 🔹 Upload File Aman | Validasi MIME type dan batas 2MB |
| 🔹 Rate Limiting | Batasi request per IP/token |
| 🔹 Pagination | Diterapkan di endpoint /users |
| 🔹 CORS Middleware | Mengizinkan akses dari frontend (browser) |
| 🔹 Postman + OpenAPI | Dokumentasi dan testing otomatis |

## 🧪 Testing API Menggunakan Postman

Gunakan koleksi `API PHP Native.postman_collection.json` untuk uji cepat di Postman.

### 🔑 Login

**Endpoint:**

```
POST /api/v1/auth/login
```

**Body (JSON):**

```json
{
  "email": "irene@example.com",
  "password": "Cinta_sejati_takkan_pernah_pudar_oleh_waktu"
}
Setelah berhasil login, Postman otomatis menyimpan token di variabel `{{token}}`.

## 📋 Endpoint Utama

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | /api/v1/health | Cek status server |
| POST | /api/v1/auth/login | Login user |
| GET | /api/v1/users | Tampilkan daftar user |
| POST | /api/v1/users | Tambahkan user baru |
| PUT | /api/v1/users/{id} | Update user |
| DELETE | /api/v1/users/{id} | Hapus user |
| POST | /api/v1/upload | Upload file (gambar/pdf) |

## 🌐 Dokumentasi OpenAPI-lite

File dokumentasi API tersedia di: `openapi-lite.yaml`

Kamu bisa membuka file ini di Swagger Editor:
👉 https://editor.swagger.io
dan menampilkan dokumentasi otomatis API kamu.

## 🧾 Changelog

Lihat file `CHANGELOG.md` untuk catatan versi.

Contoh:

```markdown
## [1.0.0] - 2025-11-12
### Added
- Routing dasar
- JWT Auth
- CRUD Users + Validation
- Upload File Aman
- Rate Limiting
- Dokumentasi OpenAPI & Postman
```

## 🧰 Troubleshooting

| Masalah | Penyebab | Solusi |
|---------|---------|--------|
| ❌ 404 Route not found | URL salah | Pastikan path sesuai route di index.php |
| ⚠ 401 Invalid credentials | Password salah / user belum ada | Periksa tabel users |
| ⏱ 429 Too Many Requests | Melebihi limit request | Tunggu 1 menit lalu ulangi |
| 📁 Upload gagal | File terlalu besar atau bukan gambar/pdf | Pastikan ukuran < 2MB dan format valid |

## 👨‍💻 Pengembang

| Nama | Keterangan |
|------|-----------|
| Nama | Irene Tini Buttu |
| NIM | 223611002 |
| Kelas | D5 |
| Dosen Pengampu | Semuel Yacobus Padang, S.Kom.,M.Kom |

## 📚 Lisensi
Proyek ini digunakan untuk keperluan pembelajaran Pemrograman Jaringan.
Tidak untuk tujuan komersial atau distribusi ulang tanpa izin dosen pengampu.

<p align="center"> Dibuat dengan ❤ menggunakan <b>PHP Native</b><br/> © 2025 Praktikum Pemrograman Jaringan Universitas Kristen Indonesia Toraja (UKIt) </p>
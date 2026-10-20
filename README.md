# Marine Rangers

Sistem Informasi Berbasis Website untuk Pelaporan dan Pemantauan Sampah di Pantai dan Laut.  
Proyek Tugas Besar Mata Kuliah **Pengembangan Perangkat Lunak (PPL)** dengan metode **Scrum**, mendukung pencapaian **SDG 14: Life Below Water (Ekosistem Lautan)**.

---

## 🛠 Tech Stack

- **Framework**: Laravel 12/13 (PHP 8.2+)
- **Database**: MySQL / MariaDB (InnoDb)
- **Architecture**: MVC dengan Eloquent ORM
- **Testing**: PHPUnit / Laravel Feature Testing

---

## 🚀 Panduan Instalasi (Setup Cepat)

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan lokal:

### 1. Clone Repository & Masuk Direktori
```bash
git clone <url-repository>
cd Marine-Rangers
```

### 2. Pasang Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Environment
Salin template `.env.example` menjadi `.env`:
- **Windows (Command Prompt / PowerShell)**:
  ```powershell
  copy .env.example .env
  ```
- **Linux / macOS**:
  ```bash
  cp .env.example .env
  ```

Buka file `.env` dan pastikan konfigurasi database sesuai dengan pengaturan MySQL lokal Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marine_rangers
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Encryption Key
```bash
php artisan key:generate
```

### 5. Buat Database MySQL
Buat database bernama `marine_rangers` melalui phpMyAdmin, MySQL CLI, atau GUI client lainnya:
```sql
CREATE DATABASE marine_rangers CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Jalankan Migrasi & Seeder Default
Jalankan perintah ini untuk membangun tabel dan mengisi data awal (akun demo, kategori, 100+ laporan realistis pesisir Surabaya, serta riwayat pembersihan):
```bash
php artisan migrate --seed
```

> **Tips Uji Beban Peta (Opsional)**:  
> Untuk mengisi 1.000 titik koordinat guna menguji performa peta spasial, jalankan:  
> `php artisan db:seed --class=LaporanBesarSeeder`

### 7. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: `http://127.0.0.1:8000`

---

## 👥 Akun Demo untuk Pengujian

Semua akun demo menggunakan password default: **`password`**

| Role | Nama | Email | Organisasi / Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | Admin Marine Rangers | `admin@marinerangers.test` | Dinas Lingkungan Hidup Surabaya |
| **Pemantau** | Koordinator Pemantau | `pemantau@marinerangers.test` | Surabaya Peduli Laut |
| **Masyarakat** | Budi Relawan | `relawan1@marinerangers.test` | Relawan Warga Pesisir 1 |
| **Masyarakat** | Siti Relawan | `relawan2@marinerangers.test` | Relawan Warga Pesisir 2 |

---

## 🧪 Menjalankan Automated Test

Untuk memverifikasi integritas database, relasi model Eloquent, default nilai role/status, cascade delete, dan seeder:
```bash
php artisan test --testdox
```

---

## 🗄 Struktur & Skema Database

Skema mengacu pada ERD proposal (Gambar 4) dengan penyesuaian primary key kustom:
- `users` (PK: `id_user`)
- `kategori` (PK: `id_kategori`)
- `laporan` (PK: `id_laporan`, FK: `id_user`, `id_kategori`)
- `pembersihan` (PK: `id_pembersihan`, FK: `id_laporan`, `id_user`)
- `log_aktivitas` (PK: `id_log`, FK: `id_user`)
- `dashboard` (PK: `id_dashboard`, FK: `id_user`)

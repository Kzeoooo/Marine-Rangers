# Marine Rangers

Sistem Informasi Berbasis Website untuk Pelaporan dan Pemantauan Sampah di Pantai dan Laut.  
Proyek Tugas Besar Mata Kuliah **Pengembangan Perangkat Lunak (PPL)** dengan metode **Scrum**, mendukung pencapaian **SDG 14: Life Below Water (Ekosistem Lautan)**.

---

## 🛠 Tech Stack

- **Framework**: Laravel 11/12 (PHP 8.2+)
- **Database**: MySQL / MariaDB (InnoDB)
- **Frontend / Styling**: Tailwind CSS, Blade Template Engine, Leaflet.js (Peta Spasial Interaktif)
- **Architecture**: MVC dengan Eloquent ORM
- **Testing**: PHPUnit / Laravel Feature Testing

---

## 🌊 Fitur Utama

1. **Pelaporan Sampah Pesisir (Masyarakat / Relawan)**:
   - Pelaporan sampah berbasis foto bukti dan geolokasi (GPS/koordinat otomatis).
   - Kategorisasi jenis sampah (plastik, styrofoam, jaring/ghost gear, kaca/logam, dll.).
2. **Peta Sebaran Spasial & Pemantauan Khusus Wilayah (Role Pemantau)**:
   - **Isolasi Wilayah Tugas Pemantau**: Setiap pemantau difokuskan hanya pada daerah penugasannya masing-masing (contoh: Kenjeran, Kedung Cowek, Tambak Wedi, dll.).
   - Peta interaktif Leaflet otomatis memusatkan tampilan (*auto-center* & *auto-zoom*) ke koordinat pesisir wilayah tugas.
   - Statistik operasional (Total Laporan, Laporan Aktif, Persentase Tindak Lanjut) terhitung secara lokal untuk area pemantau terkait.
   - **Wewenang Eksklusif Pengubahan Status**: Hanya pemantau wilayah yang berhak mengubah tahapan status laporan (*Dipantau* ➔ *Ditindaklanjuti* ➔ *Selesai*) untuk menjaga integritas fakta lapangan.
   - Proteksi otorisasi berlapis (HTTP 403 Forbidden) mencegah akses dan aksi di luar wilayah kewenangan.
3. **Panel Kontrol Administrator & Manajemen Pengguna (Dinas Lingkungan Hidup)**:
   - **Kelola Akun Pengguna (`/admin/pengguna`)**: Administrator dapat mengelola seluruh akun pengguna sistem (Admin, Pemantau Wilayah, Relawan Masyarakat), mendaftarkan pemantau baru lengkap dengan penetapan `wilayah_tugas`, memperbarui profil dan peran, serta menghapus akun secara aman.
   - Pengawasan menyeluruh (*monitoring & audit trail*) seluruh titik wilayah pesisir tanpa batasan area (mode pengawasan read-only pada detail laporan).
   - Pemantauan aktivitas sistem, rekapitulasi akun pengguna, dan penugasan wilayah pemantau.
4. **Alur Status Laporan & Konfirmasi Pembersihan**:
   - 4 Tahapan status transparan: **Baru** ➔ **Dipantau** ➔ **Ditindaklanjuti** ➔ **Selesai**.
   - Pelaporan hasil aksi pembersihan pantai disertai unggah foto bukti dan deskripsi hasil oleh pemantau lapangan.

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
Jalankan perintah ini untuk membangun tabel dan mengisi data master (akun demo seluruh peran dan kategori sampah pesisir). Database dimulai dalam kondisi **bersih (0 laporan dummy)** sehingga siap menerima input laporan baru:
```bash
php artisan migrate --seed
```

> **Perintah Bersihkan Data Laporan**:  
> Jika sewaktu-waktu ingin membersihkan seluruh laporan dan riwayat aksi pembersihan agar database kembali kosong bersih:  
> ```bash
> php artisan laporan:clear
> ```

> **Tips Isi Dummy Laporan untuk Uji Coba (Opsional)**:  
> Jika memerlukan data dummy untuk simulasi:  
> - 100+ laporan realistis: `php artisan db:seed --class=LaporanSeeder`  
> - 1.000 titik koordinat (uji performa peta): `php artisan db:seed --class=LaporanBesarSeeder`

### 7. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: `http://127.0.0.1:8000`

---

## 👥 Akun Demo untuk Pengujian Hak Akses

Semua akun demo menggunakan password default: **`password`**  
*(Tersedia juga fitur tombol **Quick Login 1-Klik** di halaman login untuk kemudahan pengujian antarkolega).*

| Role / Peran | Wilayah Penugasan | Email | Nama Akun & Organisasi |
| :--- | :--- | :--- | :--- |
| **Admin** | *Semua Wilayah (Global)* | `admin@marinerangers.test` | Admin DLH Surabaya |
| **Pemantau** | **Pantai Kenjeran** | `pemantau.kenjeran@marinerangers.test` | Pemantau Kenjeran *(Pokdarwis Kenjeran Bersih)* |
| **Pemantau** | **Kedung Cowek** | `pemantau.kedungcowek@marinerangers.test` | Pemantau Kedung Cowek *(Forum Mangrove Kedung Cowek)* |
| **Pemantau** | **Tambak Wedi** | `pemantau.tambakwedi@marinerangers.test` | Pemantau Tambak Wedi *(Komunitas Pesisir Suramadu)* |
| **Pemantau** | **Pantai Ria Kenjeran** | `pemantau.pantairia@marinerangers.test` | Pemantau Pantai Ria *(Sahabat Lingkungan Pantai Ria)* |
| **Pemantau** | **Sukolilo Baru** | `pemantau.sukolilo@marinerangers.test` | Pemantau Sukolilo Baru *(Laskar Bahari Nelayan Sukolilo)* |
| **Masyarakat** | - | `relawan1@marinerangers.test` | Budi Relawan *(Warga Pesisir)* |
| **Masyarakat** | - | `relawan2@marinerangers.test` | Siti Relawan *(Warga Pesisir)* |

---

## 🧪 Menjalankan Automated Test

Untuk memverifikasi integritas database, relasi model Eloquent, default nilai role/status, pembatasan akses wilayah pemantau, dan cascade delete:
```bash
php artisan test --testdox
```

---

## 🗄 Struktur & Skema Database

Skema mengacu pada ERD sistem dengan penyesuaian atribut penugasan wilayah:
- **`users`** (PK: `id_user`): `nama`, `email`, `password`, `role` (*masyarakat / pemantau / admin*), `organisasi`, **`wilayah_tugas`** *(daerah penugasan khusus pemantau)*
- **`kategori`** (PK: `id_kategori`): master jenis sampah pesisir
- **`laporan`** (PK: `id_laporan`, FK: `id_user`, `id_kategori`): koordinat latitude/longitude, foto bukti, deskripsi, waktu insiden, `wilayah`, `status`
- **`pembersihan`** (PK: `id_pembersihan`, FK: `id_laporan`, `id_user`): dokumentasi hasil tindak lanjut pembersihan sampah di lapangan
- **`log_aktivitas`** (PK: `id_log`, FK: `id_user`): audit trail setiap aktivitas pengguna di sistem
- **`dashboard`** (PK: `id_dashboard`, FK: `id_user`): preferensi metrik dasbor

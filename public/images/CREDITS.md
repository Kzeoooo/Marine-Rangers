# Kredit & Panduan Aset Foto Marine Rangers

Dokumen ini mencatat lisensi, sumber, dan spesifikasi dimensi foto yang digunakan dalam sistem informasi **Marine Rangers**.  
Semua foto menampilkan tema **pesisir dan laut Indonesia yang tenang, bersih, dan hangat**. Jika sebuah file foto belum tersedia di folder `public/images/`, antarmuka web secara otomatis mengaktifkan *fallback* visual berupa gradien laut (*deep blue* ke *teal*) disertai ilustrasi ombak SVG tanpa merusak tata letak.

---

## 📷 Daftar Kebutuhan Foto & Spesifikasi Dimensi

| Nama Berkas | Penempatan Antarmuka | Rekomendasi Dimensi | Orientasi / Rasio | Status Ketersediaan |
| :--- | :--- | :--- | :--- | :--- |
| `hero-laut.jpg` | Hero Banner Beranda (`welcome.blade.php`) | 1920 &times; 1080 px | Lanskap (16:9) | *Menunggu aset tim / Fallback aktif* |
| `login-pantai.jpg` | Panel Visual Kiri Login & Daftar (`layouts/auth.blade.php`) | 1200 &times; 1600 px | Potret (3:4 atau 9:16) | *Menunggu aset tim / Fallback aktif* |
| `terumbu-karang.jpg` | Bagian Edukasi SDG 14.1 (`welcome.blade.php`) | 1000 &times; 750 px | Lanskap (4:3) | *Menunggu aset tim / Fallback aktif* |
| `bersih-pantai.jpg` | Bagian Aksi Gotong Royong (`welcome.blade.php`) | 1200 &times; 800 px | Lanskap (3:2) | *Menunggu aset tim / Fallback aktif* |
| `pantai-kenjeran.jpg` | Banner Halaman Lapor & Form (`components/page-banner.blade.php`) *(Opsional)* | 1440 &times; 480 px | Lebar / Panorama (3:1) | *Menunggu aset tim / Fallback aktif* |

---

## 📝 Tabel Atribusi & Lisensi (Saat Foto Diunggah)

| Berkas Foto | Sumber / Platform | Fotografer / Kontributor | Lisensi | Tautan Asli |
| :--- | :--- | :--- | :--- | :--- |
| `hero-laut.jpg` | Unsplash / Pexels / Koleksi Tim | — | Bebas Komersial / CC0 | — |
| `login-pantai.jpg` | Unsplash / Pexels / Koleksi Tim | — | Bebas Komersial / CC0 | — |
| `terumbu-karang.jpg`| Unsplash / Pexels / Koleksi Tim | — | Bebas Komersial / CC0 | — |
| `bersih-pantai.jpg` | Dokumentasi Kegiatan PPL / Tim | — | Hak Cipta Tim Marine Rangers | — |
| `pantai-kenjeran.jpg`| Dokumentasi Kegiatan PPL / Tim | — | Hak Cipta Tim Marine Rangers | — |

---

## 🛡 Aturan Implementasi Teknis
1. Panggil foto selalu melalui helper `asset('images/[nama-file].jpg')`.
2. Semua tag `<img>` wajib menyertakan atribut `alt` deskriptif dalam Bahasa Indonesia.
3. Gunakan `loading="lazy"` untuk seluruh foto kecuali pada banner hero utama.
4. Terapkan kelas `object-cover` untuk memastikan foto responsif di layar ponsel (mobile 375 px) hingga desktop lebar.
5. Pastikan selalu terdapat overlay gradien warna midnight (`#21295C`) di atas gambar untuk menjaga kontras teks minimal **4.5:1** (standar WCAG AA).

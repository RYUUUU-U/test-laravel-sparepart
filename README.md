# Inventory Sparepart E-Commerce

Aplikasi web e-commerce dan manajemen inventory sparepart berbasis Laravel.

## Prasyarat
- **Laragon** (direkomendasikan) atau XAMPP/MAMP
- **PHP** >= 8.2
- **Composer**
- **Node.js** & **NPM**

## Langkah-langkah Instalasi (Khususnya Pengguna Laragon)

1. **Copy Project**
   Pastikan folder project ini (`sparepart-app`) berada di dalam folder `C:\laragon\www\`.

2. **Buka Terminal Laragon**
   Buka aplikasi Laragon, lalu klik tombol **Terminal**. Arahkan ke folder project:
   ```bash
   cd C:\laragon\www\sparepart-app
   ```

3. **Install Dependencies**
   Jalankan perintah berikut di terminal untuk mengunduh library PHP dan Node.js:
   ```bash
   composer install
   npm install
   ```

4. **Konfigurasi Environment (.env)**
   Secara default, Laravel membutuhkan file `.env`. 
   - Jika belum ada, copy dari `.env.example` dengan perintah: `cp .env.example .env`
   - Buka file `.env` dan pastikan konfigurasi database sudah sesuai dengan MySQL di Laragon:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=if0_40684561_inventory
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > **Penting:** Buat database bernama `if0_40684561_inventory` di HeidiSQL atau phpMyAdmin yang disediakan oleh Laragon sebelum melanjutkan.

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database & Seeder**
   Jalankan migrasi untuk membuat tabel-tabel di database Anda:
   ```bash
   php artisan migrate
   ```
   *(Jalankan `php artisan db:seed` jika sebelumnya ada instruksi untuk mengenerate data dummy atau me-rehash password legacy).*

7. **Kompilasi Aset Frontend (Vite)**
   Agar tampilan (CSS dan JS) dirender dengan benar, jalankan:
   ```bash
   npm run build
   ```
   *(Atau gunakan `npm run dev` saat sedang melakukan proses development).*

8. **Akses Aplikasi**
   Karena menggunakan Laragon dengan fitur *Auto Virtual Hosts*, Anda bisa langsung membuka browser dan mengakses:
   ```text
   http://sparepart-app.test
   ```
   Atau jika menggunakan cara manual:
   ```bash
   php artisan serve
   ```
   Dan buka `http://localhost:8000`.

## Catatan Tambahan (Hal yang perlu diperhatikan)
1. **Xendit Payment Gateway**: Aplikasi ini terintegrasi dengan Xendit. Pastikan variabel `XENDIT_SECRET_KEY` di dalam `.env` sudah terisi dengan Secret Key dari dashboard Xendit (mode *Test/Development*) agar fitur checkout dapat bekerja dengan baik.
2. **Upload & Gambar**: Jika terdapat fitur upload foto produk/bukti bayar, pastikan untuk menjalankan perintah `php artisan storage:link` di terminal agar folder storage terhubung ke public.
3. **Konflik Port**: Pastikan Apache/Nginx dan MySQL di Laragon sudah dalam keadaan "Start" (berjalan) dan tidak mengalami bentrok port (seperti port 80 atau 3306) dengan aplikasi lain (misal: Skype atau XAMPP yang masih aktif).

## Menghubungkan ke Ngrok (Untuk Testing Pembayaran/Xendit)

Karena menggunakan Xendit (Payment Gateway), Xendit butuh mengirimkan *Callback/Webhook* jika ada pembayaran berhasil. Agar Xendit bisa mendeteksi project yang ada di komputer lokal Anda, Anda butuh menggunakan **Ngrok**.

**Apakah Laragon harus selalu menyala?**
**YA**. Minimal **MySQL** di Laragon harus selalu berjalan.

**Opsi 1: Menggunakan `php artisan serve` (Paling Mudah)**
1. Pastikan **MySQL** di Laragon menyala.
2. Buka terminal Laragon, ketik: `php artisan serve` (aplikasi akan berjalan di port `8000`).
3. Buka tab terminal baru (atau Command Prompt biasa), ketik perintah ngrok:
   ```bash
   ngrok http 8000
   ```

**Opsi 2: Menggunakan Virtual Host Laragon (port 80)**
1. Pastikan **Apache/Nginx** dan **MySQL** di Laragon menyala.
2. Di terminal baru, jalankan ngrok dengan menambahkan `host-header` sesuai nama virtual host Anda:
   ```bash
   ngrok http 80 --host-header=sparepart-app.test
   ```

Setelah menjalankan Ngrok, copy URL *Forwarding* yang diberikan Ngrok (contoh: `https://abcd-123.ngrok-free.app`), lalu daftarkan URL tersebut ke Dashboard Xendit Anda di bagian Webhook, atau sesuaikan konfigurasi `.env` Anda jika diperlukan.

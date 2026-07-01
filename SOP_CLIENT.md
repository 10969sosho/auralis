# Standard Operating Procedure (SOP) — Ship Ticketing System

## Daftar Isi
1. [Akses & Login](#1-akses--login)
2. [Alur Customer (Pembeli Tiket)](#2-alur-customer-pembeli-tiket)
3. [Peran Admin](#3-peran-admin)
4. [Peran Ticket Counter Officer](#4-peran-ticket-counter-officer)
5. [Peran Boarding Officer](#5-peran-boarding-officer)

---

## 1. Akses & Login

### 1.1 Link Akses

| Portal | URL | Keterangan |
|--------|-----|------------|
| Website Publik | `https://auralis8.com` | Untuk customer, info jadwal, harga |
| Admin Panel | `https://auralis8.com/admin` | Untuk admin, boarding officer, counter officer |

### 1.2 Login (Semua Role)

1. Buka `https://auralis8.com/login`
2. Masukkan **email** dan **password**
3. Klik **Login**

> **Untuk admin**: Setelah login, buka `https://auralis8.com/admin` untuk masuk ke panel Filament. <br>
> **Untuk boarding officer & counter**: Login biasa, lalu akses menu sesuai role.

### 1.3 Role & Hak Akses

| Role | Hak Akses | Halaman |
|------|-----------|---------|
| **Admin** | Full akses ke semua fitur | Admin panel + website |
| **Ticket Counter Officer** | Buat booking offline, proses pembayaran | Panel counter |
| **Boarding Officer** | Scan tiket, validasi boarding, lihat manifest | Halaman boarding |
| **Deportation** | _(opsional, diabaikan)_ | - |

---

## 2. Alur Customer (Pembeli Tiket)

### 2.1 Alur Lengkap

```
Cari Jadwal → Pilih Jadwal → Isi Data Penumpang → Pesan → Bayar (Upload Bukti) 
→ Tunggu ACC Admin → Dapat Tiket → Boarding
```

### 2.2 Langkah-langkah

#### A. Cari Jadwal
1. Buka `https://auralis8.com`
2. Pilih **asal** dan **tujuan**, lalu klik **Cari Jadwal**
3. Akan tampil daftar jadwal tersedia

#### B. Pilih Jadwal & Booking
1. Klik **Pesan** pada jadwal yang diinginkan
2. Login/register jika belum login
3. Pilih kelas (**VIP** / **Regular**)
4. Isi data penumpang (nama, passport, dll)
5. Klik **Pesan Sekarang**

#### C. Pembayaran
1. Akan muncul halaman pembayaran
2. **Scan QR Code** yang tampil (bisa pakai e-wallet/m-banking) **ATAU** transfer manual:
   - **Bank Muamalat**: 5706016718 a.n Fajar Pratama
3. Upload **bukti transfer** (format JPG/PNG, maks 5MB)
4. Klik **Upload & Konfirmasi Pembayaran**

#### D. Tunggu Approval
- Status akan menjadi **Menunggu ACC**
- Admin akan memproses pembayaran
- Jika **ACC** → Tiket otomatis terbit (bisa dilihat di halaman riwayat)
- Jika **Ditolak** → Bisa upload ulang bukti transfer

#### E. Boarding
1. Datang ke pelabuhan sebelum jadwal keberangkatan
2. Tunjukkan tiket (QR code) ke **Boarding Officer**
3. Boarding Officer akan scan QR → Validasi

---

## 3. Peran Admin

### 3.1 Akses
- Buka `https://auralis8.com/admin`
- Login dengan akun yang punya role **admin**

### 3.2 Tugas-tugas Admin

#### A. Atur Master Data

| Menu | Fungsi |
|------|--------|
| **Ships** | Tambah/edit data kapal (nama, kapasitas VIP & Regular) |
| **Routes** | Tambah/edit rute pelayaran (asal, tujuan, durasi) |
| **Schedules** | Atur jadwal keberangkatan (kapal, rute, jam, harga) |
| **Age Categories** | Atur kategori umur penumpang |
| **Promos** | Buat promo diskon tiket |

#### B. Proses Pembayaran (Penting!)
Menu: **Pembayaran**

1. Buka menu **Pembayaran**
2. Lihat daftar pembayaran yang masuk
3. Perhatikan kolom **Status**:
   - 🟡 **Menunggu ACC** — Butuh diproses
   - 🟢 **Disetujui** — Sudah ACC
   - 🔴 **Ditolak** — Ditolak
   - 🔵 **Completed** — Penumpang sudah boarding
4. Untuk pembayaran yang **Menunggu ACC**:
   - **Lihat Bukti** → Klik ikon mata untuk lihat gambar bukti transfer
   - **ACC** → Klik tombol hijau → Konfirmasi → Tiket otomatis terbit
   - **Tolak** → Klik tombol merah → Isi alasan → Customer bisa upload ulang

#### C. QR Code Pembayaran
Menu: **Settings → Payment QR**

- Upload **QR code** untuk ditampilkan ke customer saat bayar
- Customer bisa scan QR untuk bayar via e-wallet/m-banking

#### D. Boarding Logs
Menu: **Operational → Boarding Logs**

- Lihat **riwayat scan boarding**
- Info: tiket, penumpang, petugas, hasil, metode scan

#### E. Reports & Analytics
Menu: **Analytics → Reports**

- Dashboard dengan statistik **real-time** (auto-refresh 30 detik)
- **KPI Cards**: Total penumpang, pendapatan, okupansi, pending, refund
- **Charts**: Revenue trend & Booking trend (7/30 hari)
- **Schedule Table**: Detail per-jadwal (booked, paid, boarded, revenue, occupancy)
- Filter: Pilih jadwal, status, tanggal
- **Export CSV**: Download laporan

#### F. Admin Lainnya
| Menu | Fungsi |
|------|--------|
| **Users** | Kelola akun user |
| **Notifications** | Kirim notifikasi |
| **Audit Logs** | Riwayat aktivitas sistem |
| **Refunds** | Proses refund jika ada |

---

## 4. Peran Ticket Counter Officer

### 4.1 Akses
- Login di website publik `https://auralis8.com/login`
- Gunakan akun dengan role **ticket_counter_officer**

### 4.2 Tugas
Melayani **pembelian tiket offline** (customer datang ke loket):

1. Buka halaman **Counter Dashboard** (URL: /counter)
2. Pilih jadwal yang tersedia
3. Isi data penumpang (nama, passport, dll)
4. Pilih kelas (VIP/Regular)
5. Proses pembayaran
6. Customer menerima tiket

---

## 5. Peran Boarding Officer

### 5.1 Akses
- Login di website publik `https://auralis8.com/login`
- Gunakan akun dengan role **boarding_officer**

### 5.2 Tugas
Melakukan **validasi boarding** penumpang sebelum naik kapal:

#### A. Boarding Scanner
1. Buka halaman **Boarding Scanner** (URL: `/boarding/scanner`)
2. Scan **QR code tiket** customer menggunakan kamera
3. Sistem akan menampilkan hasil:
   - 🟢 **Success**: Data penumpang muncul → Boarding disetujui
   - 🟡 **Already Used**: Tiket sudah dipakai sebelumnya
   - 🟠 **Expired**: Tiket sudah expired
   - 🔴 **Invalid**: Tiket tidak valid
4. Bisa juga manual input dengan **ticket number** atau **booking code**

#### B. Boarding Manifest
1. Buka halaman **Manifest** (URL: `/boarding/manifest/{schedule_id}`)
2. Lihat daftar penumpang untuk jadwal tertentu
3. Cek status boarding masing-masing penumpang

#### C. Boarding Logs
- Lihat riwayat boarding di menu **Operational → Boarding Logs** (khusus admin)

---

## 6. Status-status Penting

### Booking Status (Customer)
| Status | Arti |
|--------|------|
| Pending Payment | Belum bayar, masih dalam proses |
| Awaiting Approval | Sudah upload bukti, menunggu ACC admin |
| Paid | Lunas, tiket aktif |
| Completed | Penumpang sudah boarding |
| Cancelled | Dibatalkan |
| Expired | Kedaluwarsa (waktu habis) |
| Refunded | Sudah direfund |

### Payment Status (Admin)
| Status | Arti |
|--------|------|
| Awaiting Approval | Upload bukti, butuh ACC |
| Approved | Disetujui admin |
| Paid | Lunas |
| Completed | Penumpang sudah boarding |
| Rejected | Ditolak (dengan alasan) |
| Expired | Kedaluwarsa |

### Boarding Result
| Hasil | Arti |
|-------|------|
| Valid | Tiket sah, boarding sukses |
| Already Used | Tiket sudah dipakai |
| Expired | Tiket kadaluwarsa |
| Invalid | Tiket tidak valid |

---

## 7. Catatan Penting

1. **Storage**: Semua file upload (bukti transfer, QR code) tersimpan di folder `storage/app/public/` dan bisa diakses via URL `/storage/...`
2. **Cron Job**: Pastikan cron job berjalan untuk cancel otomatis booking expired:
   ```
   * * * * * cd /home/.../auralis8.com && php artisan schedule:run
   ```
3. **Keamanan**: Hanya **admin** yang bisa akses panel Filament. Boarding officer & counter tidak bisa akses admin panel.

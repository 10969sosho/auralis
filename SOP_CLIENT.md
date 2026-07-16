# Standard Operating Procedure (SOP) — Ship Ticketing System

## Daftar Isi
1. [Akses & Login](#1-akses--login)
2. [Alur Buyer / Pembeli Tiket](#2-alur-buyer--pembeli-tiket)
3. [Alur Guest (Tanpa Login)](#3-alur-guest-tanpa-login)
4. [Alur Deportasi](#4-alur-deportasi)
5. [Peran Admin](#5-peran-admin)
6. [Peran Counter Officer](#6-peran-counter-officer)
7. [Peran Boarding Officer](#7-peran-boarding-officer)
8. [Status-status Penting](#8-status-status-penting)
9. [Catatan Penting](#9-catatan-penting)

---

## 1. Akses & Login

### 1.1 Link Akses
- Website Publik — `https://auralis8.com` — untuk buyer cari jadwal, booking, info
- Admin Panel — `https://auralis8.com/admin` — khusus admin
- Login — `https://auralis8.com/login`

### 1.2 Role & Hak Akses
- *Admin* — full akses ke semua fitur (panel Filament + website)
- *Counter Officer* — buat & bayar booking offline di loket
- *Boarding Officer* — scan tiket, validasi boarding, lihat manifest
- *Deportation Officer* — urus manifest & boarding deportasi
- *Buyer/Guest* — cari jadwal, booking online, bayar manual QR

### 1.3 Cara Ganti Password Sendiri
- Panel admin (Filament): klik ikon profil di pojok kanan atas → klik nama → Edit Profile → ganti password
- Website biasa: belum ada fitur ganti password dari website

---

## 2. Alur Buyer / Pembeli Tiket

### 2.1 Alur Lengkap (Login)
```
Cari Jadwal → Pilih Jadwal → Isi Penumpang → Booking → Bayar Upload QR → ACC Admin → Email Tiket → Boarding
```

### 2.2 Langkah
- **Cari jadwal** — buka `https://auralis8.com/schedules`, pilih asal & tujuan
- **Pilih jadwal** — klik *Book Now* pada jadwal yang diinginkan
- **Login/Register** — login atau register dulu (kalau belum)
- **Isi penumpang** — nama, gender, tgl lahir, nationality, passport, phone, pilih kelas VIP/Regular, upload passport file
- **Booking** — klik *Continue to Payment*, sistem buat booking dengan expiry 30 minit
- **Bayar** — di halaman payment, scan QR code untuk transfer manual
- **Upload bukti** — upload screenshot/photo bukti transfer
- **Tunggu ACC admin** — status jadi *Awaiting Approval*
- **Admin ACC** — admin setujui → tiket terbit otomatis
- **Email masuk** — dapat email *Payment Approved* dengan link booking + PDF tiket di attachment
- **Board** — tunjuk QR code tiket ke boarding officer

---

## 3. Alur Guest (Tanpa Login)

### 3.1 Alur Lengkap (Guest)
```
Cari Jadwal → Pilih Jadwal → Isi Penumpang + Email → Booking (tanpa login) → Bayar → ACC Admin → Email + PDF Tiket
```

### 3.2 Perbezaan dari Buyer Login
- *Tak perlu login* — terus isi data penumpang
- *Masukkan email* — field email muncul untuk guest, guna hantar link booking + tiket
- *Booking tanpa user* — sistem create booking dengan *guest_email* dan *guest_token*
- *Email diterima* — dapat email *Booking Guest* dengan link payment
- *Selepas ACC* — dapat email *Payment Approved* dengan PDF tiket di attachment
- *Akses booking* — guna link khas dengan token (tak perlu login)

### 3.3 Cara Akses Tiket (Tanpa Login)
- Buka link dari email: `/booking/guest/BK-XXXX?token=...`
- Klik *PDF* untuk download tiket setiap penumpang
- Atau buka attachment PDF terus dari email

---

## 4. Alur Deportasi

### 4.1 Alur Lengkap
```
Register Deportasi → Login → Booking (open ticket) → Bayar → ACC Admin → Boarding di shelter
```

### 4.2 Aliran Deportasi
- **Register** — buka `/deportation/register`, isi nama, email, password, phone, shelter point
- **Login** — login guna akaun deportasi
- **Dashboard** — `/deportation/dashboard` — lihat ringkasan booking & jadual
- **Booking** — klik *Book Now* di dashboard, isi data penumpang
- **Payment** — upload bukti transfer manual
- **Admin ACC** — admin approve → tiket deportasi terbit (open ticket, tiada expiry)
- **Board** — tunjuk QR ke deportation officer di shelter point
- **Email** — dapat email boarding success

### 4.3 Perbezaan dari Tiket Biasa
- *Open ticket* — tiada jadual kapal tertentu, tiada expiry date
- *Shelter point* — ada lokasi shelter (Tawau, Sandakan, Kinabalu) dengan fee berbeza
- *Boarding di shelter* — bukan di pelabuhan biasa
- *Tiada ToyibPay* — hanya manual transfer

### 4.4 Officer Deportasi
- *Deportation Officer* — role khas untuk urus manifest & scan boarding deportasi
- *Scanner* — `/deportation/scanner` — scan QR tiket deportasi
- *Manifest* — `/deportation/manifests` — lihat & urus manifest

---

## 5. Peran Admin

### 5.1 Akses
- Login di `https://auralis8.com/login`
- Buka `https://auralis8.com/admin` — hanya *admin* yang boleh akses

### 5.2 Sidebar Admin (Filament)

#### Group: Operational
- *Ships* — urus kapal (nama, kapasiti VIP/Regular, free baggage)
- *Routes* — urus rute pelayaran (asal, tujuan, durasi)
- *Schedules* — urus jadwal keberangkatan (kapal, rute, waktu, harga)
- *Boarding Logs* — lihat riwayat scan boarding (read-only)
- *Manifests* — urus manifest deportasi

#### Group: Transactions
- *Pembayaran* — proses payment yang menunggu ACC admin
  - Filter status, lihat bukti transfer, *ACC* (setujui), *Tolak*
  - ACC → tiket terbit + email + PDF attachment dihantar
- *Refunds* — lihat daftar refund (read-only)

#### Group: Analytics
- *Reports* — dashboard dengan KPI cards, revenue chart, booking trend
  - Filter by jadwal, export CSV
- *Deportation* — dashboard deportasi
  - Stats: total registered users, total bookings, paid, boarded
  - Tables: registered users, payment status, boarded passengers

#### Group: Settings
- *Payment QR* — upload QR code payment yang akan nampak di halaman bayar buyer
- *Age Categories* — urus kategori umur (Infant/Child/Adult) untuk harga
- *Promos* — urus promo diskon (auto apply atau pakai kode)
- *Payment QR* — upload gambar QR code untuk manual transfer

#### Group: Admin
- *Users* — urus semua akaun pengguna
  - Create user, edit, assign role, reset password
- *Notifications* — hantar notifikasi ke user
- *Audit Logs* — lihat history aktiviti sistem (read-only)

### 5.3 Cara Buat Akaun Staff
1. Buka *Admin → Users*
2. Klik *Create*, isi nama, email, password, set aktif
3. Klik *Save*
4. Edit user, centang role:
   - *ticket_counter_officer* — untuk staff counter
   - *boarding_officer* — untuk staff boarding
   - *deportation_officer* — untuk staff deportasi
5. Klik *Save*, beritahu staff email & password

### 5.4 Cara ACC Pembayaran
1. Buka *Transactions → Pembayaran*
2. Cari payment dengan status *Awaiting Approval*
3. Klik icon 👁 untuk lihat bukti transfer
4. Klik ✓ *ACC* untuk setujui → tiket terbit, email + PDF dihantar
5. Atau klik ✕ *Tolak* dengan alasan

### 5.5 Email Notification (Auto)
Sistem hantar email automatik:
- *Register* — welcome email ke user
- *Booking dibuat* — pending notification
- *Guest booking* — booking guest email dengan link
- *Admin ACC* — payment approved + PDF tiket di attachment
- *Boarding* — boarding success confirmation
- *Schedule berubah* — notification ke semua booking terlibat

---

## 6. Peran Counter Officer

### 6.1 Akses
- Login di website, guna akaun *ticket_counter_officer*
- Dashboard: `/counter`

### 6.2 Tugas
- **Buat booking** — cari schedule, isi data penumpang, proses pembayaran tunai/card
- **Cetak tiket** — dari halaman detail booking
- **Refund** — hanya untuk booking yang sudah paid
- **History** — lihat semua booking yang dibuat

---

## 7. Peran Boarding Officer

### 7.1 Akses
- Login di website, guna akaun *boarding_officer*
- Scanner: `/boarding/scanner`

### 7.2 Tugas
- **Scan QR** — scan QR code tiket buyer
  - *Valid* — hijau, boarding setuju
  - *Already Used* — kuning, tiket sudah dipakai
  - *Expired* — oren, tiket tamat
  - *Invalid* — merah, tiket tak sah
- **Manual validate** — guna booking code kalau QR tak boleh scan
- **Manifest** — lihat senarai penumpang per jadual

---

## 8. Status-status Penting

### Booking Status
- *Pending Payment* — belum bayar
- *Awaiting Approval* — sudah upload bukti, tunggu ACC admin
- *Paid* — sudah bayar, tiket aktif
- *Used / Completed* — penumpang sudah boarding
- *Cancelled* — booking dibatalkan
- *Expired* — tempoh 30 minit habis
- *Refunded* — sudah direfund

### Payment Status
- *Pending* — belum bayar
- *Awaiting Approval* — sudah upload bukti
- *Approved* — disetujui admin
- *Paid* — sudah bayar
- *Rejected* — ditolak admin (dengan alasan)
- *Expired* — booking expired

### Boarding Result
- *Valid* — tiket sah, boarding sukses
- *Already Used* — tiket sudah guna
- *Expired* — tiket tamat tempoh
- *Invalid* — tiket tak sah

---

## 9. Catatan Penting

1. **Pembayaran manual** — buyer upload bukti transfer, kena tunggu admin ACC baru tiket terbit
2. **QR payment** — admin upload QR code di *Settings → Payment QR* yang akan nampak di payment page
3. **Guest booking** — buyer boleh booking tanpa login, cuma perlu email untuk terima link & tiket
4. **Deportasi** — open ticket tanpa jadual, guna shelter point, boarding di shelter
5. **Email** — guna SMTP Hostinger (`cs@auralis8.com`), hantar ke user/guest untuk semua event penting
6. **Kapasiti VIP** — kalau `vip_capacity = 0`, opsi VIP tak muncul di form booking
7. **Booking expired** — booking auto expired lepas 30 minit kalau tak dibayar. Cron job kena jalan:
   ```
   * * * * * cd /home/.../auralis8.com && php artisan schedule:run
   ```
8. **Upload files** — passport, travel permit, bukti transfer simpan di `storage/app/public/`
9. **Keamanan** — hanya *admin* yang boleh akses panel Filament

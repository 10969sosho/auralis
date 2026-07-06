# Standard Operating Procedure (SOP) — Ship Ticketing System

## Daftar Isi
1. [Akses & Login](#1-akses--login)
2. [Alur Buyer / Pembeli Tiket](#2-alur-buyer--pembeli-tiket)
3. [Peran Admin](#3-peran-admin)
4. [Peran Counter Officer](#4-peran-counter-officer)
5. [Peran Boarding Officer](#5-peran-boarding-officer)

---

## 1. Akses & Login

### 1.1 Link Akses

| Portal | URL | Keterangan |
|--------|-----|------------|
| Website Publik | `https://auralis8.com` | Untuk buyer, info jadwal, harga |
| Admin Panel (Filament) | `https://auralis8.com/admin` | Khusus admin |

### 1.2 Login (Semua Role)

1. Buka `https://auralis8.com/login`
2. Masukkan **email** dan **password**
3. Klik **Login**

> **Untuk admin**: Setelah login, buka `https://auralis8.com/admin` untuk masuk ke panel Filament. <br>
> **Untuk counter & boarding officer**: Login biasa, lalu akses menu sesuai role dari navbar website.

### 1.3 Role & Hak Akses

| Role | Hak Akses | Halaman |
|------|-----------|---------|
| **Admin** | Full akses ke semua fitur | Admin panel (Filament) + website |
| **Counter Officer** | Buat booking offline via loket, proses pembayaran tunai | `/counter/*` |
| **Boarding Officer** | Scan tiket, validasi boarding, lihat manifest | `/boarding/*` |
| **Buyer** | Cari jadwal, booking online, bayar via ToyibPay | Website publik |

---

## 2. Alur Buyer / Pembeli Tiket

### 2.1 Alur Lengkap

```
Cari Jadwal → Pilih Jadwal → Isi Data Penumpang → Booking → Bayar via ToyibPay 
→ Dapat Tiket (otomatis) → Boarding
```

### 2.2 Langkah-langkah

#### A. Cari Jadwal
1. Buka `https://auralis8.com`
2. Pilih **asal** dan **tujuan**, lalu klik **Filter**
3. Akan tampil daftar jadwal tersedia

#### B. Pilih Jadwal & Booking
1. Klik **Book Now** pada jadwal yang diinginkan
2. Login/register jika belum login
3. Pilih jumlah penumpang
4. Isi data penumpang (nama, gender, tgl lahir, nationality, passport/ID, phone)
5. Pilih kelas (**VIP** / **Regular**) — *jika kapasitas VIP = 0, opsi VIP tidak muncul*
6. Upload **Passport/ID** (PDF/JPG/PNG, maks 5MB) dan **Travel Permit** (opsional)
7. Cek ringkasan booking di **Booking Summary**
8. Klik **Continue to Payment**

#### C. Pembayaran via ToyibPay
1. Setelah booking, sistem akan mengarahkan ke halaman pembayaran **ToyibPay**
2. Di halaman ToyibPay:
   - Pilih metode pembayaran (**QRIS**, **Virtual Account**, **Convenience Store**, dll)
   - Ikuti instruksi yang diberikan
3. Pembayaran diproses secara **real-time** oleh ToyibPay
4. Setelah sukses, sistem akan otomatis mengalihkan kembali ke halaman sukses

#### D. Tiket Otomatis Terbit
- Tiket langsung terbit **otomatis** setelah pembayaran berhasil — *tidak perlu menunggu ACC admin*
- Tiket bisa dilihat di halaman **My Bookings** (`/my-bookings`)
- Tiket bisa di-download (PDF) dan ditunjukkan saat boarding

#### E. Boarding
1. Datang ke pelabuhan sebelum jadwal keberangkatan
2. Tunjukkan tiket (QR code / PDF) ke **Boarding Officer**
3. Boarding Officer akan scan QR → Validasi

---

## 3. Peran Admin

### 3.1 Akses
- Login di `https://auralis8.com/login`
- Setelah login, buka `https://auralis8.com/admin`
- Hanya akun dengan **role admin** yang bisa mengakses panel ini

### 3.2 Sidebar Admin (Filament Panel)

Berikut adalah daftar menu di sidebar kiri panel admin beserta fungsinya:

---

### 3.2.1 Grup: 🚢 Operational

---

#### Ships
**Navigasi:** Operational → Ships

Fungsi: Mengelola data kapal (vessel).

| Field | Keterangan |
|-------|------------|
| Name | Nama kapal (e.g. MV Auralis 8) |
| VIP Capacity | Jumlah kursi VIP |
| Regular Capacity | Jumlah kursi Regular |
| Free Baggage | Berat bagasi gratis per penumpang (kg) |
| Status | Status kapal (active/inactive) |

> **Catatan**: Jika kapal sudah memiliki jadwal aktif, kapasitas VIP & Regular tidak bisa diubah. Nonaktifkan jadwalnya dulu baru bisa edit.

**Cara tambah kapal baru:**
1. Klik tombol **Create** (pojok kanan atas)
2. Isi nama kapal, kapasitas VIP & Regular, free baggage
3. Klik **Save**

**Cara edit kapal:**
1. Klik ikon **Edit** (pensil) pada kapal yang ingin diedit
2. Ubah data yang diperlukan
3. Klik **Save**

---

#### Routes
**Navigasi:** Operational → Routes

Fungsi: Mengelola rute pelayaran.

| Field | Keterangan |
|-------|------------|
| Origin Port | Pelabuhan asal (e.g. Bongao) |
| Destination Port | Pelabuhan tujuan (e.g. Lahad Datu) |
| Estimated Duration | Perkiraan durasi perjalanan |
| Active | Status aktif/nonaktif rute |

**Cara tambah rute baru:**
1. Klik **Create**
2. Isi asal, tujuan, durasi
3. Set Active = true
4. Klik **Save**

---

#### Schedules
**Navigasi:** Operational → Schedules

Fungsi: Mengelola jadwal keberangkatan kapal.

| Field | Keterangan |
|-------|------------|
| Active | Status aktif jadwal |
| Status | Status jadwal (scheduled / cancelled / completed) |
| Vessel | Pilih kapal dari daftar |
| Route | Pilih rute (format: asal → tujuan) |
| Departure Time | Tanggal & jam keberangkatan |
| Arrival Time | Tanggal & jam kedatangan |
| VIP Price | Harga tiket VIP (MYR) |
| Regular Price | Harga tiket Regular (MYR) |
| Age Category Pricing | Harga khusus per kategori umur (opsional) |

**Cara tambah jadwal baru:**
1. Klik **Create**
2. Pilih kapal, rute, atur waktu & harga
3. Jika perlu, tambahkan harga khusus per kategori umur (misal: Infant lebih murah)
4. Klik **Save**

**Cara lihat daftar penumpang per jadwal:**
1. Buka daftar Schedules
2. Klik nama jadwal
3. Scroll ke bagian bawah — ada tombol **Passengers**
4. Dari halaman passengers bisa **Export PDF** atau **Export Excel**

---

#### Boarding Logs
**Navigasi:** Operational → Boarding Logs

Fungsi: Melihat riwayat scan boarding.

| Kolom | Keterangan |
|-------|------------|
| Ticket | Nomor tiket |
| Passenger | Nama penumpang |
| Validated By | Petugas yang melakukan scan |
| Result | Hasil validasi (Valid / Already Used / Expired / Invalid) |
| Scan Method | Metode scan (QR / Manual) |
| Validated At | Waktu scan |

> Menu ini bersifat **read-only** untuk melihat riwayat. Tidak bisa tambah/edit data.

---

### 3.2.2 Grup: 💳 Transactions

---

#### Pembayaran
**Navigasi:** Transactions → Pembayaran

Fungsi: Memproses pembayaran yang masuk (khusus pembayaran manual/upload bukti transfer).

**Daftar Pembayaran:**
| Kolom | Keterangan |
|-------|------------|
| Kode Booking | Kode booking customer |
| Pembeli | Nama pembeli (atau "COUNTER" jika dari loket) |
| Rute | Asal → Tujuan |
| Jumlah | Total pembayaran |
| Bukti | Ada/tidaknya bukti transfer |
| Status | Status pembayaran |
| Tanggal | Tanggal pembayaran dibuat |

**Filter Status**: Bisa filter berdasarkan status (Awaiting Approval, Approved, Rejected, dll)

**Action untuk pembayaran dengan status Awaiting Approval:**

| Aksi | Cara | Hasil |
|------|------|-------|
| **Lihat Bukti** | Klik tombol 👁 (mata) | Muncul modal dengan gambar bukti transfer |
| **ACC (Setujui)** | Klik tombol hijau ✓ → Konfirmasi | Tiket otomatis terbit untuk semua penumpang |
| **Tolak** | Klik tombol merah ✕ → Isi alasan | Status jadi Rejected, buyer bisa upload ulang |

> **Untuk pembayaran via ToyibPay**: Tidak perlu diproses di sini karena sudah otomatis terverifikasi.

---

#### Refunds
**Navigasi:** Transactions → Refunds

Fungsi: Melihat daftar refund yang diajukan.

| Kolom | Keterangan |
|-------|------------|
| Booking | Kode booking |
| Amount | Jumlah refund |
| Status | Status refund (requested / processed / rejected) |
| Reason | Alasan refund |
| Processed By | Admin yang memproses |

> Menu ini bersifat **read-only** untuk admin. Proses refund dilakukan melalui halaman booking di website.

---

### 3.2.3 Grup: 📊 Analytics

---

#### Reports
**Navigasi:** Analytics → Reports

Fungsi: Dashboard laporan & analitik real-time.

**Komponen Dashboard:**
| Komponen | Keterangan |
|----------|------------|
| KPI Cards | Total penumpang, total pendapatan, okupansi, pending payment, refund |
| Revenue Chart | Grafik tren pendapatan (7 hari / 30 hari) |
| Booking Trend Chart | Grafik tren booking |
| Schedule Table | Tabel detail per jadwal |

**Table Detail Per Jadwal:**
| Kolom | Keterangan |
|-------|------------|
| Jadwal | Kapal & rute |
| Keberangkatan | Waktu keberangkatan |
| Kapasitas | Total kursi |
| Booked | Kursi sudah dipesan |
| Paid | Kursi sudah dibayar |
| Boarded | Penumpang sudah boarding |
| Revenue | Pendapatan |
| Occupancy | Persentase okupansi |

**Filter:**
- Pilih jadwal spesifik
- Filter status pembayaran
- Filter tanggal

**Export:**
- Klik **Export CSV** untuk download laporan dalam format CSV

---

### 3.2.4 Grup: ⚙️ Booking Settings

---

#### Age Categories
**Navigasi:** Booking Settings → Age Categories

Fungsi: Mengelola kategori umur penumpang.

| Field | Keterangan |
|-------|------------|
| Name | Nama kategori (e.g. Infant, Child, Adult) |
| Min Age | Umur minimal (inklusif) |
| Max Age | Umur maksimal (inklusif) |
| Sort Order | Urutan tampil (angka kecil = muncul duluan) |
| Is Active | Status aktif |

**Contoh konfigurasi:**
| Kategori | Min | Max |
|----------|-----|-----|
| Infant | 0 | 2 |
| Child | 3 | 12 |
| Adult | 13 | 150 |

> Kategori ini digunakan untuk menentukan harga otomatis berdasarkan umur penumpang.

**Cara tambah kategori baru:**
1. Klik **Create**
2. Isi nama, umur minimal & maksimal
3. Atur sort order
4. Klik **Save**

---

#### Promos
**Navigasi:** Booking Settings → Promos

Fungsi: Mengelola promo diskon tiket.

| Field | Keterangan |
|-------|------------|
| Name | Nama promo (e.g. "Lebaran Diskon") |
| Code | Kode promo (bisa diketik buyer) |
| Type | Tipe diskon: `percentage` (persen) atau `fixed` (nominal tetap) |
| Value | Nilai diskon (contoh: 10 untuk 10% atau 50 untuk RM 50) |
| Start Date | Tanggal mulai berlaku |
| End Date | Tanggal berakhir |
| Usage Quota | Kuota maksimal pemakaian |
| Used Count | Jumlah sudah terpakai (otomatis) |
| Route | Batasi ke rute tertentu (kosongkan untuk semua rute) |
| Ticket Class | Kelas tiket (all / vip / regular) |
| Is Active | Status aktif |
| Auto Apply | Jika ON, promo otomatis diterapkan tanpa perlu kode |
| Min Passengers | Minimal jumlah penumpang (opsional) |
| Max Passengers | Maksimal jumlah penumpang (opsional) |

**Cara tambah promo baru:**
1. Klik **Create**
2. Isi nama, pilih tipe (percentage/fixed), masukkan nilai
3. Atur periode berlaku, kuota
4. Set Auto Apply jika ingin otomatis
5. Klik **Save**

---

### 3.2.5 Grup: 🔐 Admin

---

#### Users
**Navigasi:** Admin → Users

Fungsi: Mengelola semua akun pengguna.

**Daftar User:**
| Kolom | Keterangan |
|-------|------------|
| Name | Nama lengkap |
| Email | Alamat email (digunakan untuk login) |
| Phone | Nomor telepon |
| Nationality | Kewarganegaraan |
| Passport Number | Nomor passport |
| Birth Date | Tanggal lahir |
| Gender | Jenis kelamin |
| Is Active | Status aktif akun |

**Cara tambah user baru:**
1. Klik **Create**
2. Isi **Name**, **Email**, **Password**
3. Data tambahan: Phone, Nationality, Passport Number, Birth Date, Gender
4. Set **Is Active** = true
5. Klik **Save**

**Cara ganti password user (termasuk admin, counter, boarding):**
1. Buka menu **Users**
2. Cari user yang ingin diganti passwordnya
3. Klik ikon **Edit** (pensil)
4. Pada field **Password**, ketik password baru
5. Klik **Save**
6. Password baru akan langsung aktif — user bisa login dengan password tersebut

> **Catatan**: Untuk mengganti password akun sendiri (admin yang sedang login), tidak bisa dari sini. Buka **Profile** di pojok kanan atas (ikon user) → klik nama → ubah password.

**Cara ganti role user:**
1. Buka menu **Users**
2. Klik **Edit** pada user yang ingin diubah rolenya
3. Di bagian **Roles**, centang/ubah role yang diinginkan:
   - `admin` — Akses penuh ke Filament + website
   - `ticket_counter_officer` — Akses ke halaman counter
   - `boarding_officer` — Akses ke halaman boarding
   - `deportation_officer` — Akses ke halaman deportasi
4. Klik **Save**

> Seorang user bisa memiliki **lebih dari satu role** sekaligus.

---

#### Notifications
**Navigasi:** Admin → Notifications

Fungsi: Mengirim notifikasi ke user.

| Field | Keterangan |
|-------|------------|
| User | Penerima notifikasi |
| Type | Tipe notifikasi (payment_success, booking_reminder, dll) |
| Title | Judul notifikasi |
| Body | Isi notifikasi |
| Channel | Channel pengiriman (database) |

**Cara kirim notifikasi:**
1. Klik **Create**
2. Pilih user penerima, isi title & body
3. Klik **Save**
4. Notifikasi akan muncul di halaman notifikasi user tersebut

---

#### Audit Logs
**Navigasi:** Admin → Audit Logs

Fungsi: Melihat riwayat aktivitas penting dalam sistem.

| Kolom | Keterangan |
|-------|------------|
| User | User yang melakukan aksi |
| Action | Tipe aksi (toyibpay_callback, payment_approved, dll) |
| Entity Type | Tipe entitas (booking, payment, dll) |
| Entity ID | ID entitas |
| IP Address | Alamat IP |
| Created At | Waktu aksi |

> Menu ini **read-only**. Berguna untuk troubleshooting dan audit keamanan.

---

### 3.3 Cara Ganti Password Akun Sendiri (Admin)

1. Di panel admin (Filament), klik **ikon profil** di pojok kanan atas
2. Klik nama akun Anda
3. Klik tombol **Edit Profile**
4. Masukkan password baru
5. Klik **Save**

### 3.4 Cara Membuat Akun untuk Staff (Counter / Boarding)

1. Buka menu **Admin → Users**
2. Klik **Create**
3. Isi data:
   - Name: Nama staff
   - Email: Email staff (untuk login)
   - Password: Buat password sementara
   - Is Active: Centang
4. Klik **Save**
5. Setelah user terbuat, klik **Edit** pada user tersebut
6. Di bagian **Roles**, centang role yang sesuai:
   - Untuk counter staff → centang `ticket_counter_officer`
   - Untuk boarding staff → centang `boarding_officer`
7. Klik **Save**
8. Beri tahu staff email & password yang dibuat — mereka bisa login di `https://auralis8.com/login`

---

## 4. Peran Counter Officer

### 4.1 Akses
- Login di website publik `https://auralis8.com/login`
- Gunakan akun dengan role **ticket_counter_officer**

### 4.2 Tugas
Melayani **pembelian tiket offline** (buyer datang ke loket):

#### A. Buat Booking Baru
1. Buka halaman **Counter Dashboard** (`/counter`)
2. Cari jadwal menggunakan form pencarian (filter route)
3. Klik **Book** pada jadwal yang dipilih
4. Isi data penumpang (nama, gender, tgl lahir, nationality, passport/ID, phone)
5. Pilih kelas (**VIP** / **Regular**)
6. Sistem akan menampilkan ringkasan harga & total

#### B. Proses Pembayaran Tunai
1. Masukkan **jumlah uang yang diterima** dari buyer
2. Sistem otomatis menghitung **kembalian**
3. Pilih metode pembayaran (**Cash** / **Card**)
4. Klik **Confirm Payment**
5. Tiket langsung terbit — buyer langsung dapat tiket

#### C. Riwayat & Detail Booking
- Buka **History** (`/counter/history`) untuk lihat semua booking yang dibuat
- Klik **Detail** untuk lihat info booking & tiket
- Bisa **Cetak Tiket** ulang jika diperlukan

#### D. Refund
- Di halaman detail booking, klik **Refund** untuk memproses refund
- Hanya untuk booking yang sudah **paid**

---

## 5. Peran Boarding Officer

### 5.1 Akses
- Login di website publik `https://auralis8.com/login`
- Gunakan akun dengan role **boarding_officer**

### 5.2 Tugas
Melakukan **validasi boarding** penumpang sebelum naik kapal:

#### A. Boarding Scanner
1. Buka halaman **Boarding Scanner** (`/boarding/scanner`)
2. Scan **QR code tiket** buyer menggunakan kamera
3. Sistem akan menampilkan hasil:
   - 🟢 **Valid**: Data penumpang muncul → Boarding disetujui
   - 🟡 **Already Used**: Tiket sudah dipakai sebelumnya
   - 🟠 **Expired**: Tiket sudah expired
   - 🔴 **Invalid**: Tiket tidak valid
4. Bisa juga manual input dengan **ticket number** atau **booking code**

#### B. Boarding Manifest
1. Buka halaman **Manifest** (`/boarding/manifest/{schedule_id}`)
2. Pilih jadwal dari dropdown
3. Lihat daftar penumpang untuk jadwal tertentu
4. Cek status boarding masing-masing penumpang

---

## 6. Status-status Penting

### Booking Status
| Status | Arti |
|--------|------|
| Pending Payment | Belum bayar, masih dalam proses |
| Awaiting Approval | Upload bukti transfer (manual), menunggu ACC admin |
| Paid | Lunas, tiket aktif |
| Completed | Penumpang sudah boarding |
| Cancelled | Dibatalkan |
| Expired | Kedaluwarsa (waktu habis / 30 menit) |
| Refunded | Sudah direfund |

### Payment Status
| Status | Arti |
|--------|------|
| Pending | Belum dibayar |
| Awaiting Approval | Upload bukti (manual), butuh ACC admin |
| Approved | Disetujui admin |
| Paid | Lunas (via ToyibPay atau tunai di counter) |
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

1. **Pembayaran Online**: Buyer membayar via **ToyibPay** (QRIS, VA, Convenience Store, dll). Tiket terbit otomatis setelah pembayaran sukses — tidak perlu ACC admin.
2. **Pembayaran Manual**: Jika ToyibPay gagal, fallback ke upload bukti transfer — admin harus ACC manual melalui menu **Pembayaran**.
3. **Pembayaran Counter**: Counter officer menerima tunai/card, tiket langsung terbit — tidak perlu ACC admin.
4. **Kapasitas VIP**: Jika `vip_capacity = 0` pada kapal, opsi VIP tidak akan muncul di form booking.
5. **Booking Expired**: Booking otomatis expired setelah **30 menit** jika tidak dibayar. Pastikan cron job berjalan:
   ```
   * * * * * cd /home/.../auralis8.com && php artisan schedule:run
   ```
6. **Storage**: Semua file upload (passport, travel permit, bukti transfer) tersimpan di `storage/app/public/`.
7. **Keamanan**: Hanya **admin** yang bisa akses panel Filament. Boarding officer & counter officer tidak bisa akses admin panel.

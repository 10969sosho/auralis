# Ship Ticketing

International Ferry Booking System — Malaysia ↔ Philippines (Bongao, Tawi-Tawi ↔ Lahad Datu, Sabah)

---

## Daftar Role & Akun

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@shipticketing.com | password |
| Boarding Officer | boarding@shipticketing.com | password |
| Ticket Counter | counter@shipticketing.com | password |
| Deportation Officer | deportation@shipticketing.com | password |
| Passenger | (registrasi sendiri) | (buat sendiri) |

---

## 1. Alur Pembeli (Passenger)

### 1.1 Registrasi & Login
1. Buka halaman `/register` → isi nama, email, password
2. Role otomatis jadi **passenger**
3. Login di `/login` → redirect ke home

### 1.2 Cari Jadwal
1. Di halaman home, klik **Search Schedule**
2. Filter: pelabuhan asal, pelabuhan tujuan, tanggal, jumlah penumpang
3. Sistem tampilkan jadwal yang tersedia beserta harga VIP & Regular
4. Promo otomatis (`auto_apply`) langsung terlihat di hasil pencarian

### 1.3 Booking Tiket
1. Pilih jadwal → klik **Book Now**
2. Isi data penumpang (maks 8 orang per booking):
   - Nama lengkap, gender, tanggal lahir, kewarganegaraan, nomor paspor
   - Upload file passport (PDF/JPG/PNG, max 5MB)
   - Upload travel permit (opsional)
   - Pilih kelas: **VIP** atau **Regular**
3. Bisa input **kode promo** jika punya
4. Sistem hitung total + diskon promo
5. Klik booking → data tersimpan dengan status **pending_payment**
6. Booking kadaluarsa dalam **30 menit** jika tidak dibayar

### 1.4 Pembayaran
1. Redirect ke halaman payment
2. Metode pembayaran: **FPX**, **EWallet**, **Online Banking**
3. Klik bayar → status booking jadi **paid**
4. Tiket QR otomatis digenerate untuk setiap penumpang
5. Notifikasi masuk ke dashboard

### 1.5 Download E-Tiket
1. Buka halaman **My Bookings** → lihat daftar booking
2. Klik booking → detail penumpang + QR code masing-masing
3. Klik **Download PDF** untuk cetak e-tiket
4. QR code berisi data terenkripsi (ticket_id, booking_code, token)

### 1.6 Request Refund (jika batal)
1. Buka detail booking yang sudah **paid**
2. Syarat: masih **H-6 sebelum keberangkatan**
3. Masukkan alasan pembatalan
4. Sistem potong **75%** (refund 25% dari total)
5. Status berubah ke **refund_requested**
6. Admin akan approve/reject refund

### 1.7 Notifikasi
1. Lihat notifikasi di menu **Notifications**
2. Notifikasi otomatis untuk: booking success, refund status, pengumuman
3. Bisa mark as read / mark all read

---

## 2. Alur Petugas Boarding (Boarding Officer)

### 2.1 Scan QR Boarding
1. Login → menu **Boarding** (sidebar)
2. Buka halaman scanner → kamera aktif
3. Scan QR code dari e-tiket penumpang
4. Sistem validasi:
   - ✅ **Valid** → tiket aktif → boarding sukses
   - ⚠️ **Used** → tiket sudah dipakai
   - ⚠️ **Expired** → tiket kadaluarsa / boarding sudah tutup
   - ❌ **Cancelled** → booking dibatalkan
   - ❌ **Refunded** → tiket sudah direfund
5. Setiap scan tercatat di **Boarding Logs**

### 2.2 Manual Validasi (jika QR rusak)
1. Masukkan **booking code** manual
2. Sistem cari tiket terkait
3. Validasi sama seperti scan QR

### 2.3 Lihat Manifes
1. Pilih jadwal → lihat daftar penumpang yang sudah boarding
2. Filter berdasarkan status boarding

---

## 3. Alur Petugas Deportasi (Deportation Officer)

### 3.1 Buat Manifes Deportasi
1. Login → menu **Deportation**
2. Klik **Create New Manifest**
3. Pilih jadwal kapal
4. Sistem generate **manifest code** otomatis
5. Tambahkan penumpang deportasi satu per satu:
   - Nama, gender, kewarganegaraan, nomor paspor
   - QR token digenerate otomatis

### 3.2 Boarding Deportasi
1. Buka manifest → lihat daftar penumpang
2. Scan QR deportasi → validasi identitas
3. Status penumpang berubah ke **boarded**
4. Manifes tercetak untuk laporan

---

## 4. Alur Petugas Counter (Ticket Counter Officer)

### 4.1 Buat Booking Manual
1. Membantu penumpang yang tidak bisa akses online
2. Input data penumpang langsung dari counter
3. Upload dokumen passport/travel permit

### 4.2 Terima Pembayaran Tunai
1. Proses pembayaran cash di counter
2. Generate QR tiket untuk penumpang
3. Cetak e-tiket fisik jika diperlukan

---

## 5. Alur Admin

### 5.1 Manajemen Pengguna
1. Akses panel admin lewat menu **Admin** atau `/admin`
2. Kelola semua user: lihat, edit, hapus, ganti role
3. Role tersedia: passenger, boarding_officer, ticket_counter_officer, deportation_officer, admin

### 5.2 Manajemen Kapal (Vessels)
1. CRUD data kapal:
   - Nama kapal
   - Kapasitas VIP & Regular
   - Status aktif/nonaktif

### 5.3 Manajemen Rute (Routes)
1. CRUD data rute:
   - Pelabuhan asal & tujuan
   - Status aktif/nonaktif

### 5.4 Manajemen Jadwal (Schedules)
1. CRUD jadwal keberangkatan:
   - Pilih kapal & rute
   - Waktu berangkat & tiba
   - Harga VIP & Regular
   - Status: scheduled, departed, cancelled, completed
2. Tracking sisa kursi otomatis dari booking

### 5.5 Manajemen Promo
1. CRUD promo/diskon:
   - Tipe: **percentage** (persen) atau **fixed_amount** (nominal tetap)
   - Periode berlaku (start_date – end_date)
   - Kuota pemakaian
   - Filter: rute tertentu, kelas tiket, min/max penumpang
   - Auto-apply (langsung muncul di pencarian) / manual (pakai kode)

### 5.6 Manajemen Booking
1. Lihat semua booking
2. Filter berdasarkan status: pending, paid, used, expired, cancelled, refund_requested, refunded
3. Cari berdasarkan booking code atau user

### 5.7 Approve/Reject Refund
1. Lihat daftar refund yang **requested**
2. **Approve** → dana dikembalikan, booking status jadi **refunded**, tiket jadi **refunded**
3. **Reject** → booking balik ke **paid**, penumpang tetap bisa naik

### 5.8 Laporan & Audit
1. Lihat **Audit Logs** — semua aktivitas tercatat (siapa, aksi, entity, IP address, timestamp)
2. Export manifes boarding/deportasi
3. Lihat laporan penjualan per periode

### 5.9 Broadcast Notifikasi
1. Kirim notifikasi massal ke semua penumpang
2. Tipe: pengumuman jadwal, promo, perubahan rute

---

## Diagram Alur Sederhana

```
REGISTRASI → CARI JADWAL → ISI DATA PENUMPANG → UPLOAD DOKUMEN
                                                    ↓
                                              PEMBAYARAN (FPX/EWallet/Banking)
                                                    ↓
                                              E-TIKET + QR CODE
                                                    ↓
                              ┌───────────────────┴───────────────────┐
                              ↓                                       ↓
                        BOARDING (scan QR)                     REFUND (H-6)
                              ↓                                       ↓
                        CHECK-IN SUKSES                       APPROVED / REJECTED

```

## Relasi Database (Ringkasan)

```
User ──hasMany── Booking ──belongsTo── Schedule ──belongsTo── Vessel
                            │                        └belongsTo── Route
                            ├──hasMany── BookingPassenger ──hasOne── Ticket
                            │                              └──hasMany── Document
                            ├──hasOne── Payment
                            ├──hasOne── Refund
                            └──belongsTo── Promo

Ticket ──hasMany── BoardingLog

Schedule ──hasMany── DeportationManifest ──hasMany── DeportationPassenger
```

---

## Status Kelengkapan Fitur

| Fitur | Status |
|-------|--------|
| Registrasi & Login | ✅ Selesai |
| Cari Jadwal + Filter | ✅ Selesai |
| Booking Tiket + Upload Dokumen | ✅ Selesai |
| Pembayaran (FPX/EWallet/Banking) | ✅ Selesai |
| Generate QR Tiket | ✅ Selesai |
| Download PDF E-Tiket | ✅ Selesai |
| Request Refund (H-6) | ✅ Selesai |
| Notifikasi | ✅ Selesai |
| Boarding Scanner QR + Manual | ✅ Selesai |
| Boarding Logs | ✅ Selesai |
| Manifes Boarding | ✅ Selesai |
| Deportasi (Manifes + QR + Boarding) | ✅ Selesai |
| **Admin Panel (Filament):** | |
| └ CRUD Vessels | ✅ Selesai |
| └ CRUD Routes | ✅ Selesai |
| └ CRUD Schedules | ✅ Selesai |
| └ CRUD Promos | ✅ Selesai |
| └ CRUD Bookings | ✅ Selesai |
| └ CRUD Users + Role | ✅ Selesai |
| └ Approve/Reject Refund | ✅ Selesai |
| └ CRUD Notifications | ✅ Selesai |
| └ Audit Logs | ✅ Selesai |
| └ CRUD Boarding Logs | ✅ Selesai |
| └ CRUD Deportation Manifests | ✅ Selesai |

---

# Simulasi Studi Kasus — Testing Lengkap

## Role & Pembagian Tugas Testing

| No | Nama Role | Test Case # | Skenario |
|----|-----------|-------------|----------|
| **A** | **Admin** (orang 1) | TC-01 s.d TC-09 | Setup data master + Kelola sistem |
| **B** | **Passenger 1** (orang 2) | TC-10 s.d TC-17 | Booking penuh + bayar + refund |
| **C** | **Passenger 2** (orang 3) | TC-18 s.d TC-22 | Booking + bayar + boarding |
| **D** | **Boarding Officer** (orang 4) | TC-23 s.d TC-26 | Scan QR boarding + validasi |
| **E** | **Deportation Officer** (orang 5) | TC-27 s.d TC-30 | Buat manifes deportasi + boarding |
| **F** | **Admin** (orang 1) | TC-31 s.d TC-36 | Approve refund + audit + notifikasi |
| **G** | **Semua** | TC-37 | Uji coba bebas (exploratory) |

---

## Urutan Testing (WAJIB berurutan!)

### 🅰️ Admin — Setup Data Master (TC-01 s.d TC-09)

**Login:** `admin@shipticketing.com` / `password` → buka `/admin`

| TC | Langkah | Expected Result |
|----|---------|----------------|
| **TC-01** | Buka menu **Vessels** → klik **Create** → isi: `name="Auralis 8"`, `vip_capacity=20`, `regular_capacity=80`, `free_baggage=10` → save | Vessel tersimpan, muncul di list |
| **TC-02** | Buka menu **Routes** → klik **Create** → isi: `origin_port="Bongao, Tawi-Tawi"`, `destination_port="Lahad Datu, Sabah"`, `active=true` → save | Route tersimpan |
| **TC-03** | Buat route kedua: `origin_port="Lahad Datu, Sabah"`, `destination_port="Bongao, Tawi-Tawi"` | Route kedua tersimpan |
| **TC-04** | Buka menu **Schedules** → klik **Create** → pilih vessel Auralis 8, route Bongao→Lahad Datu, departure besok jam 08:00, arrival besok jam 12:00, `vip_price=350000`, `regular_price=150000` → save | Schedule tersimpan |
| **TC-05** | Buat schedule kedua: besok jam 14:00–18:00, harga sama | Schedule kedua tersimpan |
| **TC-06** | Buka menu **Promos** → klik **Create** → `name="Diskon Lebaran"`, `code="LEBARAN25"`, `type=percentage`, `value=25`, `start_date=hari ini`, `end_date=+7 hari`, `usage_quota=100`, `auto_apply=false` → save | Promo tersimpan |
| **TC-07** | Buat promo auto-apply: `name="Welcome 10%"`, `type=percentage`, `value=10`, `auto_apply=true`, `usage_quota=50` → save | Promo auto-apply aktif |
| **TC-08** | Buka menu **Users** → lihat semua user yang sudah ada (admin, boarding, counter, deportation) | 4 user terdaftar |
| **TC-09** | Buka menu **Audit Logs** → lihat riwayat aktivitas admin | Semua aktivitas TC-01~08 tercatat |

---

### 🅱️ Passenger 1 — Booking + Bayar + Refund (TC-10 s.d TC-17)

**Registrasi:** buka `/register` → isi: `name="Ali Bin Ahmad"`, `email="ali@test.com"`, `password=password123`

| TC | Langkah | Expected Result |
|----|---------|----------------|
| **TC-10** | Di halaman home, klik **Search Schedule** → cari tanggal besok | Jadwal Auralis 8 muncul |
| **TC-11** | Klik **Book Now** pada schedule pagi (08:00) | Halaman booking terbuka, ada harga VIP & Regular |
| **TC-12** | Isi 2 penumpang: (1) dewasa VIP, (2) dewasa Regular — upload file passport (PDF) untuk masing-masing. Masukkan kode promo `LEBARAN25` → klik Book | Booking sukses, redirect ke payment. Total harga: 500.000 - 25% = 375.000 |
| **TC-13** | Di halaman payment, pilih metode **FPX** → klik Bayar | Payment sukses, redirect ke halaman success. Status booking = **paid** |
| **TC-14** | Klik **Download PDF** untuk e-tiket masing-masing penumpang | File PDF terdownload, QR code muncul |
| **TC-15** | Buka menu **My Bookings** → lihat booking yang baru | Booking status = paid, ada 2 tiket |
| **TC-16** | Klik detail booking → klik **Request Refund** → isi alasan "Batal berangkat" → submit | Refund status = **requested**. Booking status = **refund_requested** |
| **TC-17** | Cek menu **Notifications** → ada notifikasi booking success? | Notifikasi booking success masuk |

---

### 🅲 Passenger 2 — Booking + Bayar + Boarding (TC-18 s.d TC-22)

**Registrasi:** buka `/register` → isi: `name="Siti Nurhaliza"`, `email="siti@test.com"`, `password=password123`

| TC | Langkah | Expected Result |
|----|---------|----------------|
| **TC-18** | Cari schedule pagi (08:00) besok → Book untuk 1 penumpang dewasa **Regular** kelas. Upload passport & travel permit | Booking sukses |
| **TC-19** | Bayar pakai **EWallet** | Payment sukses, status = **paid** |
| **TC-20** | Download PDF tiket | PDF dengan QR code terdownload |
| **TC-21** | Cari schedule **sore** (14:00) besok → Book 3 penumpang: 1 VIP + 2 Regular. Upload passport masing-masing. **Jangan gunakan promo** | Booking sukses, total = 350.000 + (2×150.000) = 650.000 |
| **TC-22** | Bayar pakai **Online Banking** | Payment sukses. Booking status = **paid**, 3 tiket tergenerate |

---

### 🅳 Boarding Officer — Scan QR Boarding (TC-23 s.d TC-26)

**Login:** `boarding@shipticketing.com` / `password`

| TC | Langkah | Expected Result |
|----|---------|----------------|
| **TC-23** | Buka menu **Boarding** → **Scanner** → scan QR code tiket Siti (Passenger 2, TC-20) | Status: **✅ Valid**. Boarding sukses. Tiket status jadi **used** |
| **TC-24** | Scan **ulang** QR code yang sama | Status: **⚠️ Used** — tiket sudah dipakai |
| **TC-25** | Buka menu **Boarding** → **Manifest** → pilih schedule pagi (08:00) | Manifes menunjukkan Siti sudah boarding |
| **TC-26** | Coba **Manual Input** → masukkan booking code Passenger 2 yang **sore** (TC-21) → validate | Status: **✅ Valid** — tiket aktif |

---

### 🅴 Deportation Officer — Manifes Deportasi (TC-27 s.d TC-30)

**Login:** `deportation@shipticketing.com` / `password`

| TC | Langkah | Expected Result |
|----|---------|----------------|
| **TC-27** | Buka menu **Deportation** → **Create New Manifest** → pilih schedule pagi (08:00) → create | Manifest tergenerate dengan kode unik |
| **TC-28** | Klik manifest → **Add Passenger** → isi: `name="John Doe"`, `gender=male`, `nationality="Filipina"`, `passport_number="PH123456"` | Penumpang deportasi tersimpan, QR token digenerate |
| **TC-29** | Tambah 1 penumpang lagi: `name="Jane Doe"`, `gender=female`, `nationality="Filipina"`, `passport_number="PH789012"` | Penumpang kedua tersimpan |
| **TC-30** | Scan QR deportasi penumpang pertama | Status: **✅ board**. Status penumpang = **boarded** |

---

### 🅵 Admin — Approve Refund + Audit + Notifikasi (TC-31 s.d TC-36)

**Login:** `admin@shipticketing.com` / `password` → buka `/admin`

| TC | Langkah | Expected Result |
|----|---------|----------------|
| **TC-31** | Buka menu **Refunds** → lihat daftar refund | Refund dari Ali (Passenger 1) muncul dengan status **requested** |
| **TC-32** | Klik refund Ali → klik **Approve** | Refund status = **processed**. Booking status Ali = **refunded**. Tiket Ali = **refunded** |
| **TC-33** | Buka menu **Notifications** → **Create** → pilih user Ali, isi title="Refund Approved", body="Refund Anda telah diproses" → save | Notification terkirim ke Ali |
| **TC-34** | Buka menu **Bookings** → filter status = **refunded** | Booking Ali muncul dengan status refunded |
| **TC-35** | Buka menu **Audit Logs** → filter aksi = approve refund | Log approve refund tercatat |
| **TC-36** | Buka menu **Notifications** → **Create** → kirim broadcast ke semua user: title="Jadwal Update", body="Besok ada tambahan jadwal" | Semua user dapat notifikasi |

---

### 🅶 Exploratory Testing — Bebas (TC-37)

**Semua role login & coba skenario bebas:**

| Role | Coba lakukan |
|------|-------------|
| Admin | Edit vessel/routes/schedules, nonaktifkan schedule, coba delete data |
| Passenger | Coba booking tanpa upload passport (harus error), coba booking > 8 penumpang (harus error), coba booking expired (30 menit) |
| Boarding Officer | Coba scan QR deportasi di boarding scanner (harus invalid), coba manual input booking code palsu |
| Deportation Officer | Coba scan QR tiket biasa di scanner deportasi (harus invalid) |
| Ticket Counter | Login sebagai counter@shipticketing.com, coba akses menu yang tersedia |

---

## Ringkasan Role & Tanggung Jawab

| Role | Tanggung Jawab |
|------|---------------|
| **Admin** (1 orang) | Setup data master (vessel, route, schedule, promo), kelola user, approve/reject refund, lihat audit log, kirim broadcast notifikasi |
| **Passenger** (2+ orang) | Registrasi, cari jadwal, booking, upload dokumen, bayar, download e-tiket, request refund, cek notifikasi |
| **Boarding Officer** (1 orang) | Scan QR boarding, validasi manual, lihat manifes boarding |
| **Deportation Officer** (1 orang) | Buat manifes deportasi, tambah penumpang deportasi, scan boarding deportasi |
| **Ticket Counter Officer** (1 orang) | Bantu booking manual, terima pembayaran cash, cetak tiket |

### Flow Antar Role

```
Admin setup data ──→ Passenger booking ──→ Passenger bayar
                                              ↓
                           ┌──────────────────┴──────────────────┐
                           ↓                                     ↓
              Boarding Officer scan QR                  Admin approve/reject refund
              (saat hari H)                             (jika ada request)
                           ↓
                   Check-in sukses

Deportation Officer ──→ Buat manifes ──→ Scan QR deportasi ──→ Boarding deportasi
(terpisah dari booking biasa)
```

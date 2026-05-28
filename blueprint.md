# 01-product-vision.md

## Product Overview

Sistem ini adalah platform booking tiket kapal internasional antara Malaysia dan Filipina.

Fokus utama sistem:

* Booking tiket online
* Boarding QR validation
* Operational management
* Refund management
* Promo pricing
* Multi-role operational workflow
* Deportation passenger handling

Sistem dirancang untuk:

* Mobile-first usage
* Operasional pelabuhan real-time
* High concurrent booking
* Maintenance sederhana
* Deployment ringan tanpa dependency frontend kompleks

### System Language

Semua UI dan konten sistem menggunakan **English**.

---

## Product Goals

### Primary Goals

1. Mempermudah booking tiket kapal internasional
2. Mengurangi proses manual operasional pelabuhan
3. Mempercepat boarding validation
4. Mengurangi fraud tiket duplicate
5. Menyediakan monitoring operasional real-time
6. Menyediakan sistem refund terkontrol
7. Menyediakan sistem deportasi terpisah dari passenger normal

---

## Target Users

| User Type              | Description                          |
| ---------------------- | ------------------------------------ |
| Passenger              | Penumpang umum Malaysia ↔ Filipina   |
| Admin                  | Pengelola seluruh operasional sistem |
| Boarding Officer       | Petugas validasi boarding            |
| Ticket Counter Officer | Petugas loket offline                |
| Deportation Officer    | Petugas deportasi                    |

---

## Main Route

| Origin                          | Destination                     |
| ------------------------------- | ------------------------------- |
| Bongao, Tawi-Tawi (Philippines) | Lahad Datu, Sabah (Malaysia)    |
| Lahad Datu, Sabah (Malaysia)    | Bongao, Tawi-Tawi (Philippines) |

---

## Vessel Information

| Field          | Value          |
| -------------- | -------------- |
| Vessel Name    | Auralis 8      |
| Capacity       | 280 passengers |
| Ticket Classes | VIP, Regular   |
| Free Baggage   | 10kg           |

### Seat Quota

| Class   | Quota |
| ------- | ----- |
| VIP     | 40    |
| Regular | 240   |

### VIP Benefits

* Kursi lebih nyaman
* Area prioritas
* Kuota terbatas

### Regular

* Kursi standar

---

## Core Business Characteristics

### International Travel

Sistem harus mendukung:

* Passenger identity validation
* Passport handling
* Immigration operational workflow
* International passenger manifest

### Boarding Validation

Boarding wajib menggunakan:

* Dynamic QR validation
* Real-time ticket status checking
* Anti-duplicate scan validation

### Refund Management

Refund:

* Manual approval
* Maksimal H-6 jam
* Refund amount 25%

### Deportation Handling

Deportation passenger:

* Tidak membutuhkan payment
* Tidak membutuhkan booking normal
* Memiliki manifest khusus terpisah dari manifest regular
* Tetap masuk manifest kapal
* Dipisahkan dari passenger regular
* Tidak melalui payment flow
* Tetap menggunakan QR validation internal untuk boarding

### Deportation Boarding Flow

```text
CREATE DEPORTATION MANIFEST → ADD PASSENGER → VALIDATE IDENTITY → GENERATE INTERNAL QR → BOARDING WITH QR
```

---

## Operational Goals

| Goal                   | Target       |
| ---------------------- | ------------ |
| Booking Speed          | < 3 menit    |
| QR Validation          | < 2 detik    |
| Concurrent Booking     | Stable       |
| Mobile Compatibility   | Full support |
| Overbooking Prevention | Mandatory    |
| Payment Sync           | Real-time    |

---

## Technical Philosophy

### Backend Focused Architecture

Sistem menggunakan:

* Laravel monolith architecture
* Blade rendering
* Minimal frontend complexity
* Filament admin ecosystem

### Simplicity First

Menghindari:

* SPA complexity
* Node.js runtime dependency production
* Heavy frontend framework

Fokus:

* Stability
* Maintainability
* Shared hosting compatibility
* Fast deployment

---

## Success Indicators

Sistem dianggap sukses apabila:

1. User dapat booking tanpa bantuan manual
2. Boarding berjalan cepat
3. Tidak terjadi duplicate ticket usage
4. Tidak terjadi overbooking
5. Refund berjalan sesuai policy
6. Operasional admin berjalan realtime
7. Ticket validation stabil pada high traffic

# 02-user-roles-rbac.md

## User Roles

Sistem memiliki 5 role utama.

---

## 1. Customer / Passenger

### Description

User umum yang melakukan booking tiket kapal.

### Permissions

| Permission           | Access |
| -------------------- | ------ |
| Register account     | YES    |
| Login                | YES    |
| Search schedule      | YES    |
| Booking ticket       | YES    |
| Upload document      | YES    |
| Payment              | YES    |
| Download e-ticket    | YES    |
| Request refund       | YES    |
| View booking history | YES    |
| Manage vessel        | NO     |
| Manage pricing       | NO     |
| Boarding validation  | NO     |

---

## 2. Admin

### Description

Role dengan akses penuh terhadap sistem operasional.

### Permissions

| Permission             | Access |
| ---------------------- | ------ |
| Manage vessels         | YES    |
| Manage routes          | YES    |
| Manage schedules       | YES    |
| Manage pricing         | YES    |
| Manage promo           | YES    |
| Manage booking         | YES    |
| Approve refund         | YES    |
| Reject refund          | YES    |
| View reports           | YES    |
| Broadcast notification | YES    |
| Manage passenger data  | YES    |
| Export manifest        | YES    |
| Manage users           | YES    |
| View audit logs        | YES    |

---

## 3. Boarding Officer

### Description

Petugas pelabuhan untuk validasi boarding.

### Permissions

| Permission              | Access |
| ----------------------- | ------ |
| Scan QR ticket          | YES    |
| Validate passenger      | YES    |
| View ticket status      | YES    |
| Boarding check-in       | YES    |
| View manifest           | YES    |
| Reject invalid boarding | YES    |
| Edit booking            | NO     |
| Refund management       | NO     |
| Pricing management      | NO     |

---

## 4. Ticket Counter Officer

### Description

Petugas loket pembelian offline.

### Permissions

| Permission            | Access |
| --------------------- | ------ |
| Create manual booking | YES    |
| Receive cash payment  | YES    |
| Print ticket          | YES    |
| Search booking        | YES    |
| Create passenger data | YES    |
| Edit vessel           | NO     |
| Edit pricing          | NO     |
| Refund approval       | NO     |

---

## 5. Deportation Officer

### Description

Petugas khusus deportasi penumpang.

### Permissions

| Permission                    | Access |
| ----------------------------- | ------ |
| Create deportation manifest   | YES    |
| Add deportation passenger     | YES    |
| Validate deportation identity | YES    |
| Boarding without payment      | YES    |
| Generate deportation report   | YES    |
| Public booking                | NO     |
| Pricing access                | NO     |
| Refund access                 | NO     |

---

## RBAC Rules

### Permission Separation

Boarding officer tidak boleh:

* Mengubah harga
* Mengubah booking
* Approve refund

Ticket counter officer tidak boleh:

* Melakukan refund approval
* Mengelola kapal
* Mengubah business rules

Deportation officer hanya boleh:

* Mengakses deportation module
* Mengelola deportation manifest

---

## Authentication Rules

| Rule                     | Description |
| ------------------------ | ----------- |
| Password hashing         | Required    |
| Session timeout          | Required    |
| CSRF protection          | Required    |
| Rate limiting login      | Required    |
| Audit login log          | Required    |
| Multi-device restriction | Optional    |

---

## Audit Logging

Aktivitas berikut wajib tercatat:

* Login
* Logout
* Refund approval
* Booking cancellation
* Ticket validation
* Schedule changes
* Price changes
* Promo changes
* Boarding validation
* Deportation manifest creation

# 03-booking-flow.md

## Booking Flow Overview

Booking flow dirancang:

* cepat
* mobile-first
* minim langkah
* validasi realtime
* anti-overbooking

---

## Booking Lifecycle

```text
SEARCH → SELECT_SCHEDULE → INPUT_PASSENGER → PAYMENT → PAID → BOARDING → USED
```

---

## Detailed Booking Flow

### Step 1 — Search Schedule

User memilih:

* Origin port
* Destination port
* Departure date
* Passenger count

### Validation

| Validation           | Rule        |
| -------------------- | ----------- |
| Origin required      | YES         |
| Destination required | YES         |
| Passenger max        | 8 persons   |
| Past date booking    | NOT ALLOWED |

---

### Step 2 — Schedule Listing

Sistem menampilkan:

* Vessel name
* Departure time
* Arrival estimation
* Available seats
* VIP price
* Regular price
* Promo availability

### System Rules

| Rule         | Description           |
| ------------ | --------------------- |
| Fully booked | Hide booking button   |
| Promo active | Show discounted price |
| H-6 passed   | Booking disabled      |

---

### Step 3 — Select Ticket Class

User memilih:

* VIP
* Regular

### Validation

| Validation              | Rule     |
| ----------------------- | -------- |
| VIP quota available     | Required |
| Regular quota available | Required |

---

### Step 4 — Passenger Input

### Required Fields

| Field                | Required |
| -------------------- | -------- |
| Full name            | YES      |
| Gender               | YES      |
| Date of birth        | YES      |
| Nationality          | YES      |
| Passport / ID number | YES      |
| Phone number         | YES      |

---

### Automatic Logic

| Rule     | Result |
| -------- | ------ |
| Age 0–12 | Child  |
| Age 13+  | Adult  |

---

### Step 5 — Document Upload

### Required Documents

| Document      | Required |
| ------------- | -------- |
| Passport / ID | YES      |
| Travel permit | Optional |

### Upload Validation

| Validation       | Rule         |
| ---------------- | ------------ |
| File type        | PDF/JPG/PNG  |
| Max size         | Configurable |
| Virus validation | Recommended  |

---

### Step 6 — Temporary Seat Lock

Saat user masuk payment:

* Seat di-lock sementara
* Mencegah concurrent overbooking

### Rules

| Rule            | Description   |
| --------------- | ------------- |
| Lock duration   | Configurable  |
| Payment timeout | Auto cancel   |
| Expired payment | Seat released |

---

### Step 7 — Payment

Supported methods:

* FPX
* E-wallet
* Online banking

### Payment Status

| Status  | Description     |
| ------- | --------------- |
| PENDING | Waiting payment |
| PAID    | Success         |
| FAILED  | Failed          |
| EXPIRED | Timeout         |

---

### Step 8 — Ticket Generation

Jika payment success:

* Generate invoice
* Generate QR ticket
* Generate PDF ticket
* Send notification

---

## Booking Constraints

| Rule                       | Value                       |
| -------------------------- | --------------------------- |
| Max passengers per booking | 8                           |
| Booking close              | H-6 departure               |
| Refund limit               | H-6 departure               |
| Boarding close             | 30 minutes before departure |

---

## Booking Edge Cases

| Scenario                     | Handling            |
| ---------------------------- | ------------------- |
| Double payment               | Manual verification |
| Callback failure             | Admin verification  |
| Seat sold out during payment | Payment rejected    |
| Duplicate passenger          | Validation warning  |
| Invalid passport             | Booking blocked     |
| Passenger timeout            | Booking cancelled   |

# 04-business-rules.md

## Pricing Rules

### Currency

Semua harga menggunakan **MYR (Malaysian Ringgit)**.

### Ticket Classes

| Class   | Description   |
| ------- | ------------- |
| VIP     | Premium seat  |
| Regular | Standard seat |

---

## Promo Rules

### Promo Types

| Type         | Description   |
| ------------ | ------------- |
| Percentage   | Example: 20%  |
| Fixed amount | Example: RM20 |

---

### Promo Configuration

| Field         | Required |
| ------------- | -------- |
| Promo name    | YES      |
| Promo code    | Optional |
| Start date    | YES      |
| End date      | YES      |
| Usage quota   | YES      |
| Active status | YES      |

---

### Promo Application Methods

Sistem mendukung dua cara penerapan promo:

1. **Auto-apply** — Sistem otomatis menerapkan promo ke schedule yang memenuhi syarat
2. **Promo Code** — User memasukkan kode promo manual saat booking

### Promo Restrictions

Promo dapat dibatasi berdasarkan:

* Route
* Ticket class
* Date range
* Passenger quantity

---

## Refund Rules

| Rule                  | Value                  |
| --------------------- | ---------------------- |
| Refund type           | Manual (via WhatsApp)  |
| Refund amount         | 25% dari harga tiket   |
| Max refund request    | H-6                    |
| Used ticket refund    | Not allowed            |
| Expired ticket refund | Not allowed            |

> **Note:** Sistem hanya mencatat refund request. Proses refund dilakukan manual via WhatsApp oleh admin. Tidak ada refund payment gateway otomatis.

---

## Boarding Rules

| Rule             | Description                 |
| ---------------- | --------------------------- |
| QR validation    | Mandatory                   |
| Duplicate scan   | Blocked                     |
| Boarding closure | 30 minutes before departure |
| USED ticket      | Invalid                     |
| EXPIRED ticket   | Invalid                     |

---

## Passenger Rules

### Passenger Categories

| Age  | Category |
| ---- | -------- |
| 0–12 | Child    |
| 13+  | Adult    |

### Pricing

Child (0–12 tahun) menggunakan harga yang sama dengan Adult untuk saat ini.

---

### Mandatory Data

All passenger data wajib lengkap.

Booking tidak dapat dilanjutkan jika:

* Passport missing
* Invalid identity
* Required fields incomplete

---

## Deportation Rules

### Special Handling

Deportation passenger:

* Tidak membutuhkan booking normal
* Tidak membutuhkan payment
* Tetap masuk manifest kapal
* Dipisahkan dari normal passenger

---

## Ticket Lifecycle

```text
PENDING_PAYMENT
→ PAID
→ USED
→ EXPIRED
→ CANCELLED
→ REFUND_REQUESTED
→ REFUNDED
```

---

## Payment Rules

### Timeout Handling

Jika payment tidak selesai:

* Booking auto cancelled
* Seat released
* QR not generated

---

## Capacity Rules

| Rule                          | Description |
| ----------------------------- | ----------- |
| Overbooking                   | Forbidden   |
| Seat lock                     | Required    |
| Concurrent booking protection | Required    |
| VIP quota                     | Separate    |
| Regular quota                 | Separate    |

---

## Notification Rules

Notification dikirim untuk:

* Booking success
* Payment success
* Refund update
* Schedule change
* Vessel cancellation
* Boarding reminder

### Notification Channels

| Channel    | Status   |
| ---------- | -------- |
| WhatsApp   | Required |
| Email      | Required |
| In-app     | Optional |

---

## Security Rules

| Rule                    | Status   |
| ----------------------- | -------- |
| CSRF protection         | Required |
| Session timeout         | Required |
| Upload validation       | Required |
| Audit logging           | Required |
| Login rate limiting     | Required |
| Secure password hashing | Required |

---

## Operational Rules

### Schedule Changes

Jika jadwal berubah:

* User mendapat notifikasi
* Ticket tetap valid
* User dapat request refund sesuai policy

### Vessel Cancellation

Jika kapal dibatalkan:

* Ticket menjadi CANCELLED
* User mendapat notifikasi
* Refund diproses manual/full refund sesuai keputusan admin

### Reschedule & No-Show Policy

Saat ini **tidak ada** kebijakan reschedule maupun no-show. Tiket yang tidak digunakan akan hangus (EXPIRED).

# 05-boarding-system.md

## Boarding System Overview

Boarding system digunakan untuk:

* Validasi tiket realtime
* Mencegah duplicate boarding
* Memastikan passenger sesuai manifest
* Mempercepat proses check-in pelabuhan

---

## Boarding Workflow

```text
SCAN QR → VALIDATE → CHECK STATUS → BOARDING SUCCESS/REJECTED
```

---

## Boarding Validation Rules

| Validation                   | Rule     |
| ---------------------------- | -------- |
| Ticket status must be PAID   | Required |
| USED ticket                  | Rejected |
| EXPIRED ticket               | Rejected |
| Boarding closed              | Rejected |
| Passenger exists in manifest | Required |
| QR signature valid           | Required |

---

## QR Ticket Structure

QR wajib memiliki:

| Field                      | Required |
| -------------------------- | -------- |
| Ticket ID                  | YES      |
| Booking code               | YES      |
| Passenger ID               | YES      |
| Encrypted validation token | YES      |
| Departure schedule ID      | YES      |

---

## Boarding Status

| Status    | Description        |
| --------- | ------------------ |
| ACTIVE    | Valid for boarding |
| USED      | Already boarded    |
| EXPIRED   | Boarding expired   |
| CANCELLED | Booking cancelled  |
| REFUNDED  | Refund completed   |

---

## Boarding UI Requirements

### Boarding Scanner Page

Platform: **Web mobile browser dengan akses kamera device**. Tidak menggunakan native mobile app.

Features:

* Camera QR scanner (via browser WebRTC API)
* Manual booking code input
* Passenger detail preview
* Boarding result popup
* Realtime validation

---

## Boarding Result States

| State        | UI Response    |
| ------------ | -------------- |
| VALID        | Green success  |
| USED         | Red warning    |
| INVALID      | Red rejection  |
| EXPIRED      | Orange warning |
| OFFLINE MODE | Yellow warning |

---

## Offline Boarding Mode

Jika internet terputus:

* Gunakan cached manifest
* Boarding tetap dapat dilakukan
* Sync otomatis setelah online kembali

### Risks

| Risk           | Mitigation      |
| -------------- | --------------- |
| Duplicate scan | Sync validation |
| Delayed sync   | Queue retry     |

---

## Boarding Audit Logs

Wajib menyimpan:

* Boarding officer
* Timestamp
* Device information
* Validation result
* QR scan result

# 06-refund-system.md

## Refund System Overview

Refund dilakukan secara manual oleh admin via WhatsApp.

Sistem hanya mencatat refund request. Proses refund (pengembalian dana) dilakukan manual di luar sistem.

---

## Refund Eligibility

| Rule               | Value         |
| ------------------ | ------------- |
| Max refund request | H-6 departure |
| Ticket status      | Must be PAID  |
| USED ticket        | Not eligible  |
| EXPIRED ticket     | Not eligible  |
| Refund amount      | 25%           |

---

## Refund Workflow

```text
USER REQUEST → SAVE TO DATABASE → ADMIN VIEW → ADMIN PROCESS MANUALLY VIA WHATSAPP
```

---

## Refund Request Process

### Step 1 — User Request

User membuka booking detail lalu:

* Klik request refund
* Input alasan refund

---

### Step 2 — System Validation

Sistem mengecek:

| Validation     | Result   |
| -------------- | -------- |
| Before H-6     | Continue |
| Ticket PAID    | Continue |
| Ticket USED    | Reject   |
| Ticket EXPIRED | Reject   |

---

### Step 3 — Admin Review

Admin dapat:

* Approve refund
* Reject refund
* Tambahkan catatan

---

## Refund Status Lifecycle

```text
PAID → REFUND_REQUESTED → PAID (no actual refund processed via system)
```

Sistem hanya mengubah status menjadi REFUND_REQUESTED. Admin yang memproses refund secara manual di WhatsApp.

---

## Refund Admin Dashboard

### Required Features

* Refund queue list
* Passenger detail
* Payment detail
* Refund reason
* Refund logs
* Admin notes (untuk referensi proses manual WhatsApp)

> Approval/rejection action tidak diperlukan di sistem karena refund diproses manual via WhatsApp.

---

## Refund Notifications

User menerima notifikasi saat:

* Refund requested
* Refund approved
* Refund rejected

---

## Refund Edge Cases

| Scenario                      | Handling           |
| ----------------------------- | ------------------ |
| Duplicate refund request      | Blocked            |
| Refund after boarding closure | Rejected           |
| Already refunded ticket       | Rejected           |

# 07-database-schema.md

## Database Architecture

Database menggunakan MySQL relational architecture.

---

## Main Tables

```text
users
roles
vessels
routes
schedules
bookings
booking_passengers
tickets
payments
refunds
promos
boarding_logs
notifications
deportation_manifests
deportation_passengers
documents
audit_logs
```

---

## users

| Column     | Type      |
| ---------- | --------- |
| id         | bigint    |
| role_id    | foreignId |
| name       | string    |
| email      | string    |
| password   | string    |
| created_at | timestamp |

---

## roles

| Column | Type   |
| ------ | ------ |
| id     | bigint |
| name   | string |
| slug   | string |

---

## vessels

| Column           | Type    |
| ---------------- | ------- |
| id               | bigint  |
| name             | string  |
| capacity         | integer |
| vip_capacity     | integer |
| regular_capacity | integer |
| status           | enum    |

---

## routes

| Column             | Type    |
| ------------------ | ------- |
| id                 | bigint  |
| origin_port        | string  |
| destination_port   | string  |
| estimated_duration | integer |
| active             | boolean |

---

## schedules

| Column         | Type      |
| -------------- | --------- |
| id             | bigint    |
| vessel_id      | foreignId |
| route_id       | foreignId |
| departure_time | datetime  |
| arrival_time   | datetime  |
| vip_price      | decimal   |
| regular_price  | decimal   |
| status         | enum      |

---

## bookings

| Column           | Type      |
| ---------------- | --------- |
| id               | bigint    |
| user_id          | foreignId |
| booking_code     | string    |
| total_passengers | integer   |
| total_amount     | decimal   |
| booking_status   | enum      |
| payment_status   | enum      |
| created_at       | timestamp |

---

## booking_passengers

| Column          | Type      |
| --------------- | --------- |
| id              | bigint    |
| booking_id      | foreignId |
| full_name       | string    |
| gender          | enum      |
| birth_date      | date      |
| nationality     | string    |
| passport_number | string    |
| passenger_type  | enum      |
| ticket_class    | enum      |

> ticket_class: VIP / Regular

---

## tickets

| Column        | Type      |
| ------------- | --------- |
| id            | bigint    |
| booking_id    | foreignId |
| passenger_id  | foreignId |
| ticket_class  | enum      |
| qr_token      | text      |
| ticket_status | enum      |
| boarded_at    | timestamp |

> ticket_class: VIP / Regular

---

## payments

| Column         | Type      |
| -------------- | --------- |
| id             | bigint    |
| booking_id     | foreignId |
| payment_method | string    |
| transaction_id | string    |
| amount         | decimal   |
| payment_status | enum      |
| paid_at        | timestamp |

---

## refunds

| Column        | Type      |
| ------------- | --------- |
| id            | bigint    |
| booking_id    | foreignId |
| refund_amount | decimal   |
| refund_reason | text      |
| refund_status | enum      |
| processed_by  | foreignId |

---

## promos

| Column       | Type      |
| ------------ | --------- |
| id           | bigint    |
| name         | string    |
| code         | string    |
| type         | enum      |
| value        | decimal   |
| start_date   | datetime  |
| end_date     | datetime  |
| usage_quota  | integer   |
| used_count   | integer   |
| route_id     | foreignId |
| ticket_class | enum      |
| is_active    | boolean   |
| created_at   | timestamp |

> type: percentage / fixed_amount
> ticket_class: VIP / Regular / all (nullable)

---

## notifications

| Column    | Type      |
| --------- | --------- |
| id        | bigint    |
| user_id   | foreignId |
| type      | enum      |
| channel   | enum      |
| title     | string    |
| body      | text      |
| is_read   | boolean   |
| sent_at   | timestamp |
| created_at | timestamp |

> type: booking_success / payment_success / refund_update / schedule_change / vessel_cancellation / boarding_reminder
> channel: email / whatsapp / in_app

---

## deportation_manifests

| Column           | Type      |
| ---------------- | --------- |
| id               | bigint    |
| schedule_id      | foreignId |
| officer_id       | foreignId |
| manifest_code    | string    |
| total_passengers | integer   |
| notes            | text      |
| created_at       | timestamp |

---

## deportation_passengers

| Column            | Type      |
| ----------------- | --------- |
| id                | bigint    |
| manifest_id       | foreignId |
| full_name         | string    |
| gender            | enum      |
| nationality       | string    |
| passport_number   | string    |
| qr_token          | text      |
| boarding_status   | enum      |
| boarded_at        | timestamp |
| created_at        | timestamp |

---

## documents

| Column        | Type      |
| ------------- | --------- |
| id            | bigint    |
| passenger_id  | foreignId |
| type          | enum      |
| file_path     | string    |
| file_name     | string    |
| mime_type     | string    |
| file_size     | integer   |
| uploaded_at   | timestamp |

> type: passport / travel_permit

---

## boarding_logs

| Column            | Type      |
| ----------------- | --------- |
| id                | bigint    |
| ticket_id         | foreignId |
| validated_by      | foreignId |
| validation_result | enum      |
| validated_at      | timestamp |

---

## audit_logs

| Column      | Type      |
| ----------- | --------- |
| id          | bigint    |
| user_id     | foreignId |
| action      | string    |
| entity_type | string    |
| entity_id   | bigint    |
| payload     | json      |
| created_at  | timestamp |

# 08-api-specification.md

## API Architecture

Sistem menggunakan:

* Laravel web routes
* Internal REST API jika diperlukan
* Session authentication
* Sanctum optional untuk mobile app future

---

## Authentication Endpoints

| Method | Endpoint  |
| ------ | --------- |
| POST   | /login    |
| POST   | /register |
| POST   | /logout   |

---

## Booking Endpoints

| Method | Endpoint                   |
| ------ | -------------------------- |
| GET    | /schedules                 |
| GET    | /schedules/{id}            |
| POST   | /bookings                  |
| GET    | /bookings/{code}           |
| POST   | /bookings/{id}/payment     |
| POST   | /bookings/{id}/refund      |
| GET    | /tickets/{id}/download     |

---

## Boarding Endpoints

| Method | Endpoint                      |
| ------ | ----------------------------- |
| POST   | /boarding/scan                |
| POST   | /boarding/manual-validate     |
| GET    | /boarding/manifest/{schedule} |

---

## Admin Endpoints

| Method | Endpoint                    |
| ------ | --------------------------- |
| GET    | /admin/bookings             |
| GET    | /admin/refunds              |
| POST   | /admin/schedules            |
| POST   | /admin/promos               |

## Deportation Endpoints

| Method | Endpoint                                  |
| ------ | ----------------------------------------- |
| POST   | /deportation/manifests                    |
| GET    | /deportation/manifests/{schedule}         |
| POST   | /deportation/manifests/{id}/passengers    |
| POST   | /deportation/boarding/scan                |

---

## API Security

| Security         | Status   |
| ---------------- | -------- |
| CSRF Protection  | Required |
| Rate limiting    | Required |
| Auth middleware  | Required |
| Audit logging    | Required |
| Input validation | Required |

# 09-security-architecture.md

## Security Objectives

Tujuan utama:

* Protect passenger data
* Prevent ticket fraud
* Prevent duplicate boarding
* Secure payment flow
* Secure admin actions

---

## Authentication Security

| Rule               | Required |
| ------------------ | -------- |
| Password hashing   | YES      |
| Session expiration | YES      |
| CSRF protection    | YES      |
| Login throttling   | YES      |
| Secure cookies     | YES      |

---

## File Upload Security

| Rule                         | Description |
| ---------------------------- | ----------- |
| MIME validation              | Required    |
| File size validation         | Required    |
| Dangerous extension blocking | Required    |
| Virus scan                   | Recommended |

---

## QR Security

QR wajib:

* Unique per ticket
* Encrypted token
* Tidak dapat ditebak
* Tidak sequential

---

## Payment Security

| Rule                         | Description |
| ---------------------------- | ----------- |
| Payment signature validation | Required    |
| Callback verification        | Required    |
| Duplicate callback handling  | Required    |
| Payment audit logging        | Required    |

---

## Audit Logging Requirements

Aktivitas wajib dicatat:

* Login/logout
* Payment changes
* Refund approval
* Boarding validation
* Schedule updates
* Promo updates
* Admin changes

---

## Backup Strategy

| Backup          | Frequency |
| --------------- | --------- |
| Database backup | Daily     |
| File backup     | Daily     |
| Audit backup    | Daily     |

---

## Recommended Infrastructure

| Component               | Recommendation |
| ----------------------- | -------------- |
| SSL                     | Mandatory      |
| Cloudflare              | Recommended    |
| Firewall                | Recommended    |
| WAF                     | Recommended    |
| Queue worker monitoring | Recommended    |

# 10-deployment-architecture.md

## Deployment Strategy

Sistem dirancang agar:

* Mudah deploy
* Mudah maintenance
* Cocok shared hosting
* Bisa scale ke VPS

---

## Production Stack

| Component  | Stack            |
| ---------- | ---------------- |
| Backend    | Laravel          |
| Frontend   | Blade + Tailwind |
| Database   | MySQL            |
| Web Server | Nginx / Apache   |
| Queue      | Database / Redis |
| Storage    | Local / S3       |

---

## Recommended Server Specs

### Initial Production

| Resource | Recommendation |
| -------- | -------------- |
| CPU      | 2 Core         |
| RAM      | 4 GB           |
| Storage  | SSD            |
| OS       | Ubuntu LTS     |

---

## Queue Jobs

Queue digunakan untuk:

* Notification sending
* PDF generation
* Email sending
* Boarding sync
* Refund processing logs

---

## Monitoring

### Required Monitoring

| Monitoring            | Required |
| --------------------- | -------- |
| Server uptime         | YES      |
| Queue failures        | YES      |
| Payment callback logs | YES      |
| Boarding logs         | YES      |
| Error logs            | YES      |

---

## Environment Variables

```env
APP_NAME="Ship Ticketing"
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=file
```


boarding@shipticketing.com password 

counter@shipticketing.com
password 
admin@shipticketing.com
password



/ umur deteksi dari tanggal lahir 

ada harga beda dari kategori umur


bisa pakai data diri profile ( register disesuaikan )
bisa add orang 

posisi QR bawah tengah di detail tiket


estimated time dihapus 

ada 1 menu untuk check rill time available seat

ada reporting per schedule berapa daftar, berapa bayar, beraapa naik, berapa berangkat, berapa refund dll per schedule



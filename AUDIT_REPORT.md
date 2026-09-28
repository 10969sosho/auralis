# Auralis — Logical Error Audit Report

**Date:** 2026-09-28
**Method:** Static code tracing (controllers, models, observers, console commands, Filament resources, routes, views, migrations, seeders). No files were modified. `vendor/` is not installed, so findings were proven by cross-referencing migrations/enums/call sites rather than runtime repro.
**Severity:** Critical = broken core flow or auth bypass · High = wrong money/seat data or always-fails request · Medium = wrong UI/report/edge flow · Low = dead code, copy, cosmetic logic.

---

## CRITICAL

### C1. Ticket PDF + boarding QR accessible to anyone when logged out (`null === null`)
- **File:** `app/Http/Controllers/TicketController.php:18`
- **Description:** `if ($ticket->booking->user_id === auth()->id())` evaluates `null === null` to `true` for unauthenticated callers (and for all counter/guest bookings where `user_id` is null). The guest-token branch at line 23 becomes dead code for guests. Routes `routes/web.php:123-124` have no auth middleware, so anyone who knows/guesses a ticket id can download the e-ticket PDF including the boarding QR.
- **Fix:** `if (auth()->check() && $ticket->booking->user_id !== null && $ticket->booking->user_id === auth()->id()) { return; }` and, when the booking has a `guest_token`, require a matching `?token=` (hash_equals) instead of treating it as optional.

### C2. Ticket expiry = midnight of departure day → every boarding scan on sailing day fails
- **Files:**
  - Check: `app/Http/Controllers/BoardingController.php:103` — `$ticket->expiry_date->isPast()`
  - Writers: `app/Http/Controllers/CounterController.php:143`, `app/Filament/Resources/Payments/Tables/PaymentsTable.php:141`, `app/Filament/Resources/Payments/Pages/EditPayment.php:82`, `app/Observers/ScheduleObserver.php:29` — all store `departure_time->startOfDay()`
- **Description:** `expiry_date` (timestamp, `Ticket` cast `datetime`) is always 00:00 of the departure day, but boarding happens on that same day (window closes at departure −30 min). `isPast()` is therefore true from 00:00, and the expiry branch (line 103) runs *before* the `active` check (line 139) → every scan on sailing day returns "This ticket has expired". Full boarding outage on the day it matters most.
- **Fix:** Store `->endOfDay()` at the four writers (or compare `$ticket->expiry_date->copy()->endOfDay()->isPast()`), or drop the expiry check and rely on the boarding-window check (`isBoardingClosed`, line 130).

### C3. Editing a schedule's departure time 500s (observer crashes twice)
- **File:** `app/Observers/ScheduleObserver.php:19-22, 33` (registered at `app/Providers/AppServiceProvider.php:17`)
- **Description:**
  1. Lines 21-22 call `$schedule->route()->withTrashed()->first()` / `->vessel()->withTrashed()->first()`, but `App\Models\Route` and `App\Models\Vessel` do not use `SoftDeletes` → `withTrashed` macro doesn't exist → `BadMethodCallException` on every departure-time edit, before any notification runs. (Even if it existed, the *current* relation would be returned, not the old one.)
  2. Line 19 builds a `stdClass` stub, but `MailHelper::sendScheduleChanged(Booking, Schedule, Schedule)` (`app/Helpers/MailHelper.php:48`) type-hints `App\Models\Schedule` → `TypeError` at line 33.
  Additionally line 29 re-sets expiry with the same `startOfDay()` bug (C2), and tickets/emails are mutated without a transaction.
- **Fix:** Resolve originals by id (`Route::find($oldRouteId)`, `Vessel::find($oldVesselId)`), build a real `new Schedule([...])` stub or change the signature to accept an array/DTO, and wrap ticket updates + mail in a transaction.

### C4. `bookings:cancel-expired` (every minute) crashes on deportation bookings → no expired booking is ever cancelled
- **Files:** `app/Console/Commands/CancelExpiredBookings.php:17-21, 30`; schedule at `routes/console.php:5`; `app/Events/SeatAvailabilityUpdated.php:18`
- **Description:** Deportation bookings are created with `schedule_id => null`, `booking_status = pending_payment`, `payment_status = pending`, `expires_at = now()+24h` (`app/Http/Controllers/DeportationController.php:175-185`), so they match the query at lines 17-21. Line 30 then dispatches `new SeatAvailabilityUpdated(null)` → `TypeError` (constructor requires `Schedule`) → unhandled → the whole run aborts; no *later* expired booking is cancelled that minute. Once any deportation hold expires, the cron stays broken for normal bookings too.
- **Fix:** `if ($booking->schedule) { event(new SeatAvailabilityUpdated($booking->schedule)); }` — and consider skipping `is_deportation` rows entirely.

### C5. Deportation boarding scan returns HTTP 500 *after* the boarding was committed
- **File:** `app/Http/Controllers/DeportationController.php:434-448`
- **Description:** Deportation bookings always have `schedule_id = null` (line 177), so `$schedule = $ticket->booking->schedule` is null and `$schedule->route->origin_port` (line 446) throws — but only *after* the `DB::transaction` at 401-432 has already committed. The officer sees a 500 for a boarding that actually persisted, and the analytics/email inside the transaction were written.
- **Fix:** Use the existing helpers: `'route' => $ticket->booking->route_display`, `'vessel' => $ticket->booking->vessel_display`, `'departure' => null` (helpers already exist in `app/Models/Booking.php:69-91`).

---

## HIGH

### H1. Seat capacity ignores unpaid holds and awaiting-approval bookings → oversell
- **Files:** `app/Models/Schedule.php:49-80`; `app/Http/Controllers/BookingController.php:135-144, 177`; `app/Http/Controllers/CounterController.php:72-81`
- **Description:** `vipBooked`/`regularBooked` count only `paid|used|refund_requested`. A `pending_payment` booking holds seats for 10 minutes (`BookingController.php:190-191`) and an `awaiting_approval` booking can sit for days while the admin reviews proof — neither is counted. The capacity check also runs *outside* the `DB::transaction` (line 177) with no row lock, so two concurrent submissions both pass. Result: seats/VIP cabins oversell.
- **Fix:** Count `pending_payment`/`awaiting_approval` where `expires_at > now()` (or `paid_at`-independent hold), and re-check inside the transaction with `Schedule::lockForUpdate()`.

### H2. Booking payment/success/detail endpoints leak without token; success page 500s for unpaid bookings
- **Files:** `app/Http/Controllers/BookingController.php:361-386` (`showBooking` — token only applied when supplied, no owner check), `:260-286` (`showPayment`), `:288-337` (`processPayment` — no auth, anyone with the code can upload proof), `:339-348` (`success` — no status gate); routes `routes/web.php:101-106`; view `resources/views/booking/success.blade.php:47`
- **Description:**
  - `/booking/{code}/detail` and `/booking/guest/{code}` expose names, passport numbers, ticket numbers and e-ticket links to *anyone* who has the code (no `?token=` required, no `user_id` check) — the sibling controllers enforce it (`TicketController`, `DeportationController:246-250`).
  - `/booking/{code}/success` renders "Booking Confirmed" for `pending_payment`/`expired` bookings, then dereferences `$passenger->ticket->ticket_number` which is null (tickets only exist after approval) → 500.
- **Fix:** When the booking has a `guest_token` and the caller is unauthenticated, require + match the token; gate `success()` on `payment_status ∈ [paid, approved]` (redirect to `booking.payment` otherwise) and null-guard the ticket loop.

### H3. VIP passengers are charged the regular price
- **Files:** `app/Models/Schedule.php:107-119`; seeders `database/seeders/DatabaseSeeder.php:162-166`, `database/seeders/ScheduleSeeder.php:103-107`; mirrored JS `resources/views/booking/create.blade.php:208-211`, `resources/views/counter/create.blade.php:144-145`
- **Description:** `getPassengerPrice()` returns the age-category price *before* considering `$ticketClass`, and seeders store the adult age price as `regular_price`. So every adult VIP ticket is billed the regular price while `/prices` and the booking form advertise `vip_price` (e.g. VIP shows 150, charges 80). The client-side JS repeats the same precedence, so displayed total matches the wrong server total.
- **Fix:** Only apply the age-category price when it is class-agnostic (child/infant), otherwise fall back to the class price: `if ($agePrice !== null && $ticketClass !== 'vip') { return $agePrice; }` (or add per-class age prices). Mirror the change in both blade JS calculators.

### H4. Boarding QR accepts forged `ticket_id` (qr_token never verified)
- **Files:** `app/Http/Controllers/BoardingController.php:25-46`; payload built at `app/Http/Controllers/TicketController.php:37-43`; same hole `app/Http/Controllers/DeportationController.php:357-370`
- **Description:** The QR carries `token`/`booking_code`, but `scan()` only reads `ticket_id` and loads the ticket. A crafted payload `{"ticket_id":1}` validates and boards. There is also no check that the ticket belongs to the schedule currently being boarded (an officer scanning for Schedule A can board a ticket from Schedule B).
- **Fix:** `hash_equals((string) $ticket->qr_token, (string) ($qrData['token'] ?? ''))` before `validateTicket()`, and verify `$ticket->booking->schedule_id` matches the boarded schedule (context/schedule id from the scanner page).

### H5. Counter refund request always 500s (invalid enum + non-fillable field)
- **File:** `app/Http/Controllers/CounterController.php:243-249`
- **Description:** Writes `refund_status => 'pending'`, but the enum is `requested|approved|rejected|refunded|processed` (`database/migrations/2026_05_16_000010_create_refunds_table.php:17`, `database/migrations/2026_06_26_043339_add_refunded_to_refunds_status.php:10`) with `strict => true` (`config/database.php:60`) → insert throws. `requested_by` is not a column and not in `Refund::$fillable` → silently discarded. Even on a non-strict DB the status would never match what `RefundsTable` filters on (`=== 'requested'`), so the request would be invisible to admins.
- **Fix:** `'refund_status' => 'requested'` and remove `requested_by`.

### H6. Manual boarding validation only ever handles the first passenger
- **File:** `app/Http/Controllers/BoardingController.php:65-67`
- **Description:** `Ticket::whereHas('booking', ...)->first()` always returns the same first ticket of a booking. For multi-passenger bookings, manual validation validates passenger #1 (then reports `used` forever) — passengers 2..n can never be boarded manually.
- **Fix:** Return the booking's tickets as a selectable list (or accept `passenger_id`/`ticket_id`) and validate the selected one.

### H7. Ship marked "departed" during boarding merely because it is fully booked
- **File:** `app/Http/Controllers/BoardingController.php:154-157`
- **Description:** Inside `validateTicket()`, `if ($booking->schedule->isFullyBooked) { $schedule->update(['status' => 'departed']); }` — "all seats sold" is unrelated to departure. On a full ship the first scan flips the schedule to `departed`, which removes it from all `status = 'scheduled'` queries (search, home, seat availability) while the vessel is still at the pier, and it re-runs on every scan.
- **Fix:** Remove the block; flip to `departed` based on time (scheduled job / existing scheduler) or an explicit officer action.

### H8. Promo quota burned without discount applied + promo class hardcoded to `'regular'`
- **File:** `app/Http/Controllers/BookingController.php:167, 238-240` (same pattern `:78`, `resources/views/booking/search.blade.php:153`)
- **Description:**
  1. Line 238-240 increments `used_count` whenever a promo *code was submitted*, even when `isApplicableToSchedule()` at line 167 returned false — the customer loses quota and gets no discount.
  2. Line 167 always passes `'regular'` as the ticket class, so a promo with `ticket_class = 'vip'` can never apply to any booking, and a `regular`-only promo applies to VIP-only bookings.
- **Fix:** Increment only when a discount was actually granted (`if ($discountAmount > 0)`); pass the actual class(es) purchased (or `all` when mixed).

---

## MEDIUM

### M1. Refund rejected → customer can never re-request
- **Files:** `app/Http/Controllers/BookingController.php:420-423`; `resources/views/booking/detail.blade.php:196`; `app/Filament/Resources/Refunds/Tables/RefundsTable.php:197-203`
- **Description:** Rejecting a refund restores `booking_status = 'paid'` but leaves the refund row with `refund_status = 'rejected'`. `refundRequest()` blocks on *any* existing refund, and the view hides the form behind `@if($booking->refund)` → the customer is permanently locked out of refund requests after one rejection.
- **Fix:** Allow when `$existingRefund->refund_status === 'rejected'` (and render the form under the same condition).

### M2. Views dereference `$booking->schedule` for deportation bookings (null)
- **Files:** `resources/views/booking/history.blade.php:41,44`; `resources/views/booking/detail.blade.php:42,46,215`; `resources/views/tickets/show.blade.php:23`
- **Description:** Deportation bookings have `schedule_id = null` but are returned by `BookingController::history()` (no `is_deportation` filter) and reachable via `booking.detail`/`tickets.show` → "property on null" 500 when the owner opens My Bookings / booking detail / ticket page. The null-safe helpers `route_display` / `vessel_display` (`app/Models/Booking.php:69-91`) exist precisely for this and are not used.
- **Fix:** Use `$booking->route_display` / `$booking->vessel_display`, and null-guard `$booking->schedule->isH6Passed` (`$booking->schedule?->isH6Passed ?? false`).

### M3. Report metrics: "Remaining" derived from boarded count; occupancy includes cancelled passengers
- **File:** `app/Http/Controllers/AdminReportController.php:100-101, 119-121, 124-125`
- **Description:** `$remaining = max(0, $totalCapacity - $boarded)` — a schedule with paid seats but 0 boardings reports the *full capacity* as free. `$occupancy = $totalPassengers / $totalCapacity` where `$totalPassengers` includes `cancelled`/`expired` bookings → occupancy can exceed 100%. Both feed the admin reports page and the CSV export (`:174-197`).
- **Fix:** Track `$activePassengers` (paid/used/refund_requested) and use it for both `$remaining` and occupancy.

### M4. Admin schedule filter "Payment = Refunded" always returns 0 rows
- **Files:** `resources/views/admin/schedule-show.blade.php:133`; `app/Http/Controllers/AdminScheduleController.php:52-54, 130-131`
- **Description:** The option applies `payment_status = refunded`, but `bookings.payment_status` enum is `pending|awaiting_approval|paid|rejected|failed|expired` (`database/migrations/2026_06_26_041334...:10`) — no `refunded` value exists → the filter silently matches nothing. (The `booking_status` variant at view line 146 *is* valid.)
- **Fix:** Remove the option, or map `payment_status=refunded` to `booking_status = refunded` in the controller.

### M5. Payment QR image can never be cleared; success toast always fires
- **File:** `app/Filament/Pages/PaymentSettings.php:86-94`
- **Description:** A cleared upload arrives as `[]`, fails the condition at line 86, and skips `Setting::setValue(...)` entirely — yet line 94 still shows "Payment QR settings updated successfully". Deleting the QR is impossible from the UI.
- **Fix:** In the else branch call `Setting::setValue('payment_qr_image', null)` (and delete the stored file).

### M6. Schedule-change notifications skip `awaiting_approval` and `refund_requested` bookings
- **File:** `app/Filament/Resources/Schedules/Pages/EditSchedule.php:52`
- **Description:** `whereIn('booking_status', ['pending_payment', 'paid', 'used'])` omits two active states, so those passengers get no in-app notification of a changed departure/arrival. (The email/observer path that should cover them is itself broken — C3.)
- **Fix:** Add `'awaiting_approval'` and `'refund_requested'`.

### M7. Filament Reports page filters do nothing
- **Files:** `app/Filament/Pages/Reports.php:10-16`; `resources/views/filament/pages/reports.blade.php:26-40, 50-65`
- **Description:** `scheduleId`/`status`/`dateFrom`/`dateTo` are bound in the filter bar but never passed into `ReportsStatsOverviewWidget` / `RevenueChartWidget` / `BookingTrendChartWidget` / `ScheduleTableWidget`, all of which query globally. Changing a filter visibly changes nothing (`$status` is never read at all); only the CSV link re-queries (via `AdminReportController`).
- **Fix:** Pass the filter state to the widgets (`@livewire(..., ['filters' => [...]])`) and apply it in their queries — or remove the filter bar.

### M8. Schedule table widget: `->sortable()` on computed `->state()` columns → SQL error
- **File:** `app/Filament/Widgets/ScheduleTableWidget.php:61, 66, 71, 76, 81, 87, 92, 97`
- **Description:** Columns like `capacity`, `booked`, `revenue`, `occupancy` exist only as closures; clicking their headers makes Filament `orderBy('capacity')` etc. against the `schedules` table, which has no such column → query error on sort.
- **Fix:** Remove `->sortable()` from computed columns (or supply `->sortQuery(...)` sorting on a real column / precomputed join).

### M9. Audit logging middleware never attached → audit trail permanently empty
- **Files:** `app/Http/Middleware/AuditLogging.php:16-24`; alias only at `bootstrap/app.php:20`
- **Description:** No route uses `->middleware('audit')` (grep across `routes/`), and `AuditLog::log` has no other caller → the Filament AuditLogs resource can never show data. If it were attached, the route-name mapping is also wrong (`'login'` — POST `/login` is unnamed; `'booking.payment'` is the GET page, not the `booking.process-payment` mutation; `'schedule.store'`/`'promo.store'` don't exist).
- **Fix:** Attach to the mutating route groups and correct the name list to real route names.

### M10. `Payment::create` outside the transaction → later fatal + stuck booking
- **Files:** `app/Http/Controllers/BookingController.php:245-249, 312-317`; same pattern `CounterController.php:150-157`, `DeportationController.php:230-234`
- **Description:** The booking is committed inside `DB::transaction` (line 177-243) but the `Payment` row is created after. If that insert fails (or the process dies between), the booking exists with `payment_status = pending` and `$booking->payment === null` → `processPayment()` line 317 `$payment->update(...)` throws on null and the booking can never be paid.
- **Fix:** Create the payment inside the transaction (`$booking->payment()->create([...])`) and null-check in `processPayment()`.

### M11. Deportation scanner accepts refunded/expired tickets
- **File:** `app/Http/Controllers/DeportationController.php:380-399`
- **Description:** Only `used` and `cancelled` are rejected before boarding. A `refunded` or `expired` deportation ticket still boards successfully (the regular scanner checks both — `BoardingController.php:103-128`).
- **Fix:** Add `refunded`/`expired` guards mirroring `BoardingController::validateTicket()`.

### M12. Refund approval crashes for deportation bookings (unguarded event)
- **File:** `app/Filament/Resources/Refunds/Tables/RefundsTable.php:164`
- **Description:** `event(new SeatAvailabilityUpdated($booking->schedule))` is unguarded inside the approval transaction; deportation bookings have `schedule = null` → `TypeError` rolls back the whole refund approval. Same root cause family as C4 (`SeatAvailabilityUpdated` requires non-null `Schedule`).
- **Fix:** `if ($booking->schedule) { event(...); }`.

---

## LOW

### L1. `BookingController::store` skips the `is_active` gate
- **File:** `app/Http/Controllers/BookingController.php:128` vs `:68`
- **Description:** `show()` blocks inactive schedules, `store()` only checks `is_h6_passed` + status → a crafted POST can book an inactive schedule.
- **Fix:** Add `|| !$schedule->is_active`.

### L2. Seat-availability payload mixes status lists and doesn't clamp totals
- **Files:** `app/Http/Controllers/SeatAvailabilityController.php:32-56`; `app/Events/SeatAvailabilityUpdated.php:31-33, 70-74`
- **Description:** `paid`/`available` are computed from `paid|used` while `booked`/`remaining` use `paid|used|refund_requested` (accessors) → `remaining` and `available` disagree for the same schedule; `total.remaining` / `total.available` skip the `max(0, ...)` clamp used for VIP/regular, so they can render negative.
- **Fix:** Use one status list for both and clamp with `max(0, ...)`.

### L3. Counter store allows non-`scheduled` schedules
- **File:** `app/Http/Controllers/CounterController.php:65-67`
- **Description:** Unlike `BookingController::store:128`, the counter path never checks `$schedule->status === 'scheduled'` → walk-in bookings can be written against a `departed`/`cancelled` schedule.
- **Fix:** Add the same status check.

### L4. Payment countdown TDZ crash when the page opens after expiry
- **File:** `resources/views/booking/payment.blade.php:635, 654-655`
- **Description:** `updateCountdown()` is invoked immediately at line 654, but `const timerInterval` is declared at line 655 — if the booking is already expired, line 635 `clearInterval(timerInterval)` hits the temporal dead zone → `ReferenceError`, the interval is never set, and every script statement after line 655 (QR enlarge handler, etc.) fails to execute.
- **Fix:** Declare `let timerInterval;` before the function / call, assign after.

### L5. Dead branch: bookings are never `approved`
- **Files:** `app/Http/Controllers/BookingController.php:281`, `app/Http/Controllers/DeportationController.php:257`, `app/Filament/Pages/DeportationAnalytics.php:62`
- **Description:** These check `payment_status ∈ ['paid', 'approved']` on *bookings*, but approval writes `approved` to the **payments** table and `paid` to bookings (`PaymentsTable.php:108-117`); `bookings.payment_status` enum has no `approved` value. Harmless today, misleading tomorrow.
- **Fix:** Drop `'approved'` from the booking-side checks (or map explicitly via the payment row).

### L6. Refund WhatsApp link sends the phone number as the message
- **File:** `app/Filament/Resources/Refunds/Tables/RefundsTable.php:60`
- **Description:** `?text=Refund%20Booking%20' . urlencode($state)` where `$state` is the phone column → the prefilled message is the phone number instead of the booking reference.
- **Fix:** Build text from `$record->booking?->booking_code`.

### L7. `EditPayment` page is unreachable dead code (and lacks deportation guards)
- **Files:** `app/Filament/Resources/Payments/PaymentResource.php:48-53`; `app/Filament/Resources/Payments/Pages/EditPayment.php:82, 89`
- **Description:** Only `index` is registered, so `EditPayment` can never run. It also lacks the null-schedule guards that the working table action has (`PaymentsTable.php:141, 147`): approving a deportation payment here would deref `schedule->departure_time` and dispatch the event unguarded.
- **Fix:** Either delete `EditPayment.php` + `PaymentForm.php`, or register the route and port the deportation guards.

### L8. Age-category seed data is mutually inconsistent
- **Files:** `database/seeders/AgeCategorySeeder.php:8-21` (Infant 0-2, Child 3-12, Adult 13-150) vs `database/seeders/DummyDataSeeder.php:81-83` (Adult 12-120, Child 2-11, Infant 0-1) vs fallback logic `app/Models/Schedule.php:125` (`<=2 Infant, <=12 Child`) and `BookingController.php:206` (`<=12 Child` only)
- **Description:** Whichever seeder runs first wins (`DummyDataSeeder` guards on `count() === 0`), producing different age brackets — and therefore different prices/passenger types — between environments.
- **Fix:** Delete the age block from `DummyDataSeeder` (let `AgeCategorySeeder` own it) and use one shared boundary table.

### L9. Booking-hold copy says 30 minutes, actual hold is 10
- **Files:** `resources/views/booking/create.blade.php:164` vs `app/Http/Controllers/BookingController.php:191` (`addMinutes(10)`); counter hold is 30 (`CounterController.php:114`)
- **Fix:** Make the copy match the configured hold.

### L10. Deportation booking skips the H-6 cutoff
- **File:** `app/Http/Controllers/DeportationController.php:154-156`
- **Description:** Only `status`/`is_active` are checked; the regular flow blocks at H-6 (`BookingController.php:128`) → deportation users can buy into a schedule inside the cutoff window (possibly intentional for open tickets — confirm with product).
- **Fix:** Add `if ($schedule->isH6Passed)` or document the exception.

---

## Not reported / cleared on inspection

- `SeatAvailabilityController::show()` *is* routed (`routes/api.php:7`) — no defect.
- Payment approval writes `approved`/`paid` to the correct tables (enum-compatible).
- Counter change calculation, guest `change` echo, and `RefundsTable` reject→approve flow re-verified as correct at the data level (M1 covers the re-request lockout).
- `ToyibPayService` references unregistered routes (`booking.toyibpay-return`/`-callback`) and appears unused — dead code, no runtime impact found; not counted as a logical error.

## Suggested fix order

1. C2 (boarding outage) → C1 (ticket leak) → C4 (cron crash) → C5/C3 (admin+officer 500s)
2. H1 (oversell) → H3 (wrong VIP price) → H2 (token/status gates) → H4 (QR forgery)
3. H5–H8, then Medium batch, then Low.

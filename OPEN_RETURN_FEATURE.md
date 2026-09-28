# Open Return Ticket

Booking without a fixed return sailing. Buyer picks only a `return_date`; the system binds it to a sailing later.

## Data

| Table | Purpose |
|---|---|
| `open_return_tickets` | `booking_id`, `return_date`, `status` (`open` → `assigned` → `used`) |
| `master_ships` | Daily return capacity (`capacity`), seeded with `Auralis Ferry` / 280 when empty |

There is no `schedule_id` on the ticket — the sailing is resolved by date.

## Flow

1. `POST /booking` with `return_date` (and no `schedule_id`) creates the booking + `open_return_tickets` row (`status = open`).
   - Price source: sailing on the return date, else the newest active scheduled sailing, else 0 (insurance still applies).
   - No seat/capacity check against a departure schedule — there is none to check.
2. `OpenReturnTicket::assignIfPossible()` flips `open` → `assigned` when **both** hold:
   - a `scheduled` sailing exists on `return_date` (`OpenReturnTicket::schedule()`), and
   - `ShipCapacityService::canAllocate(date)` (`master_ships` total minus `assigned`/`used` on that date).
   - Capacity failure logs a warning and leaves the ticket `open`.
3. It is called from two places, one implementation:
   - at booking time (the sailing may already exist), and
   - `ScheduleObserver::created/updated` (a new sailing picks up every pending open return for that date).
4. Boarding (`BoardingController`):
   - QR and manual validation accept the ticket only after it is assigned, and match it to the selected schedule through `matchesSchedule()` (open returns have no `booking.schedule_id`).
   - `validateTicket()` resolves the sailing via `openReturnTicket->schedule()` instead of dereferencing a null schedule.
   - When the last passenger of the booking boards, `open_return_tickets.status` becomes `used`.

## Files

- `database/migrations/2026_09_28_000001_create_open_return_tickets_table.php`
- `database/migrations/2026_09_28_000002_create_master_ships_table.php`
- `app/Models/OpenReturnTicket.php`
- `app/Services/ShipCapacityService.php`
- `app/Models/Booking.php` (`openReturnTicket` hasOne)
- `app/Http/Controllers/BookingController.php` (`return_date` support)
- `app/Observers/ScheduleObserver.php` (assignment on sailing create/update)
- `app/Http/Controllers/BoardingController.php` (`matchesSchedule()`, assignment guard, `used` transition)
- Views show the return date when there is no departure schedule: `tickets/show`, `booking/{payment,success,history}`.

## Tests

`tests/Feature/OpenReturnTest.php` — assigned when a sailing exists, stays open without one, pending returns picked up by a new sailing, capacity 0 leaves it unassigned.

Run: `php artisan migrate --force && php artisan test`

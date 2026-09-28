# Auralis Local E2E QA Report

- Base URL: `http://127.0.0.1:8000`
- Date: 2026-09-28
- Tool: `ag-browser`
- Result: PASS

## Scenario 1: Admin Setup & Management

PASS. Admin login, vessel, route, schedule, promo, bookings, reports, report filters, refunds, and audit logs were checked.

Fixes found:

- Route creation returned HTTP 500 because `estimated_duration` was required by the database but absent from the form. Added a default 60-minute field.
- Schedule form exposed an empty optional age-price row and non-native date controls. Optional rows now start empty and date controls use native inputs.

Screenshots: `qa_01_admin_login.png`, `qa_02_admin_dashboard.png`, `qa_03_vessel_form.png`, `qa_04_vessel_created.png`, `qa_05_route_filled.png`, `qa_06_route_created.png`, `qa_07_schedule_filled.png`, `qa_08_promo_filled2.png`, `qa_09_admin_bookings.png`, `qa_10_admin_reports_filtered.png`, `qa_11_admin_refunds.png`, `qa_12_admin_audit_logs.png`.

## Scenario 2: User Register & Booking

PASS. Registration, passenger login, schedule booking fixture, promo discount verification (`80 -> 72`), booking detail, payment page, proof display, and open-return detail/payment were checked.

Fixes found:

- Guest layout loaded blocked CDN Echo and then attempted to instantiate an undefined `Echo`. Removed the unused external realtime scripts and initializer.
- Payment page returned HTTP 500 when `expires_at` was null. Expiry markup and countdown now tolerate bookings without expiry.

Screenshots: `qa_13_register.png`, `qa_14_register_filled.png`, `qa_15_booking_form.png`, `qa_16_booking_promo.png`, `qa_17_booking_detail.png`, `qa_18_payment_fixed.png`, `qa_42_open_return_detail.png`, `qa_43_open_return_payment.png`.

## Scenario 3: Ticket Counter

PASS. Counter login, counter dashboard, offline booking form, cash flow, counter booking detail, e-ticket and PDF links were checked.

Screenshots: `qa_19_counter_dashboard.png`, `qa_20_counter_form.png`, `qa_21_counter_filled.png`, `qa_22_counter_ticket.png`.

## Scenario 4: Boarding Officer

PASS. Boarding login, scanner, manual booking-code validation, successful boarding, and second-use rejection were checked. Ticket status changed to `used` after the first successful scan.

Screenshots: `qa_23_boarding_dashboard.png`, `qa_25_boarding_validated.png`, `qa_26_boarding_rejected_used.png`.

## Scenario 5: Deportation Officer

PASS. Deportation login, dashboard, booking form, payment page, history/manifest view, ticket page and deportation scanner were checked.

Fix found:

- `User::isDeportation()` only checked `account_type`, so the supplied `deportation_officer` role was redirected to Home. It now accepts the role as well.

Screenshots: `qa_27_deport_dashboard_fixed.png`, `qa_28_deport_booking_fixed.png`, `qa_29_deport_form_filled2.png`, `qa_30_deport_payment_waiting.png`, `qa_31_deport_manifest_list.png`, `qa_40_deport_scanner.png`, `qa_41_deport_ticket.png`.

## Scenario 6: Admin Approve & Verify

PASS. Admin re-login, payment approval for all awaiting payments, ticket generation, and post-approval boarding were checked.

Screenshots: `qa_32_admin_relogin.png`, `qa_33_admin_bookings_approval.png`, `qa_35_admin_payments.png`, `qa_36_admin_approved_one.png`, `qa_37_admin_approved_all.png`, `qa_39_boarding_regular_pass.png`.

## Verification

- `php artisan test --compact`: 35 tests passed, 84 assertions.
- Targeted Pint: passed.
- `git diff --check`: passed.
- Final browser error buffer: no console, HTTP, or network errors on verified pages.

Screenshots are stored in `~/.antigravity/screenshots/`.

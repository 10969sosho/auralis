<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BoardingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DeportationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PassengerProfileController;
use App\Http\Controllers\SeatAvailabilityController;
use App\Models\Route as RouteModel;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $prices = Schedule::where('status', 'scheduled')
        ->where('departure_time', '>=', now())
        ->where('is_active', true)
        ->with('route')
        ->select('route_id', 'vip_price', 'regular_price')
        ->distinct()
        ->get()
        ->groupBy(fn($s) => $s->route->origin_port . '→' . $s->route->destination_port)
        ->map(fn($group) => $group->first())
        ->take(3);

    $schedules = Schedule::where('status', 'scheduled')
        ->where('departure_time', '>=', now())
        ->where('is_active', true)
        ->with(['route', 'vessel'])
        ->orderBy('departure_time')
        ->take(3)
        ->get();

    return view('home', compact('prices', 'schedules'));
})->name('home');
Route::get('/harga', function () {
    $prices = Schedule::where('status', 'scheduled')
        ->where('departure_time', '>=', now())
        ->where('is_active', true)
        ->with('route')
        ->select('route_id', 'vip_price', 'regular_price')
        ->distinct()
        ->get()
        ->groupBy(fn($s) => $s->route->origin_port . '→' . $s->route->destination_port)
        ->map(fn($group) => $group->first());

    $ports = RouteModel::where('active', true)
        ->pluck('origin_port')
        ->unique()
        ->values();

    return view('harga', compact('prices', 'ports'));
})->name('harga');
Route::get('/pengumuman', fn () => view('pengumuman'))->name('pengumuman');
Route::get('/pengumuman/{id}', fn ($id) => view('pengumuman-detail', ['id' => $id]))->name('pengumuman.detail');
Route::get('/informasi', fn () => view('informasi'))->name('informasi');
Route::get('/jadwal', fn () => view('jadwal'))->name('jadwal');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/schedules', [BookingController::class, 'search'])->name('schedules');
Route::get('/seat-availability', [SeatAvailabilityController::class, 'index'])->name('seat-availability');
Route::get('/booking/{schedule}', [BookingController::class, 'show'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->middleware('auth', 'throttle:10,1')->name('booking.store');
Route::get('/booking/{code}/payment', [BookingController::class, 'showPayment'])->name('booking.payment');
Route::post('/booking/{code}/payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
Route::get('/booking/{code}/success', [BookingController::class, 'success'])->name('booking.success');
Route::get('/booking/{code}/detail', [BookingController::class, 'showBooking'])->name('booking.detail');
Route::post('/booking/{code}/refund', [BookingController::class, 'refundRequest'])->name('booking.refund');

// ToyibPay payment gateway routes
Route::get('/booking/{code}/toyibpay-return', [BookingController::class, 'toyibpayReturn'])->name('booking.toyibpay-return');
Route::post('/booking/toyibpay-callback', [BookingController::class, 'toyibpayCallback'])
    ->name('booking.toyibpay-callback')
    ->middleware('throttle:60,1');
Route::get('/booking/{code}/check-status', [BookingController::class, 'checkPaymentStatus'])
    ->name('booking.check-status')
    ->middleware('throttle:30,1');

Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'history'])->name('booking.history');
    Route::post('/booking/{code}/cancel-expired', [BookingController::class, 'cancelExpired'])->name('booking.cancel-expired');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    Route::get('/profiles', [PassengerProfileController::class, 'index'])->name('profiles.index');
    Route::post('/profiles', [PassengerProfileController::class, 'store'])->name('profiles.store');
    Route::delete('/profiles/{profile}', [PassengerProfileController::class, 'destroy'])->name('profiles.destroy');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
});

Route::middleware(['auth', 'role:boarding_officer,admin'])->prefix('boarding')->name('boarding.')->group(function () {
    Route::get('/scanner', [BoardingController::class, 'scanner'])->name('scanner');
    Route::post('/scan', [BoardingController::class, 'scan'])->name('scan');
    Route::post('/manual-validate', [BoardingController::class, 'manualValidate'])->name('manual-validate');
    Route::get('/manifest/{schedule}', [BoardingController::class, 'manifest'])->name('manifest');
});

Route::middleware(['auth', 'role:deportation_officer,admin'])->prefix('deportation')->name('deportation.')->group(function () {
    Route::get('/', [DeportationController::class, 'index'])->name('index');
    Route::get('/create', [DeportationController::class, 'create'])->name('create');
    Route::post('/manifests', [DeportationController::class, 'storeManifest'])->name('manifests.store');
    Route::get('/manifests/{code}', [DeportationController::class, 'showManifest'])->name('manifest.show');
    Route::post('/manifests/{manifest}/passengers', [DeportationController::class, 'addPassenger'])->name('passengers.store');
    Route::post('/boarding/scan', [DeportationController::class, 'boardingScan'])->name('boarding.scan');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Reports
    Route::get('/report-list', [App\Http\Controllers\AdminReportController::class, 'index'])->name('reports.index');
    Route::prefix('exports')->name('reports.')->group(function () {
        Route::get('/csv', [App\Http\Controllers\AdminReportController::class, 'exportCsv'])->name('csv');
        Route::get('/excel', [App\Http\Controllers\AdminReportController::class, 'exportExcel'])->name('excel');
    });

    // Schedule Passenger List (Show)
    Route::get('/schedules/{schedule}/passengers', [App\Http\Controllers\AdminScheduleController::class, 'passengers'])->name('schedule.passengers');
    Route::get('/schedules/{schedule}/passengers/export', [App\Http\Controllers\AdminScheduleController::class, 'exportPassengers'])->name('schedule.passengers.export');
});

Route::middleware(['auth', 'role:ticket_counter_officer,admin'])->prefix('counter')->name('counter.')->group(function () {
    Route::get('/', [App\Http\Controllers\CounterController::class, 'dashboard'])->name('dashboard');
    Route::get('/create/{schedule}', [App\Http\Controllers\CounterController::class, 'newBooking'])->name('create');
    Route::post('/store', [App\Http\Controllers\CounterController::class, 'store'])->name('store');
    Route::get('/success', [App\Http\Controllers\CounterController::class, 'success'])->name('success');
    Route::get('/search', [App\Http\Controllers\CounterController::class, 'search'])->name('search');
    Route::get('/history', [App\Http\Controllers\CounterController::class, 'history'])->name('history');
    Route::get('/booking/{code}', [App\Http\Controllers\CounterController::class, 'detail'])->name('detail');
    Route::post('/booking/{code}/refund', [App\Http\Controllers\CounterController::class, 'refundRequest'])->name('refund');
});

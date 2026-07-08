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
use App\Models\Vessel;
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
Route::get('/prices', function () {
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

    return view('prices', compact('prices', 'ports'));
})->name('prices');
Route::get('/announcements', fn () => view('announcements'))->name('announcements');
Route::get('/announcements/{id}', fn ($id) => view('announcement-detail', ['id' => $id]))->name('announcements.detail');
Route::get('/information', fn () => view('information'))->name('information');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', fn () => view('auth.register-choice'))->name('register');
    Route::get('/register/regular', [AuthController::class, 'showRegister'])->name('register.regular');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/api/countries', function () {
    $cacheKey = 'countries_list_v3';
    $countries = cache()->remember($cacheKey, 86400, function () {
        $client = new \GuzzleHttp\Client(['timeout' => 15]);

        try {
            $response = $client->get('https://restcountries.com/v3.1/all?fields=name,cca2');
            $data = json_decode($response->getBody(), true);

            if (!is_array($data)) {
                return [];
            }

            $list = array_map(function ($c) {
                $name = $c['name']['common'] ?? '';
                return [
                    'value' => $name,
                    'text'  => $name,
                ];
            }, $data);

            $list = array_filter($list, fn($c) => !empty($c['value']));

            usort($list, fn($a, $b) => strcmp($a['text'], $b['text']));

            return array_values($list);
        } catch (\Exception $e) {
            Log::warning('Countries API failed', ['error' => $e->getMessage()]);
            return [];
        }
    });

    return response()->json($countries);
})->name('api.countries');

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

// ==========================
// Deportation User Routes (pengguna deportasi - ship ticket only)
// ==========================
Route::get('/deportation/register', [DeportationController::class, 'showRegister'])->name('deportation.register');
Route::post('/deportation/register', [DeportationController::class, 'register'])->name('deportation.register.store');

Route::middleware('auth')->prefix('deportation')->name('deportation.')->group(function () {
    Route::get('/dashboard', [DeportationController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking', [DeportationController::class, 'showBooking'])->name('booking');
    Route::post('/booking', [DeportationController::class, 'storeBooking'])->name('booking.store')->middleware('throttle:10,1');
    Route::get('/payment/{code}', [DeportationController::class, 'showPayment'])->name('payment');
    Route::post('/payment/{code}', [DeportationController::class, 'processPayment'])->name('payment.process');
    Route::get('/success/{code}', [DeportationController::class, 'success'])->name('success');
    Route::get('/ticket/{ticket}', [DeportationController::class, 'showTicket'])->name('ticket');
    Route::get('/history', [DeportationController::class, 'history'])->name('history');
    Route::get('/check-status/{code}', [DeportationController::class, 'checkPaymentStatus'])
        ->name('check-status')->middleware('throttle:30,1');
});

// ToyibPay callbacks for deportation (public)
Route::get('/deportation/{code}/toyibpay-return', [DeportationController::class, 'toyibpayReturn'])
    ->name('deportation.toyibpay-return')->middleware('auth');
Route::post('/deportation/toyibpay-callback', [DeportationController::class, 'toyibpayCallback'])
    ->name('deportation.toyibpay-callback')->middleware('throttle:60,1');

// ==========================
// Deportation Officer Routes (petugas - manifests, boarding scan)
// ==========================
Route::middleware(['auth', 'role:boarding_officer,deportation_officer,admin'])->prefix('deportation')->name('deportation.')->group(function () {
    Route::get('/scanner', [DeportationController::class, 'scanner'])->name('scanner');
    Route::post('/scan', [DeportationController::class, 'scan'])->name('scan');
});

Route::middleware(['auth', 'role:deportation_officer,admin'])->prefix('deportation/manifests')->name('deportation.')->group(function () {
    Route::get('/', [DeportationController::class, 'index'])->name('index');
    Route::get('/create', [DeportationController::class, 'create'])->name('create');
    Route::post('/', [DeportationController::class, 'storeManifest'])->name('manifests.store');
    Route::get('/{code}', [DeportationController::class, 'showManifest'])->name('manifest.show');
    Route::post('/{manifest}/passengers', [DeportationController::class, 'addPassenger'])->name('passengers.store');
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
    Route::get('/schedules/{schedule}/passengers/export/pdf', [App\Http\Controllers\AdminScheduleController::class, 'exportToPdf'])->name('schedule.passengers.export.pdf');
    Route::get('/schedules/{schedule}/passengers/export/excel', [App\Http\Controllers\AdminScheduleController::class, 'exportToExcel'])->name('schedule.passengers.export.excel');
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

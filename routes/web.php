<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BoardingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DeportationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PassengerProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/schedules', [BookingController::class, 'search'])->name('schedules');
Route::get('/booking/{schedule}', [BookingController::class, 'show'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->middleware('auth')->name('booking.store');
Route::get('/booking/{code}/payment', [BookingController::class, 'showPayment'])->name('booking.payment');
Route::post('/booking/{code}/payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
Route::get('/booking/{code}/success', [BookingController::class, 'success'])->name('booking.success');
Route::get('/booking/{code}/detail', [BookingController::class, 'showBooking'])->name('booking.detail');
Route::post('/booking/{code}/refund', [BookingController::class, 'refundRequest'])->name('booking.refund');

Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'history'])->name('booking.history');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    Route::get('/profiles', [PassengerProfileController::class, 'index'])->name('profiles.index');
    Route::post('/profiles', [PassengerProfileController::class, 'store'])->name('profiles.store');
    Route::delete('/profiles/{profile}', [PassengerProfileController::class, 'destroy'])->name('profiles.destroy');
});

Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');

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
    Route::get('/reports', [App\Http\Controllers\AdminReportController::class, 'index'])->name('reports');
    Route::get('/reports/{schedule}', [App\Http\Controllers\AdminReportController::class, 'detail'])->name('reports.detail');
    Route::get('/reports/export/csv', [App\Http\Controllers\AdminReportController::class, 'exportCsv'])->name('reports.csv');
});

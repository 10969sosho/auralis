<?php

use App\Http\Controllers\SeatAvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/schedules/{schedule}/seat-availability', [SeatAvailabilityController::class, 'show']);

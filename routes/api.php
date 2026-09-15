<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class);
Route::get('/events/{slug}', [EventController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);

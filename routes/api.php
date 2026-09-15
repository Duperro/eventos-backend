<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/events/{slug}', [EventController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);

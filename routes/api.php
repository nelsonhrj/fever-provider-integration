<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventsController;

Route::middleware('api')->group(function () {
    Route::get('/events', [EventsController::class, 'index'])
        ->name('api.events.index');
});

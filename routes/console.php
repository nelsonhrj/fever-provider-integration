<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('provider:sync')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/provider-sync.log'));

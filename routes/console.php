<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dọn dẹp audio Speaking cũ hơn 30 ngày (mỗi ngày một lần lúc 02:00 sáng)
Schedule::command('ai:clean-audio --days=30')->dailyAt('02:00');

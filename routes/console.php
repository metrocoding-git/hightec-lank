<?php

use App\Http\Controllers\ScanAttendanceController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// "auto attendance ra 12ta wadanna, 6.30ta off wuna wage out nokarapu okkoma"
// — every night at 12:00 midnight this closes every attendance session
// (Daily course attendance, Theory, Practical) from a previous day that is
// still missing a time-out. The time written is 6:30 PM of that day (or the
// time-in if it started after 6:30 PM), and the row is marked "AUTO OUT"
// with a note saying the out scan was missed. Safe to run any number of times.
Artisan::command('attendance:auto-out', function () {
    $closed = ScanAttendanceController::autoCloseMissingDailyOut();
    $this->info("Closed {$closed} attendance session(s) with a missing time-out.");
})->purpose('Auto time-out (6:30 PM) for attendance still missing a time-out on previous days');

// Needs the usual Laravel cron entry on the server (runs every minute, Laravel
// decides what is due):
//   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
// Check with:  php artisan schedule:list
// (The Scan Attendance and Attendance Card pages also run it when opened, so
// it still happens even without cron.)
Schedule::command('attendance:auto-out')
    ->dailyAt(ScanAttendanceController::AUTO_OUT_RUN_TIME)
    ->timezone('Asia/Colombo')
    ->withoutOverlapping();

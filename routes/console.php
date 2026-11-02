<?php

use App\Http\Controllers\ScanAttendanceController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// "daly attandence can be some time missing out.. like that time auto do out
// 5.00pm on that day" + "want work every daya 10.30" — every night at 10:30 PM
// this closes Daily Attendance still missing a time-out: today's and any
// earlier day's. The time written is 5:00 PM (or the end of that day's last
// activity if later), and the row is marked "AUTO OUT". Safe to run any
// number of times.
Artisan::command('attendance:auto-out', function () {
    $closed = ScanAttendanceController::autoCloseMissingDailyOut();
    $this->info("Closed {$closed} daily attendance session(s) with a missing time-out.");
})->purpose('Auto time-out Daily Attendance still missing a time-out');

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

<?php

use App\Http\Controllers\ScanAttendanceController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// "daly attandence can be some time missing out.. like that time auto do out
// 5.00pm on that day" — closes every Daily Attendance from a previous day that
// still has no time-out (5:00 PM, or the end of the last activity that day if
// later). Safe to run any number of times.
Artisan::command('attendance:auto-out', function () {
    $closed = ScanAttendanceController::autoCloseMissingDailyOut();
    $this->info("Closed {$closed} daily attendance session(s) with a missing time-out.");
})->purpose('Auto time-out Daily Attendance left open on previous days');

// Runs a little after midnight every day. Needs the usual Laravel cron entry
// on the server:  * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
// (The Scan Attendance and Attendance Card pages also run this on load, so it
// still happens even without cron.)
Schedule::command('attendance:auto-out')->dailyAt('00:05')->timezone('Asia/Colombo');

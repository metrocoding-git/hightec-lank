<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\CourseIntakeController;
use Illuminate\Support\Facades\Route;

// Course routes
Route::post('/courses', [CourseController::class, 'store']);
Route::get('courses', [CourseController::class, 'index']);
Route::put('courses/{id}', [CourseController::class, 'update']);
Route::delete('courses/{id}', [CourseController::class, 'destroy']);

// branch routes
Route::post('/branches', [BranchController::class, 'store']);
Route::get('branches', [BranchController::class, 'index']);
Route::put('branches/{id}', [BranchController::class, 'update']);
Route::delete('branches/{id}', [BranchController::class, 'destroy']);


// intake routes
Route::post('/intakes', [IntakeController::class, 'store']);
Route::get('intakes', [IntakeController::class, 'index']);
Route::put('intakes/{id}', [IntakeController::class, 'update']);
Route::delete('intakes/{id}', [IntakeController::class, 'destroy']);


// course intake routes
Route::post('/course-intake', [CourseIntakeController::class, 'store']);
Route::get('course-intake', [CourseIntakeController::class, 'index']);
Route::put('course-intakes/{id}', [CourseIntakeController::class, 'update']);
Route::delete('course-intakes/{id}', [CourseIntakeController::class, 'destroy']);
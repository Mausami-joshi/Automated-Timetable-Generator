<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\FacultyAvailabilityController;
use App\Http\Controllers\TimetableController;

// Login (guest only)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected admin routes
Route::middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('teachers', TeacherController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('classrooms', ClassroomController::class);
    Route::resource('timeslots', TimeslotController::class);
    Route::resource('faculty-availabilities', FacultyAvailabilityController::class);

    Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    Route::post('/timetable/generate', [TimetableController::class, 'generate'])->name('timetable.generate');
    Route::get('/timetable/export/{type}', [TimetableController::class, 'export'])->name('timetable.export');
});

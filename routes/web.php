<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DemoTestController;
use App\Http\Controllers\Student\ViolationController;


/*
|--------------------------------------------------------------------------
| Welcome Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/demo-test', [DemoTestController::class, 'index'])->name('demo.index');
Route::get('/demo-test/{slug}', [DemoTestController::class, 'start'])->name('demo.start');
Route::post('/demo-test/{slug}', [DemoTestController::class, 'submit'])->name('demo.submit');
Route::get('/demo-test/{slug}/result', [DemoTestController::class, 'result'])->name('demo.result');


/*
|--------------------------------------------------------------------------
| Redirect After Login (ROLE BASED)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {

    $user = Auth::user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('student.dashboard'),
    };

})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| DEBUG (OPTIONAL)
|--------------------------------------------------------------------------
*/
Route::get('/debug-user', function () {
    return Auth::check() ? Auth::user() : 'Not logged in';
})->middleware('auth');


/*
|--------------------------------------------------------------------------
| ✅ STUDENT PROFILE ROUTES (ADDED — NOTHING ELSE CHANGED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/store-violation', [ViolationController::class, 'store'])->name('violations.store');
    Route::get('/exam-terminated', [ViolationController::class, 'terminated'])->name('exam.terminated');
});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';


/*
|--------------------------------------------------------------------------
| LOAD SEPARATED ROUTE FILES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/admin.php';
require __DIR__ . '/student.php';

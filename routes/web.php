<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DataTransaksiController;
use App\Http\Controllers\DataParkirController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginAdminController;
use App\Http\Controllers\Admin\WisataController;

Route::get('/', function () {
    return redirect('/login');
});


// Tampilkan Form Login Admin
Route::get('/login', [LoginAdminController::class, 'showLoginForm'])->name('admin.show_login');

// Proses Submit Login Admin
Route::post('/login', [LoginAdminController::class, 'login'])->name('admin.login');

// Proses Logout
Route::post('/logout', [LoginAdminController::class, 'logout'])->name('admin.logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('pages.dashboard');

// ROUTES USER
Route::get('/users', [UserController::class, 'index'])->name('pages.data-user'); // <-- PENTING
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// ROUTES ADMIN
Route::get('/admins', [AdminController::class, 'index'])->name('pages.data-admin'); // <-- PENTING
Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
Route::put('/admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');  

Route::resource('data-wisata', WisataController::class)
    ->parameters(['data-wisata' => 'wisata']) // Supaya di Controller pakainya (Wisata $wisata)
    ->names('wisata');


Route::resource('data-transaksi', DataTransaksiController::class)
    ->only(['index', 'update', 'destroy'])
    // --- PERUBAHAN: Pastikan 'transaction' cocok dengan variabel di controller ---
    ->parameters(['data-transaksi' => 'transaction']) 
    ->names('transaksi');


Route::resource('data-parkir', DataParkirController::class)
    ->only(['index', 'update', 'destroy'])
    ->parameters(['data-parkir' => 'parkirBooking'])
    ->names('parkir');
    
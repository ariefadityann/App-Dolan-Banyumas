<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User; 
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\ParkirBookingController;
use App\Http\Controllers\Admin\WisataApiController;

Route::prefix('dolanbanyumas')->group(function () {

    // Rute Publik (Register/Login)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rute Pembuatan Transaksi (Bisa juga diproteksi)
    Route::post('/midtrans/transaction', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/booking-parkir', [ParkirBookingController::class, 'createBooking']);
    
    // Rute Notifikasi (HARUS PUBLIK agar bisa diakses Midtrans)
    Route::post('/midtrans/notification', [MidtransController::class, 'notificationHandler']);

    // === WISATA API ROUTES (PUBLIC) ===
    // Get wisata dengan pagination & filter
    Route::get('/wisata', [WisataApiController::class, 'index']);
    
    // Get single wisata by ID
    Route::get('/wisata/{id}', [WisataApiController::class, 'show']);
    
    // Get all wisata tanpa pagination (untuk map/mobile)
    Route::get('/wisata-all', [WisataApiController::class, 'all']);
    
    // Get wisata by kategori
    Route::get('/wisata/kategori/{kategori}', [WisataApiController::class, 'byKategori']);
    
    // Get available categories
    Route::get('/wisata-categories', [WisataApiController::class, 'categories']);

    // --- RUTE YANG DIPROTEKSI ---
    // Semua rute di dalam grup ini memerlukan Token otentikasi
    Route::middleware('auth:sanctum')->group(function () {
        
        // Rute GET untuk riwayat (Sekarang aman)
        Route::get('/midtrans/transactions', [MidtransController::class, 'getTransactions']);
        Route::get('/midtrans/booking-parkir', [ParkirBookingController::class, 'getAllBookings']);
        Route::post('/midtrans/cancel-booking', [MidtransController::class, 'cancelBooking']);

        // Anda bisa pindahkan rute GET user ke sini juga
        Route::get('/users', function () {
            $users = User::where('role', 'user')->get();
            $users->makeHidden('password'); 
            return response()->json($users);
        });
    });
});
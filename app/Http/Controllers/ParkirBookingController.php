<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth; // Kita tidak pakai Auth
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\ParkirBooking;
use App\Models\User; // Pastikan ini di-import
use Carbon\Carbon;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class ParkirBookingController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    public function createBooking(Request $request)
    {
        // --- PERBAIKAN VALIDASI ---
        $validator = Validator::make($request->all(), [
            // Validasi 'first_name' dari Flutter dan cek ke kolom 'username' di DB
            'first_name' => 'required|string|exists:users,username', 

            // Validasi 'email' dari Flutter, TAPI tidak cek ke DB (karena kolomnya tidak ada)
            'email' => 'required|email',

            // Validasi booking tetap sama
            'parking_type' => 'required|string',
            'plat_nomor' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'total_harga' => 'required|numeric|min:1000',
            'tanggal_booking' => 'required|date',
        ]);
        // --- AKHIR PERBAIKAN VALIDASI ---

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
        }

        // --- PERBAIKAN PENCARIAN USER ---
        // Cari user berdasarkan 'username' (yang dikirim sebagai 'first_name' dari Flutter)
        $user = User::where('username', $request->first_name)->first(); //ubah di sini saya
        // --- AKHIR PERBAIKAN PENCARIAN USER ---

        if (!$user) {
             // Seharusnya tidak akan terjadi karena sudah divalidasi, tapi ini penjagaan
             return response()->json(['message' => 'User dengan nama ' . $request->first_name . ' tidak ditemukan.'], 404);
        }

        $orderId = 'PARK-' . time() . '-' . $user->id; // Baris ini sekarang aman

        // 1. Simpan booking ke database
        $booking = ParkirBooking::create([
            'order_id' => $orderId,
            'user_id' => $user->id, // user_id sekarang didapat dari pencarian nama
            'parking_type' => $request->parking_type,
            'plat_nomor' => $request->plat_nomor,
            'jumlah' => $request->jumlah,
            'total_harga' => $request->total_harga,
            'tanggal_booking' => Carbon::parse($request->tanggal_booking)->toDateString(),
            'status' => 'pending',
        ]);

        // 2. Siapkan parameter untuk Midtrans
        $transaction_details = [
            'order_id' => $orderId,
            'gross_amount' => $request->total_harga,
        ];

        $item_details = [
            [
                'id' => 'PARK-' . Str::slug($request->parking_type),
                'price' => $request->total_harga,
                'quantity' => 1,
                'name' => 'Booking Parkir: ' . $request->parking_type,
            ],
        ];

        // --- PERBAIKAN CUSTOMER DETAILS ---
        $customer_details = [
            // Ambil nama dari $user yang ditemukan
            'first_name' => $user->username, 
            
            // Ambil email dari $request (dari Flutter), karena $user tidak punya email
            'email' => $request->email,
            
            // Ambil no_wa dari $user
            'phone' => $user->no_wa ?? '0800000000', 
        ];
        // --- AKHIR PERBAIKAN CUSTOMER DETAILS ---

        $transaction = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details,
            'enabled_payments' => ['gopay', 'shopeepay', 'qris', 'bca_va', 'bni_va', 'bri_va'],
        ];

        try {
            $snap = Snap::createTransaction($transaction);
            
            $booking->midtrans_token = $snap->token;
            $booking->midtrans_url = $snap->redirect_url;
            $booking->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking berhasil dibuat.',
                'booking' => $booking,
                'redirect_url' => $snap->redirect_url,
            ]);

        } catch (\Exception $e) {
            $booking->delete();
            return response()->json(['message' => 'Gagal membuat transaksi Midtrans', 'error' => $e->getMessage()], 500);
        }
    }
    public function getAllBookings()
    {
        $userId = auth()->id(); // Mengambil ID user yang sedang login

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Tidak terotentikasi'], 401);
        }

        $bookings = \DB::table('parkir_bookings')
            ->join('users', 'parkir_bookings.user_id', '=', 'users.id') // Join ke tabel user
            ->where('parkir_bookings.user_id', $userId) // Filter berdasarkan user ID
            // ->where('parkir_bookings.status', 'success') // Hanya tampilkan yang sukses
            ->select(
                'parkir_bookings.*', 
                'users.username' // Ambil username user
            )
            ->orderBy('parkir_bookings.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'pending' => true,
            'data' => $bookings
        ]);
    }
}
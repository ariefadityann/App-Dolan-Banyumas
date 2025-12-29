<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Transaction;    
use App\Models\ParkirBooking; 
use Illuminate\Support\Str;    
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MidtransController extends Controller
{
    /**
     * TUGAS 1: MEMBUAT TRANSAKSI TIKET WISATA
     * (Menyimpan ke tabel 'transactions')
     */
    public function createTransaction(Request $request)
    {
        // 1. Setup Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // 2. Buat Order ID Unik
        $orderId = 'TRX-' . time() . '-' . rand(100, 999);

        // 3. SIMPAN DATA KE DATABASE (Status Awal: Pending)
        try {
            $transaction = Transaction::create([
                'order_id'      => $orderId,
                'user_name'     => $request->first_name, // Asumsi dari Flutter
                'user_email'    => $request->email,      // Asumsi dari Flutter
                'wisata_name'   => $request->wisata_name,
                'visit_date'    => $request->visit_date,
                'total_tickets' => $request->quantity,
                'total_price'   => $request->gross_amount,
                'status'        => 'pending', // Status awal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }

        // 4. Siapkan Parameter Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $request->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $request->first_name,
                'email'      => $request->email,
            ],
            'item_details' => [
                [
                    'id'       => 'TIKET-WISATA',
                    'price'    => (int) ($request->gross_amount / $request->quantity),
                    'quantity' => (int) $request->quantity,
                    'name'     => "Tiket " . substr($request->wisata_name, 0, 40),
                ]
            ]
        ];

        try {
            // 5. Minta Snap Token
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan snap_token ke database
            $transaction->update(['snap_token' => $snapToken]);

            return response()->json([
                'status' => 'success',
                'redirect_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/$snapToken",
                'token' => $snapToken
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Midtrans Error: ' . $e->getMessage()], 500);
        }
    }


    /**
     * TUGAS 2: MENERIMA NOTIFIKASI DARI MIDTRANS (UNTUK SEMUA JENIS ORDER)
     * Ini adalah satu-satunya URL yang Anda daftarkan di dashboard Midtrans
     */
    public function notificationHandler(Request $request)
    {
        // Konfigurasi ulang untuk validasi notifikasi
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new \Midtrans\Notification();

            $transactionStatus = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status;

            // --- INI ADALAH LOGIKA "PINTAR" ---
            $booking = null; 

            if (Str::startsWith($order_id, 'PARK-')) {
                // Ini adalah booking parkir, cari di tabel parkir_bookings
                $booking = ParkirBooking::where('order_id', $order_id)->first();
            } else if (Str::startsWith($order_id, 'TRX-')) {
                // Ini adalah tiket wisata, cari di tabel transactions
                $booking = Transaction::where('order_id', $order_id)->first();
            }
            // --- AKHIR LOGIKA "PINTAR" ---


            if (!$booking) {
                // Jika tidak ditemukan di kedua tabel
                return response()->json(['message' => 'Transaction/Booking not found for order_id: ' . $order_id], 404);
            }

            // Update Status di tabel yang benar (ParkirBooking atau Transaction)
            if ($transactionStatus == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $booking->update(['status' => 'challenge']);
                    } else {
                        $booking->update(['status' => 'success']);
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                // Pembayaran Berhasil
                $booking->update(['status' => 'success']);
                
            } else if ($transactionStatus == 'pending') {
                // Menunggu Pembayaran
                $booking->update(['status' => 'pending']);
                
            } else if ($transactionStatus == 'deny') {
                // Ditolak
                $booking->update(['status' => 'failed']);
                
            } else if ($transactionStatus == 'expire') {
                // Kadaluarsa
                $booking->update(['status' => 'expired']);
                
            } else if ($transactionStatus == 'cancel') {
                // Dibatalkan
                $booking->update(['status' => 'canceled']);
            }

            return response()->json(['message' => 'Notification processed for ' . $order_id]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

   public function getTransactions()
    {
        $user = Auth::user(); // Mengambil user yang sedang login

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Tidak terotentikasi'], 401);
        }

        // Cari transaksi berdasarkan username user
        $transactions = Transaction::where('user_name', $user->username)
            // ->where('status', 'success') // Hanya tampilkan yang sukses
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'pending' => true,
            'data' => $transactions
        ]);
    }

    public function cancelBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Order ID tidak ada'], 422);
        }

        $order_id = $request->input('order_id');
        $user = auth()->user(); // <-- Ambil data user yang login

        if (!$user) {
             return response()->json(['message' => 'Tidak terotentikasi'], 401);
        }

        // Cari booking
        $booking = null;
        if (Str::startsWith($order_id, 'PARK-')) {
            // Asumsi ParkirBooking punya 'user_id'
            $booking = ParkirBooking::where('order_id', $order_id)->where('user_id', $user->id)->first();
        
        } else if (Str::startsWith($order_id, 'TRX-')) {
            // ==========================================================
            // PERUBAHAN PENTING:
            // Cari berdasarkan 'user_name' dan 'nama_lengkap' user,
            // karena di createTransaction Anda menyimpan 'user_name'
            // ==========================================================
            $booking = Transaction::where('order_id', $order_id)
                                ->where('user_name', $user->username)
                                ->first();
        }

        if (!$booking) {
            return response()->json(['message' => 'Pesanan tidak ditemukan atau bukan milik Anda'], 404);
        }

        // Cek apakah statusnya 'pending' sebelum dibatalkan
        if ($booking->status != 'pending') {
             return response()->json(['message' => 'Pesanan ini tidak bisa dibatalkan (status: ' . $booking->status . ')'], 400);
        }

        // Update status di DB
        $booking->update(['status' => 'canceled']);
        
        // TODO: Anda juga bisa memanggil API Midtrans untuk membatalkan transaksi di sana
        // \Midtrans\Transaction::cancel($order_id);

        return response()->json(['success' => true, 'message' => 'Pesanan telah dibatalkan']);
    }


}
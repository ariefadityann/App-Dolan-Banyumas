<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Register - Kirim OTP ke email untuk verifikasi
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_wa' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        // Cek apakah email sudah terdaftar
        $existingUserByEmail = User::where('email', $request->email)->first();
        
        // Jika email sudah ada DAN sudah verified
        if ($existingUserByEmail && $existingUserByEmail->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar dan terverifikasi. Silakan login atau gunakan email lain.'
            ], 422);
        }

        // Cek apakah username sudah digunakan (kecuali oleh user dengan email yang sama)
        $existingUserByUsername = User::where('username', $request->username)
                                      ->where('email', '!=', $request->email)
                                      ->first();
        
        if ($existingUserByUsername) {
            return response()->json([
                'success' => false,
                'message' => 'Username sudah terdaftar. Silakan gunakan username lain.'
            ], 422);
        }

        // Jika email sudah ada tapi belum verified, update data
        if ($existingUserByEmail && !$existingUserByEmail->email_verified_at) {
            // Update user data
            $existingUserByEmail->update([
                'username' => $request->username,
                'no_wa' => $request->no_wa,
                'password' => Hash::make($request->password),
            ]);
            
            $user = $existingUserByEmail;
            
            // Hapus OTP lama
            OtpVerification::where('email', $request->email)
                          ->where('type', 'email_verification')
                          ->delete();
        } else {
            // Buat user baru
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'no_wa' => $request->no_wa,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'email_verified_at' => null,
            ]);
        }

        // Generate OTP 6 digit
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan OTP ke database (expired 10 menit)
        OtpVerification::create([
            'email' => $request->email,
            'otp_code' => $otpCode,
            'type' => 'email_verification',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ]);

        // Kirim email OTP
        try {
            Mail::to($request->email)->send(new OtpMail($otpCode, 'email_verification'));
        } catch (\Exception $e) {
            // Log error tapi tetap return success (OTP sudah tersimpan di database)
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil! Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
            ]
        ], 201);
    }

    /**
     * Verify Email dengan OTP
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        // Cari OTP yang valid
        $otpRecord = OtpVerification::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'email_verification')
            ->where('is_verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa'
            ], 400);
        }

        // Update user email_verified_at
        $user = User::where('email', $request->email)->first();
        $user->update([
            'email_verified_at' => Carbon::now()
        ]);

        // Tandai OTP sebagai verified
        $otpRecord->update(['is_verified' => true]);

        // Generate token untuk auto-login
        $token = $user->createToken('auth_token_dolanbanyumas')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'no_wa' => $user->no_wa,
                    'role' => $user->role,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cari user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Cek user dan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Cek apakah email sudah diverifikasi
        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu.',
                'requires_verification' => true,
                'email' => $user->email
            ], 403);
        }

        // Generate token
        $token = $user->createToken('auth_token_dolanbanyumas')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'no_wa' => $user->no_wa,
                    'role' => $user->role,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Resend OTP untuk Email Verification
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah sudah verified
        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terverifikasi'
            ], 400);
        }

        // Hapus OTP lama yang belum verified
        OtpVerification::where('email', $request->email)
            ->where('type', 'email_verification')
            ->where('is_verified', false)
            ->delete();

        // Generate OTP baru
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan OTP baru
        OtpVerification::create([
            'email' => $request->email,
            'otp_code' => $otpCode,
            'type' => 'email_verification',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ]);

        // Kirim email OTP
        try {
            Mail::to($request->email)->send(new OtpMail($otpCode, 'email_verification'));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru telah dikirim ke email Anda. Silakan cek inbox atau folder spam.',
        ], 200);
    }

    /**
     * Forgot Password - Kirim OTP untuk reset password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Hapus OTP lama untuk forgot password
        OtpVerification::where('email', $request->email)
            ->where('type', 'password_reset')
            ->where('is_verified', false)
            ->delete();

        // Generate OTP baru
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan OTP
        OtpVerification::create([
            'email' => $request->email,
            'otp_code' => $otpCode,
            'type' => 'password_reset',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ]);

        // Kirim email OTP
        try {
            Mail::to($request->email)->send(new OtpMail($otpCode, 'password_reset'));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP untuk reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.',
            'data' => [
                'email' => $request->email,
            ]
        ], 200);
    }

    /**
     * Verify OTP untuk Forgot Password
     */
    public function verifyForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        // Cari OTP yang valid
        $otpRecord = OtpVerification::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'password_reset')
            ->where('is_verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa'
            ], 400);
        }

        // Tandai OTP sebagai verified (tapi jangan hapus, masih perlu untuk reset password)
        $otpRecord->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP valid. Silakan masukkan password baru.',
            'data' => [
                'email' => $request->email,
                'otp_verified' => true,
            ]
        ], 200);
    }

    /**
     * Reset Password (setelah OTP verified)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        // Cek OTP yang sudah verified
        $otpRecord = OtpVerification::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'password_reset')
            ->where('is_verified', true)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa'
            ], 400);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus OTP record
        $otpRecord->delete();

        // Hapus semua token lama (logout dari semua device)
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset! Silakan login dengan password baru.',
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}

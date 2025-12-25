<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('otp_code', 6); // 6 digit OTP
            $table->enum('type', ['email_verification', 'password_reset']); // Jenis OTP
            $table->timestamp('expires_at'); // Waktu kadaluarsa OTP
            $table->boolean('is_verified')->default(false); // Status verifikasi
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['email', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};

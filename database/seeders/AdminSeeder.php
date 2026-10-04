<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah admin sudah ada
        $adminExists = User::where('email', 'admin@dolanbanyumas.com')->exists();
        
        if (!$adminExists) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@dolanbanyumas.com',
                'no_wa' => '081234567890',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Admin user created successfully!');
            $this->command->info('📧 Email: admin@dolanbanyumas.com');
            $this->command->info('🔑 Password: admin123');
        } else {
            $this->command->warn('⚠️  Admin user already exists!');
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = UserRole::where('name', 'admin')->first();
        $architectRole = UserRole::where('name', 'architect')->first();

        // Create default admin user
        if (!User::where('email', 'admin@sacap.co.za')->exists()) {
            $admin = User::create([
                'name' => 'SACAP Administrator',
                'email' => 'admin@sacap.co.za',
                'password' => Hash::make('SecureAdmin123!'),
                'role_id' => $adminRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'company' => 'SACAP',
            ]);

            $this->command->info('Admin user created: admin@sacap.co.za / SecureAdmin123!');
        }

        // Create sample architect user
        if (!User::where('email', 'architect@example.com')->exists()) {
            $architect = User::create([
                'name' => 'John Smith',
                'email' => 'architect@example.com',
                'password' => Hash::make('password'),
                'role_id' => $architectRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'company' => 'Smith Architecture',
                'registration_number' => 'SACAP12345',
                'phone' => '+27123456789',
            ]);

            // Give the architect some initial tokens
            $architect->getTokenBalance()->addTokens(100, 'Welcome bonus', 0, ['welcome_bonus' => true]);

            $this->command->info('Sample architect created: architect@example.com / password');
        }
    }
}
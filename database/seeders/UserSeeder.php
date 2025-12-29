<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist (seeded by RolePermissionSeeder)
        $superRole = Role::findByName('super-admin', 'web');
        $adminRole = Role::findByName('admin', 'web');

        // ============================================
        // SUPER ADMIN ACCOUNTS (3 users)
        // ============================================

        // 1. Pa Ipong (Super Admin)
        $paIpong = User::firstOrCreate(
            ['email' => 'ipong@gmail.com'],
            [
                'name' => 'Ipong',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$paIpong->hasRole($superRole->name)) {
            $paIpong->assignRole($superRole);
        }

        // 2. Pa Adi (Super Admin)
        $paAdi = User::firstOrCreate(
            ['email' => 'adhi@gmail.com'],
            [
                'name' => 'Adhi',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$paAdi->hasRole($superRole->name)) {
            $paAdi->assignRole($superRole);
        }

        // 3.  Bagas (Super Admin)
        $bagas = User::firstOrCreate(
            ['email' => 'bagas@gmail.com'],
            [
                'name' => 'Bagas',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$bagas->hasRole($superRole->name)) {
            $bagas->assignRole($superRole);
        }

        // ============================================
        // ADMIN ACCOUNT (1 user)
        // ============================================

        // 4. Bule (Admin)
        $lutfi = User::firstOrCreate(
            ['email' => 'lutfi@gmail.com'],
            [
                'name' => 'Lutfi',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        if (!$lutfi->hasRole($adminRole->name)) {
            $lutfi->assignRole($adminRole);
        }

        // Output info
        $this->command->info('✅ Default users created successfully:');
        $this->command->info('   - Pa Ipong (Super Admin): ipong@gmail.com');
        $this->command->info('   - Pa Adhi (Super Admin): adhi@gmail.com');
        $this->command->info('   - Bagas (Super Admin): bagas@gmail.com');
        $this->command->info('   - Lutfi (Admin): lutfi@gmail.com');
        $this->command->info('   Default password: password');
    }
}

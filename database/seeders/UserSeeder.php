<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@brilliantcomputer.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Finance Staff',
                'email'    => 'finance@brilliantcomputer.com',
                'password' => Hash::make('finance123'),
                'role'     => 'finance',
            ],
            [
                'name'     => 'Cashier',
                'email'    => 'cashier@brilliantcomputer.com',
                'password' => Hash::make('cashier123'),
                'role'     => 'cashier',
            ],
            [
                'name'     => 'Inventory Staff',
                'email'    => 'inventory@brilliantcomputer.com',
                'password' => Hash::make('inventory123'),
                'role'     => 'inventory',
            ],
            [
                'name'     => 'HR Staff',
                'email'    => 'hr@brilliantcomputer.com',
                'password' => Hash::make('hr123'),
                'role'     => 'hr',
            ],
            [
                'name'     => 'Manager',
                'email'    => 'manager@brilliantcomputer.com',
                'password' => Hash::make('manager123'),
                'role'     => 'manager',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('✓ Users (6) seeded — admin/finance/cashier/inventory/hr/manager');
    }
}

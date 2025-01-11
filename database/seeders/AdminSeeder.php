<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['password' => Hash::make('nagoyameshi')]
        );

        $admin2 = Admin::firstOrCreate(
            ['email' => 'admin2@example.com'],
            ['password' => Hash::make('admin2_nagoyameshi')]
        );
    }
}

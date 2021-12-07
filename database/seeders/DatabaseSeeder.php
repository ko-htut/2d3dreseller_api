<?php

namespace Database\Seeders;

use App\Models\Number;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'phone' => '+959792239810',
        ]);

        for($x=0;$x < 100; $x++)
        {
            Number::create([
                'number' => Str::padLeft($x, 2, '0'),
                'type'  => '2D'
            ]);
        }
    }
}

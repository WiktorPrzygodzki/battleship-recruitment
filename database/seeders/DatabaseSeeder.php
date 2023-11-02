<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()
            ->create([
                'name' => 'admin',
                'email' => 'admin@admin.lol',
                'password' => 'adminlol',
                'token' => '123123123'
            ]);

        \App\Models\User::factory()
            ->create([
                'name' => 'user',
                'email' => 'user@user.lol',
                'password' => 'userlol',
                'token' => '321321321'
            ]);
    }
}

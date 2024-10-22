<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Developer',
            'email' => 'dev@psa.com',
            'password' => bcrypt(123123),
        ]);
    }
}

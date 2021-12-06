<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'email' => 'student@user.com',
        ], [
            'first_name' => 'Student',
            'last_name' => 'User',
            'password' => Hash::make('123456'),
            'role' => User::ROLE_STUDENT,
        ]);

        User::firstOrCreate([
            'email' => 'teacher@user.com',
        ], [
            'first_name' => 'Teacher',
            'last_name' => 'User',
            'password' => Hash::make('123456'),
            'role' => User::ROLE_TEACHER,
        ]);
    }
}

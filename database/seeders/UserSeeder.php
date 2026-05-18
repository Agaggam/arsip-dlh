<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua departemen kecuali 'System'
        $departmentIds = Department::where('name', '!=', 'System')->pluck('id')->toArray();

        // Role user = 3 (user biasa | tingkatan role paling bawah)
        $userRoleId = 3;

        for ($i = 0; $i < 50; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role_id' => $userRoleId,
                'department_id' => $faker->randomElement($departmentIds),
                'status' => 'pending',
                'email_verified_at' => now(),
            ]);
        }
    }
}
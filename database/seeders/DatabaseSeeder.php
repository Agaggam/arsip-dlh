<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(RoleSeeder::class);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('superadmin123'),
            'role_id' => 1, // ID dari role 'super_admin'
            'department_id' => 1, // ID dari departemen
        ]);

        User::factory()->create([
            'name' => 'Admin1',
            'email' => 'admin1@example.com',
            'password' => bcrypt('admin123'),
            'role_id' => 2, // ID dari role 'admin'
            'department_id' => 2, // ID dari departemen
        ]);

        User::factory()->create([
            'name' => 'Admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('admin123'),
            'role_id' => 2, // ID dari role 'admin'
            'department_id' => 3, // ID dari departemen
        ]);

        User::factory()->create([
            'name' => 'Admin3',
            'email' => 'admin3@example.com',
            'password' => bcrypt('admin123'),
            'role_id' => 2, // ID dari role 'admin'
            'department_id' => 4, // ID dari departemen
        ]);

        User::factory()->create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'role_id' => 3, // ID dari role 'user'
            'department_id' => 4, // ID dari departemen
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(DepartmentSeeder::class);

        $superAdminRoleId = Role::where('name', 'super_admin')->first()->id;
        $adminRoleId = Role::where('name', 'admin')->first()->id;
        $userRoleId = Role::where('name', 'user')->first()->id;

        $systemDeptId = Department::where('name', 'System')->first()->id;
        $sekretariatId = Department::where('name', 'Sekretariat')->first()->id;
        $tataLingkunganId = Department::where('name', 'Tata Lingkungan')->first()->id;
        $pengelolaanSampahId = Department::where('name', 'Pengelolaan Sampah')->first()->id;

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('superadmin123'),
            'role_id' => $superAdminRoleId,
            'department_id' => $systemDeptId,
        ]);

        User::create([
            'name' => 'Admin Sekretariat',
            'email' => 'admin1@example.com',
            'password' => bcrypt('admin123'),
            'role_id' => $adminRoleId,
            'department_id' => $sekretariatId,
        ]);

        User::create([
            'name' => 'Admin Tata Lingkungan',
            'email' => 'admin2@example.com',
            'password' => bcrypt('admin123'),
            'role_id' => $adminRoleId,
            'department_id' => $tataLingkunganId,
        ]);

        User::create([
            'name' => 'Admin Pengelolaan Sampah',
            'email' => 'admin3@example.com',
            'password' => bcrypt('admin123'),
            'role_id' => $adminRoleId,
            'department_id' => $pengelolaanSampahId,
        ]);

        User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'role_id' => $userRoleId,
            'department_id' => $pengelolaanSampahId,
        ]);

        // Aktifkan fungsi dibawah ini jika ingin membuat banyak user random
        $this->call(UserSeeder::class);
    }
}

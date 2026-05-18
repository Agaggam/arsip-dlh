<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
        ['name' => 'System', 'slug' => 'system'], // Digunakan Untuk menampung Super Admin yang Asli
        ['name' => 'Sekretariat', 'slug' => 'sekretariat'],
        ['name' => 'Tata Lingkungan', 'slug' => 'tata-lingkungan'],
        ['name' => 'Pengelolaan Sampah', 'slug' => 'pengelolaan-sampah'],
    ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}

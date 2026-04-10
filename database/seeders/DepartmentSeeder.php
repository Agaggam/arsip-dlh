<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
        ['name' => 'Sekretariat', 'slug' => 'sekretariat'],
        ['name' => 'Tata Lingkungan', 'slug' => 'tata-lingkungan'],
        ['name' => 'Pengelolaan Sampah', 'slug' => 'pengelolaan-sampah'],
    ];

        foreach ($departments as $dept) {
            \App\Models\Department::create($dept);
        }
    }
}

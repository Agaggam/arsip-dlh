<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Category;
use App\Models\Archive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredTrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_deletes_records_older_than_30_days_and_keeps_recent_ones(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $role = Role::create(['name' => 'super_admin']);
        $dept = Department::create(['name' => 'System', 'slug' => 'system', 'is_system' => true]);
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'department_id' => $dept->id,
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        $cat = Category::create([
            'name' => 'Umum',
            'slug' => 'umum',
            'department_id' => $dept->id,
        ]);

        Storage::disk('local')->put('archives/old_file.pdf', 'dummy content old');
        Storage::disk('local')->put('archives/new_file.pdf', 'dummy content new');

        $oldArchive = Archive::create([
            'title' => 'Arsip Kadaluarsa',
            'file_path' => 'archives/old_file.pdf',
            'file_type' => 'PDF',
            'file_size' => 1024,
            'category_id' => $cat->id,
            'user_id' => $user->id,
            'archive_date' => now()->subDays(40),
            'hash_token' => \Illuminate\Support\Str::random(32),
        ]);
        $oldArchive->delete();
        Archive::withTrashed()->where('id', $oldArchive->id)->update(['deleted_at' => now()->subDays(35)]);

        $newArchive = Archive::create([
            'title' => 'Arsip Baru Dihapus',
            'file_path' => 'archives/new_file.pdf',
            'file_type' => 'PDF',
            'file_size' => 1024,
            'category_id' => $cat->id,
            'user_id' => $user->id,
            'archive_date' => now(),
            'hash_token' => \Illuminate\Support\Str::random(32),
        ]);
        $newArchive->delete();
        Archive::withTrashed()->where('id', $newArchive->id)->update(['deleted_at' => now()->subDays(5)]);

        $this->artisan('trash:purge --days=30')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('archives', ['id' => $oldArchive->id]);
        Storage::disk('local')->assertMissing('archives/old_file.pdf');

        $this->assertDatabaseHas('archives', ['id' => $newArchive->id]);
        Storage::disk('local')->assertExists('archives/new_file.pdf');
    }
}



<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase; // Mengosongkan database testing setiap fungsi dijalankan

    private User $superAdmin;
    private User $admin;
    private User $regularUser;
    private Department $systemDept;
    private Department $sekretariatDept;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Palsukan storage disk local & Maatwebsite Excel agar tidak mengotori file asli
        Storage::fake('local');
        Excel::fake();

        // 2. Buat Master Roles wajib sesuai data aplikasimu
        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);

        // 3. Buat Master Departments wajib
        $this->systemDept = Department::create(['name' => 'System', 'slug' => 'system']);
        $this->sekretariatDept = Department::create(['name' => 'Sekretariat', 'slug' => 'sekretariat']);

        // 4. Buat User dummy dengan relasi department_id dan role_id yang valid
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'department_id' => $this->systemDept->id,
            'status' => 'approved'
        ]);

        $this->admin = User::create([
            'name' => 'Admin Sekretariat',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'department_id' => $this->sekretariatDept->id,
            'status' => 'approved'
        ]);

        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'department_id' => $this->sekretariatDept->id,
            'status' => 'approved'
        ]);
    }

    // =========================================================================
    // 1. TESTING SECURITY ACCESS (SUPER ADMIN VS NON-SUPER ADMIN)
    // =========================================================================

    #[Test]
    public function pure_super_admin_can_access_activity_logs_index_page()
    {
        Storage::disk('local')->put('exports/Logs_Juni_2026.xlsx', 'fake content');
        
        ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'causer_name' => $this->superAdmin->name,
            'causer_email' => $this->superAdmin->email,
            'activity' => 'Login Sistem',
            'ip_address' => '127.0.0.1'
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('activity-logs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.activity-logs.index');
        $response->assertViewHas('logs');
        $response->assertViewHas('exportFiles');
    }

    #[Test]
    public function non_super_admin_cannot_access_activity_logs_index_page()
    {
        // 1. Tes block untuk Admin/Staff
        $responseAdmin = $this->actingAs($this->admin)->get(route('activity-logs.index'));
        $responseAdmin->assertStatus(403);

        // 2. Tes block untuk User Biasa
        $responseUser = $this->actingAs($this->regularUser)->get(route('activity-logs.index'));
        $responseUser->assertStatus(403);
    }

    // =========================================================================
    // 2. TESTING SEARCH FILTER
    // =========================================================================

    #[Test]
    public function search_filter_works_correctly_on_index_page_for_super_admin()
    {
        ActivityLog::create(['activity' => 'Tambah User Baru', 'causer_name' => 'Sistem', 'ip_address' => '127.0.0.1']);
        ActivityLog::create(['activity' => 'Hapus Berkas Lama', 'causer_name' => 'Sistem', 'ip_address' => '127.0.0.1']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('activity-logs.index', ['search' => 'Tambah']));

        $response->assertStatus(200);
        $response->assertSee('Tambah User Baru');
        $response->assertDontSee('Hapus Berkas Lama');
    }

    // =========================================================================
    // 3. TESTING DOWNLOAD FUNCTIONALITY & SECURITY
    // =========================================================================

    #[Test]
    public function super_admin_can_download_existing_export_excel_file()
    {
        $filename = 'Logs_Juni_2026.xlsx';
        Storage::disk('local')->put('exports/' . $filename, 'Isi Berkas Excel Mocking');

        $response = $this->actingAs($this->superAdmin)->get(route('activity-logs.download', $filename));

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('content-type'));
    }

    #[Test]
    public function non_super_admin_is_forbidden_from_downloading_export_file()
    {
        $filename = 'Logs_Juni_2026.xlsx';
        Storage::disk('local')->put('exports/' . $filename, 'Isi Berkas Excel Mocking');

        // 1. Pastikan Admin/Staff diblokir dari fitur download log
        $responseAdmin = $this->actingAs($this->admin)->get(route('activity-logs.download', $filename));
        $responseAdmin->assertStatus(403);

        // 2. Pastikan User Biasa diblokir dari fitur download log
        $responseUser = $this->actingAs($this->regularUser)->get(route('activity-logs.download', $filename));
        $responseUser->assertStatus(403);
    }

    #[Test]
    public function download_returns_404_for_super_admin_if_file_is_missing()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('activity-logs.download', 'file_tidak_ada.xlsx'));

        $response->assertStatus(404);
    }

    // =========================================================================
    // 4. TESTING HELPER LOG ACTIVITY
    // =========================================================================

    #[Test]
    public function log_activity_helper_saves_data_accurately_into_database_for_logged_in_user()
    {
        log_activity($this->regularUser, 'Mengubah Password', 'User memperbarui password akun');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->regularUser->id,
            'causer_name' => $this->regularUser->name,
            'causer_email' => $this->regularUser->email,
            'activity' => 'Mengubah Password',
            'description' => 'User memperbarui password akun',
        ]);
    }

    // =========================================================================
    // 5. TESTING ARTISAN COMMAND SCHEDULE (ARCHIVE)
    // =========================================================================

    #[Test]
    public function artisan_command_successfully_archives_logs_and_purges_database()
    {
        ActivityLog::create([
            'activity' => 'Aktivitas Bulanan Sistem',
            'causer_name' => 'Sistem',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->startOfMonth()->addDays(5)
        ]);

        $this->artisan('logs:archive')
             ->expectsOutputToContain('Berhasil ekspor')
             ->assertExitCode(0);

        $expectedFileName = 'exports/Logs_' . now()->translatedFormat('F') . '_' . now()->year . '.xlsx';
        Excel::assertStored($expectedFileName, 'local');
        $this->assertDatabaseCount('activity_logs', 0);
    }

    #[Test]
    public function artisan_command_does_not_archive_if_database_is_empty()
    {
        $this->artisan('logs:archive')
             ->expectsOutputToContain('Tidak ada log untuk bulan')
             ->assertExitCode(0);

        $expectedFileName = 'exports/Logs_' . now()->translatedFormat('F') . '_' . now()->year . '.xlsx';
        Storage::disk('local')->assertMissing($expectedFileName);
    }
}
<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu proses registrasi
        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // Siapkan data Role dasar yang dibutuhkan oleh sistem saat user mendaftar
        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // 1. Buat department dummy untuk dipilih saat register
        $department = Department::create([
            'name' => 'Sekretariat',
            'slug' => 'sekretariat'
        ]);

        // 2. Kirim payload registrasi termasuk department_id
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'department_id'         => $department->id, // Wajib memilih departemen
        ]);

        // 3. Pastikan user berhasil login otomatis setelah register (walau belum verifikasi)
        $this->assertAuthenticated();
        
        // 4. PERBAIKAN: Pastikan dialihkan ke halaman verifikasi email, bukan langsung dashboard
        $response->assertRedirect('/verify-email');

        // 5. SELEKSI KETAT: Pastikan data tersimpan di database dengan status 'pending' dan role_id '3' (user)
        $this->assertDatabaseHas('users', [
            'email'         => 'test@example.com',
            'department_id' => $department->id,
            'status'        => 'pending', // Otomatis pending
            'role_id'       => 3,         // Otomatis menjadi role biasa (user)
            'email_verified_at' => null,  // Memastikan email memang belum terverifikasi saat awal daftar
        ]);
    }

    public function test_registration_fails_without_department(): void
    {
        // Test validasi: memastikan registrasi gagal jika department tidak diisi
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test2@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
            // 'department_id' sengaja dikosongkan
        ]);

        $response->assertSessionHasErrors('department_id');
        $this->assertGuest();
    }
}
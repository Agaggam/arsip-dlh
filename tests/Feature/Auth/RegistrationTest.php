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
        \Illuminate\Support\Facades\Mail::fake();

        // Kirim payload registrasi
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        // Pastikan user berhasil login otomatis setelah register (walau belum verifikasi)
        $this->assertAuthenticated();
        
        // Pastikan dialihkan ke halaman verifikasi OTP
        $response->assertRedirect(route('verification.otp'));

        // Pastikan data tersimpan di database dengan status 'pending' dan role_id '3' (user)
        $this->assertDatabaseHas('users', [
            'email'             => 'test@example.com',
            'status'            => 'pending',
            'role_id'           => 3,
            'email_verified_at' => null,
        ]);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OtpVerificationMail::class);
    }

    public function test_registration_fails_without_required_fields(): void
    {
        // Test validasi: memastikan registrasi gagal jika field wajib tidak diisi
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }
}
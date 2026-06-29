<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private Department $defaultDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu testing auth
        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // 1. Create roles terikat ID
        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);

        // 2. Create department default
        $this->defaultDept = Department::create([
            'name' => 'Sekretariat',
            'slug' => 'sekretariat'
        ]);
    }

    /**
     * Helper privat untuk membuat user yang valid dan siap login
     */
    private function createValidUser(array $attributes = []): User
    {
        $user = User::create(array_merge([
            'name'              => 'Auth Test User',
            'email'             => 'authtest@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->defaultDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ], $attributes));

        $user->markEmailAsVerified();
        return $user->load(['role', 'department']);
    }

    // ============================================================
    // TEST CASES
    // ============================================================

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->createValidUser();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = $this->createValidUser();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = $this->createValidUser();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * TEST BARU: Memastikan fitur throttle/rate limiter bekerja saat salah password berkali-kali
     */
    public function test_login_attempts_are_throttled_after_too_many_failures(): void
    {
        $user = $this->createValidUser();

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email'    => $user->email,
                'password' => 'wrong-password',
            ]);
            $this->assertGuest();
        }

        // Percobaan ke-6 harusnya memicu throttle rate-limiting
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
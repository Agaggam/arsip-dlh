<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private Department $defaultDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu proses testing
        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // 1. Create roles terikat ID dasar
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
     * Helper privat membuat user yang berstatus valid (Approved & Verified)
     */
    private function createValidUser(): User
    {
        $user = User::create([
            'name'              => 'Confirm Password User',
            'email'             => 'confirm@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->defaultDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);

        $user->markEmailAsVerified();
        return $user->load(['role', 'department']);
    }

    // ============================================================
    // TEST CASES
    // ============================================================

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = $this->createValidUser();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = $this->createValidUser();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = $this->createValidUser();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
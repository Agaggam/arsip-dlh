<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    private Department $defaultDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu proses pembaruan password
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
            'name'              => 'Password Update User',
            'email'             => 'updatepassword@example.com',
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

    public function test_password_can_be_updated(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password'      => 'password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password'      => 'wrong-password',
                'password'              => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}
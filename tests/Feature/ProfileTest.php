<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private Department $defaultDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu testing profil
        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // 1. Create roles terikat ID secara eksplisit sesuai kebutuhan sistem Anda
        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);

        // 2. Create department default untuk dilepaskan ke user baru
        $this->defaultDept = Department::create([
            'name' => 'Sekretariat',
            'slug' => 'sekretariat'
        ]);
    }

    /**
     * Helper privat untuk membuat User testing dengan status lengkap (Approved & Verified)
     */
    private function createValidUser(array $attributes = []): User
    {
        $user = User::create(array_merge([
            'name'              => 'Test User Original',
            'email'             => 'original@example.com',
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

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'New Profile Name',
                'email' => 'newemail@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('New Profile Name', $user->name);
        $this->assertSame('newemail@example.com', $user->email);
        
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Just Change Name',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
    //halo
}
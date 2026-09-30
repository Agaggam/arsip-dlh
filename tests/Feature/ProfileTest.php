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

    public function test_changing_email_sends_otp_and_does_not_update_email_immediately(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'New Profile Name',
                'email' => 'newemail@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'email-otp-sent')
            ->assertSessionHas('pending_email_change')
            ->assertRedirect('/profile');

        $user->refresh();

        // Nama diperbarui, tetapi email lama tetap aktif sebelum verifikasi OTP
        $this->assertSame('New Profile Name', $user->name);
        $this->assertSame('original@example.com', $user->email);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OtpVerificationMail::class, function ($mail) {
            return $mail->hasTo('newemail@example.com');
        });
    }

    public function test_verifying_otp_successfully_updates_email(): void
    {
        $user = $this->createValidUser();

        $this->actingAs($user)->withSession([
            'pending_email_change' => [
                'new_email'  => 'verifiednew@example.com',
                'otp'        => '123456',
                'expires_at' => now()->addMinutes(15),
            ]
        ])->post('/profile/email/verify', [
            'otp' => '123456',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('verifiednew@example.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull(session('pending_email_change'));
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
        $this->assertSame('Just Change Name', $user->name);
    }

    public function test_user_can_delete_their_account_without_password(): void
    {
        $user = $this->createValidUser();

        $response = $this
            ->actingAs($user)
            ->delete('/profile');

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
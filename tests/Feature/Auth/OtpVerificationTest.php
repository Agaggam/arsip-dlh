<?php

namespace Tests\Feature\Auth;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    private Department $defaultDept;

    protected function setUp(): void
    {
        parent::setUp();

        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
            }
        }

        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);

        $this->defaultDept = Department::create([
            'name' => 'Sekretariat',
            'slug' => 'sekretariat',
        ]);
    }

    public function test_otp_verification_screen_can_be_rendered(): void
    {
        $user = User::create([
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'status'            => 'pending',
            'email_verified_at' => null,
            'department_id'     => $this->defaultDept->id,
        ]);

        $response = $this->actingAs($user)->get(route('verification.otp'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Email');
    }

    public function test_user_can_verify_correct_otp_and_is_redirected_to_login(): void
    {
        $user = User::create([
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'status'            => 'pending',
            'email_verified_at' => null,
            'department_id'     => $this->defaultDept->id,
        ]);

        $user->email_otp_code = '123456';
        $user->email_otp_expires_at = now()->addMinutes(10);
        $user->save();

        $response = $this->actingAs($user)->post(route('verification.otp.verify'), [
            'otp' => '123456',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
        $this->assertGuest();

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->email_verified_at);
        $this->assertSame('pending', $freshUser->status);
        $this->assertNull($freshUser->email_otp_code);
    }

    public function test_user_cannot_verify_with_wrong_otp(): void
    {
        $user = User::create([
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'status'            => 'pending',
            'email_verified_at' => null,
            'department_id'     => $this->defaultDept->id,
        ]);

        $user->email_otp_code = '123456';
        $user->email_otp_expires_at = now()->addMinutes(10);
        $user->save();

        $response = $this->actingAs($user)->post(route('verification.otp.verify'), [
            'otp' => '654321',
        ]);

        $response->assertSessionHasErrors('otp');
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_unverified_user_attempting_login_is_redirected_to_otp_screen(): void
    {
        $user = User::create([
            'name'              => 'Test User',
            'email'             => 'unverified@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'status'            => 'pending',
            'email_verified_at' => null,
            'department_id'     => $this->defaultDept->id,
        ]);

        $response = $this->post('/login', [
            'email'    => 'unverified@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('verification.otp'));
        $response->assertSessionHas('warning');
    }

    public function test_verified_user_can_login_and_access_dashboard(): void
    {
        $user = User::create([
            'name'              => 'Verified User',
            'email'             => 'verified@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'status'            => 'approved',
            'email_verified_at' => now(),
            'department_id'     => $this->defaultDept->id,
        ]);

        $response = $this->post('/login', [
            'email'    => 'verified@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }
}

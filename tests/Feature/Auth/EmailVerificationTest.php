<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private Department $defaultDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu proses verifikasi
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
     * Helper privat membuat user baru yang BELUM terverifikasi emailnya
     */
    private function createUnverifiedUser(): User
    {
        return User::create([
            'name'              => 'Unverified User',
            'email'             => 'unverified@example.com',
            'password'          => bcrypt('password'),
            'role_id'           => 3,
            'department_id'     => $this->defaultDept->id,
            'status'            => 'pending',
            'email_verified_at' => null,
        ]);
    }

    // ============================================================
    // TEST CASES
    // ============================================================

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = $this->createUnverifiedUser();

        Event::fake();

        // Membuat URL verifikasi bertanda tangan digital (signed URL)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // Memastikan Event Verified terpicu oleh sistem Laravel
        Event::assertDispatched(Verified::class);
        
        // Memastikan field email_verified_at di database kini sudah terisi waktu
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        
        // Memastikan diarahkan kembali ke dashboard dengan membawa parameter penanda sukses verifikasi
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = $this->createUnverifiedUser();

        // Membuat URL verifikasi palsu dengan hash email yang salah
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        // Memastikan user di database TETAP berstatus belum terverifikasi (null)
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
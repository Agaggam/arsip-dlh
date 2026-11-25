<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\OtpVerificationController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class OtpVerificationUnitTest extends TestCase
{
    protected OtpVerificationController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new OtpVerificationController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeUserSpy(array $props = []): object
    {
        return new class($props) {
            public bool $verified;
            public bool $pureSuperAdmin;
            public bool $admin;
            public ?string $email_otp_code;
            public mixed $email_otp_expires_at;
            public string $status;
            public bool $markedAsVerified = false;
            public bool $saved = false;

            public function __construct(array $p)
            {
                $this->verified             = $p['verified'] ?? false;
                $this->pureSuperAdmin       = $p['pureSuperAdmin'] ?? false;
                $this->admin                = $p['admin'] ?? false;
                $this->email_otp_code       = $p['email_otp_code'] ?? '123456';
                $this->email_otp_expires_at = $p['email_otp_expires_at'] ?? now()->addMinutes(5);
                $this->status               = $p['status'] ?? 'pending';
            }

            public function hasVerifiedEmail(): bool { return $this->verified; }
            public function isPureSuperAdmin(): bool { return $this->pureSuperAdmin; }
            public function isAdmin(): bool { return $this->admin; }
            public function markEmailAsVerified(): bool { $this->markedAsVerified = true; return true; }
            public function save(): bool { $this->saved = true; return true; }
        };
    }

    /** TC-OTP-01 (Negatif): Validasi gagal jika kode OTP bukan 6 digit. */
    public function test_tc_otp_01_invalid_otp_length_throws_validation_exception(): void
    {
        // Arrange
        $request = Request::create('/verify-otp', 'POST', ['otp' => '123']);

        // Assert & Act
        $this->expectException(ValidationException::class);
        $this->controller->verify($request);
    }

    /** TC-OTP-02 (Positif): User yang sudah terverifikasi langsung dialihkan ke dashboard. */
    public function test_tc_otp_02_already_verified_user_redirects_to_dashboard(): void
    {
        // Arrange — [STUB]: User sudah terverifikasi
        $userStub = $this->makeUserSpy(['verified' => true, 'pureSuperAdmin' => true]);
        Auth::shouldReceive('user')->andReturn($userStub);

        $request = Request::create('/verify-otp', 'POST', ['otp' => '123456']);

        // Act
        $response = $this->controller->verify($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('admin.dashboard'), $response->getTargetUrl());
    }

    /** TC-OTP-03 (Negatif): Kode OTP salah mengembalikan error validasi otp. */
    public function test_tc_otp_03_wrong_otp_returns_error(): void
    {
        // Arrange — [STUB]: OTP user di sistem berbeda dengan input
        $userStub = $this->makeUserSpy(['email_otp_code' => '654321']);
        Auth::shouldReceive('user')->once()->andReturn($userStub);

        $request = Request::create('/verify-otp', 'POST', ['otp' => '123456']);

        // Act
        $response = $this->controller->verify($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Kode OTP salah', $response->getSession()->get('errors')->first('otp'));
    }

    /** TC-OTP-04 (Negatif): Kode OTP kedaluwarsa ditolak dengan pesan expired. */
    public function test_tc_otp_04_expired_otp_returns_error(): void
    {
        // Arrange — [STUB]: Waktu kedaluwarsa sudah lewat 5 menit lalu
        $userStub = $this->makeUserSpy([
            'email_otp_code'       => '123456',
            'email_otp_expires_at' => now()->subMinutes(5),
        ]);
        Auth::shouldReceive('user')->once()->andReturn($userStub);

        $request = Request::create('/verify-otp', 'POST', ['otp' => '123456']);

        // Act
        $response = $this->controller->verify($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('kedaluwarsa', $response->getSession()->get('errors')->first('otp'));
    }

    /** TC-OTP-05 (Positif): OTP valid pada user baru (pending) menandai verified, logout, dan redirect login. */
    public function test_tc_otp_05_valid_otp_for_pending_user_verifies_and_logs_out(): void
    {
        // Arrange — [SPY]: User spy mencatat markEmailAsVerified() dan save()
        $userSpy = $this->makeUserSpy(['email_otp_code' => '123456', 'status' => 'pending']);
        Auth::shouldReceive('user')->once()->andReturn($userSpy);

        // [MOCK]: Memastikan Auth::logout() dan SessionStore dipanggil
        Auth::shouldReceive('logout')->once();
        $sessionMock = Mockery::mock(SessionStore::class);
        $sessionMock->shouldReceive('invalidate')->once();
        $sessionMock->shouldReceive('regenerateToken')->once();
        $sessionMock->shouldReceive('flash')
            ->with('success', 'Email berhasil diverifikasi! Akun Anda sedang menunggu persetujuan administrator.')
            ->once();

        $request = Request::create('/verify-otp', 'POST', ['otp' => '123456']);
        $request->setLaravelSession($sessionMock);
        $this->app->instance('session.store', $sessionMock);
        $this->app['redirect']->setSession($sessionMock);

        // Act
        $response = $this->controller->verify($request);

        // Assert
        $this->assertTrue($userSpy->markedAsVerified);
        $this->assertTrue($userSpy->saved);
        $this->assertNull($userSpy->email_otp_code);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
    }
}

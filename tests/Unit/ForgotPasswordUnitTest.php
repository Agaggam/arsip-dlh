<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class ForgotPasswordUnitTest extends TestCase
{
    protected NewPasswordController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new NewPasswordController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** TC-FPW-01 (Negatif): Validasi gagal jika format email tidak valid. */
    public function test_tc_fpw_01_invalid_email_throws_validation_exception(): void
    {
        // Arrange
        $request = Request::create('/reset-password', 'POST', [
            'token'                 => 'token-abc',
            'email'                 => 'bukan-email',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert & Act
        $this->expectException(ValidationException::class);
        $this->controller->store($request);
    }

    /** TC-FPW-02 (Negatif): Validasi gagal jika konfirmasi password tidak cocok. */
    public function test_tc_fpw_02_mismatched_password_confirmation_throws_validation_exception(): void
    {
        // Arrange
        $request = Request::create('/reset-password', 'POST', [
            'token'                 => 'token-abc',
            'email'                 => 'admin@dlh.go.id',
            'password'              => 'password123',
            'password_confirmation' => 'beda12345',
        ]);

        // Assert & Act
        $this->expectException(ValidationException::class);
        $this->controller->store($request);
    }

    /** TC-FPW-03 (Positif): Reset password berhasil mengarahkan user ke route login dengan flash status. */
    public function test_tc_fpw_03_valid_reset_redirects_to_login(): void
    {
        // Arrange — [MOCK + STUB]: Password::reset dipanggil 1x dan mengembalikan PASSWORD_RESET
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::PASSWORD_RESET);

        $request = Request::create('/reset-password', 'POST', [
            'token'                 => 'valid-token-123',
            'email'                 => 'admin@dlh.go.id',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** TC-FPW-04 (Negatif): Token reset tidak valid / kedaluwarsa mengembalikan error pada field email. */
    public function test_tc_fpw_04_invalid_token_returns_error(): void
    {
        // Arrange — [MOCK + STUB]: Password::reset mengembalikan INVALID_TOKEN
        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        $request = Request::create('/reset-password', 'POST', [
            'token'                 => 'expired-token',
            'email'                 => 'admin@dlh.go.id',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertNotEmpty($response->getSession()->get('errors')->first('email'));
    }
}

<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CheckStatusMiddlewareTest extends TestCase
{
    protected CheckStatus $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckStatus();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * TC-01 (Positif): Pengguna guest (Auth::check() === false)
     * Ekspektasi: Langsung meneruskan request ke $next($request) tanpa logout.
     */
    public function test_tc01_guest_user_passes_through(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub facade Auth::check() mengembalikan false (unauthenticated / guest)
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(false);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak pernah dipanggil
        Auth::shouldReceive('logout')
            ->never();

        $request = Request::create('/dashboard', 'GET');

        // [TEST DOUBLE - DUMMY]: Dummy Response untuk memenuhi nilai kembalian Closure $next
        $dummyResponse = new Response('passed_to_next', 200);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memverifikasi apakah $next dipanggil oleh middleware
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertTrue($nextCalled, 'Closure $next harus terpanggil ketika pengguna berstatus guest.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * TC-02 (Positif): Pengguna login dengan role 'user' dan status 'approved'
     * Ekspektasi: Berhasil lolos pengecekan dan lanjut ke $next($request).
     */
    public function test_tc02_approved_user_passes_through(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek User dan Role dengan status 'approved'
        $userStub = (object) [
            'status' => 'approved',
            'role'   => (object) ['name' => 'user'],
        ];

        // [TEST DOUBLE - STUB]: Stub facade Auth untuk autentikasi user
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak pernah dipanggil
        Auth::shouldReceive('logout')
            ->never();

        $request = Request::create('/dashboard', 'GET');

        // [TEST DOUBLE - DUMMY]: Dummy Response untuk nilai balik $next
        $dummyResponse = new Response('passed_to_next', 200);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memantau pemanggilan $next
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertTrue($nextCalled, 'Closure $next harus terpanggil ketika user berstatus approved.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * TC-03 (Positif): Pengguna login dengan role 'super_admin' dan status 'pending' (non-approved)
     * Ekspektasi: Pengecualian hak istimewa Super Admin/Admin, tetap lanjut ke $next($request).
     */
    public function test_tc03_super_admin_with_pending_status_passes_through(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek Super Admin dengan status 'pending'
        $superAdminStub = (object) [
            'status' => 'pending',
            'role'   => (object) ['name' => 'super_admin'],
        ];

        // [TEST DOUBLE - STUB]: Stub facade Auth
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($superAdminStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak pernah dipanggil untuk super_admin
        Auth::shouldReceive('logout')
            ->never();

        $request = Request::create('/dashboard', 'GET');

        // [TEST DOUBLE - DUMMY]: Dummy Response untuk nilai balik $next
        $dummyResponse = new Response('passed_to_next_super_admin', 200);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memantau pemanggilan $next
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertTrue($nextCalled, 'Super admin berstatus pending harus tetap lolos ke $next.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * TC-04 (Negatif): Pengguna login dengan role 'user' dan status 'pending'
     * Ekspektasi: Auth::logout(), session di-invalidate & regenerateToken, redirect ke route 'login' dengan flash message pending.
     */
    public function test_tc04_pending_user_is_logged_out_and_redirected(): void
    {
        // Arrange
        $expectedMessage = 'Akun Anda sedang dalam tinjauan admin. Mohon tunggu.';

        // [TEST DOUBLE - STUB]: Stub User biasa dengan status 'pending'
        $userStub = (object) [
            'status' => 'pending',
            'role'   => (object) ['name' => 'user'],
        ];

        // [TEST DOUBLE - STUB]: Stub facade Auth
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userStub);

        // [TEST DOUBLE - MOCK]: Mock Auth::logout() harus dipanggil tepat 1 kali
        Auth::shouldReceive('logout')
            ->once();

        $request = Request::create('/dashboard', 'GET');

        // [TEST DOUBLE - MOCK]: Mock Session untuk memverifikasi pemanggilan invalidate(), regenerateToken(), dan flash()
        $sessionMock = Mockery::mock(SessionStore::class);
        $sessionMock->shouldReceive('invalidate')
            ->once();
        $sessionMock->shouldReceive('regenerateToken')
            ->once();
        $sessionMock->shouldReceive('flash')
            ->with('status_error', $expectedMessage)
            ->once();

        $request->setLaravelSession($sessionMock);
        $this->app->instance('session.store', $sessionMock);
        $this->app['redirect']->setSession($sessionMock);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memastikan $next TIDAK dipanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertFalse($nextCalled, 'Closure $next TIDAK boleh terpanggil untuk user pending.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * TC-05 (Negatif): Pengguna login dengan role 'user' dan status 'rejected'
     * Ekspektasi: Auth::logout(), session di-invalidate & regenerateToken, redirect ke route 'login' dengan pesan akun ditolak.
     */
    public function test_tc05_rejected_user_is_logged_out_and_redirected(): void
    {
        // Arrange
        $expectedMessage = 'Akun Anda ditolak. Silakan hubungi admin.';

        // [TEST DOUBLE - STUB]: Stub User biasa dengan status 'rejected'
        $userStub = (object) [
            'status' => 'rejected',
            'role'   => (object) ['name' => 'user'],
        ];

        // [TEST DOUBLE - STUB]: Stub facade Auth
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userStub);

        // [TEST DOUBLE - MOCK]: Mock Auth::logout() harus dipanggil tepat 1 kali
        Auth::shouldReceive('logout')
            ->once();

        $request = Request::create('/dashboard', 'GET');

        // [TEST DOUBLE - MOCK]: Mock Session untuk memverifikasi pembersihan session dan pesan flash error
        $sessionMock = Mockery::mock(SessionStore::class);
        $sessionMock->shouldReceive('invalidate')
            ->once();
        $sessionMock->shouldReceive('regenerateToken')
            ->once();
        $sessionMock->shouldReceive('flash')
            ->with('status_error', $expectedMessage)
            ->once();

        $request->setLaravelSession($sessionMock);
        $this->app->instance('session.store', $sessionMock);
        $this->app['redirect']->setSession($sessionMock);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memastikan $next TIDAK dipanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertFalse($nextCalled, 'Closure $next TIDAK boleh terpanggil untuk user rejected.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * TC-06 (Edge Case Negatif): Pengguna login dengan status 'pending' dan $user->role = null
     * Ekspektasi: Tidak terjadi null pointer exception pada $user->role, user tetap di-logout dan di-redirect.
     */
    public function test_tc06_user_with_null_role_and_pending_status_is_logged_out_safely(): void
    {
        // Arrange
        $expectedMessage = 'Akun Anda sedang dalam tinjauan admin. Mohon tunggu.';

        // [TEST DOUBLE - STUB]: Stub User dengan status 'pending' dan role bernilai null
        $userWithNullRoleStub = (object) [
            'status' => 'pending',
            'role'   => null,
        ];

        // [TEST DOUBLE - STUB]: Stub facade Auth
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($userWithNullRoleStub);

        // [TEST DOUBLE - MOCK]: Mock Auth::logout() harus dipanggil tepat 1 kali
        Auth::shouldReceive('logout')
            ->once();

        $request = Request::create('/dashboard', 'GET');

        // [TEST DOUBLE - MOCK]: Mock Session untuk memverifikasi invalidate dan token regeneration
        $sessionMock = Mockery::mock(SessionStore::class);
        $sessionMock->shouldReceive('invalidate')
            ->once();
        $sessionMock->shouldReceive('regenerateToken')
            ->once();
        $sessionMock->shouldReceive('flash')
            ->with('status_error', $expectedMessage)
            ->once();

        $request->setLaravelSession($sessionMock);
        $this->app->instance('session.store', $sessionMock);
        $this->app['redirect']->setSession($sessionMock);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memastikan $next TIDAK dipanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertFalse($nextCalled, 'Closure $next TIDAK boleh dipanggil ketika user memiliki role null dan status pending.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * TC-07 (Positif): Pengguna login dengan role 'admin' dan status 'pending' (non-approved)
     * Ekspektasi: Sesuai kode aktual CheckStatus baris 23, role 'admin' mendapat pengecualian dan lanjut ke $next($request).
     */
    public function test_tc07_admin_with_pending_status_passes_through(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek Admin dengan status 'pending'
        $adminStub = (object) [
            'status' => 'pending',
            'role'   => (object) ['name' => 'admin'],
        ];

        // [TEST DOUBLE - STUB]: Stub facade Auth
        Auth::shouldReceive('check')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($adminStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak pernah dipanggil untuk admin
        Auth::shouldReceive('logout')
            ->never();

        $request = Request::create('/admin/dashboard', 'GET');

        // [TEST DOUBLE - DUMMY]: Dummy Response untuk nilai balik $next
        $dummyResponse = new Response('passed_to_next_admin', 200);

        // [TEST DOUBLE - SPY]: Spy Closure untuk memantau pemanggilan $next
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next);

        // Assert
        $this->assertTrue($nextCalled, 'Admin berstatus pending harus tetap lolos ke $next.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
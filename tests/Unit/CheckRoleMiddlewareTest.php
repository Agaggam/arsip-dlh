<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CheckRoleMiddlewareTest extends TestCase
{
    protected CheckRole $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckRole();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * TC-CR-01 (Negatif): Pengguna belum login ($request->user() === null).
     * Ekspektasi: Redirect 302 ke route 'login', $next tidak dipanggil, Auth::logout() tidak dipanggil.
     */
    public function test_tc_cr_01_guest_user_is_redirected_to_login(): void
    {
        // Arrange
        $request = Request::create('/admin/dashboard', 'GET');

        // [TEST DOUBLE - STUB]: Mengembalikan null sebagai user belum terautentikasi
        $request->setUserResolver(fn() => null);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak dipanggil untuk guest
        Auth::shouldReceive('logout')->never();

        // [TEST DOUBLE - SPY]: Memantau apakah Closure $next terpanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'super_admin,admin');

        // Assert
        $this->assertFalse($nextCalled, 'Closure $next tidak boleh dipanggil untuk pengguna guest.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /**
     * TC-CR-02 (Negatif): Pengguna sudah login tetapi tidak memiliki role ($user->role === null).
     * Ekspektasi: Auth::logout() dipanggil 1x, flash status_error dikirim, redirect 302 ke route 'login'.
     */
    public function test_tc_cr_02_user_without_role_is_logged_out_and_redirected(): void
    {
        // Arrange
        $expectedMessage = 'Akun Anda belum memiliki Role. Hubungi Admin.';

        // [TEST DOUBLE - STUB]: Stub objek User tanpa role (role = null)
        $userWithoutRoleStub = (object) [
            'role' => null,
        ];

        $request = Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(fn() => $userWithoutRoleStub);

        // [TEST DOUBLE - MOCK]: Memverifikasi Auth::logout() dipanggil tepat 1 kali
        Auth::shouldReceive('logout')->once();

        // [TEST DOUBLE - MOCK]: Memverifikasi pesan flash error dikirim ke session
        $sessionMock = Mockery::mock(SessionStore::class);
        $sessionMock->shouldReceive('flash')
            ->with('status_error', $expectedMessage)
            ->once();

        $this->app->instance('session.store', $sessionMock);
        $this->app['redirect']->setSession($sessionMock);

        // [TEST DOUBLE - SPY]: Memantau agar $next tidak dipanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'super_admin,admin');

        // Assert
        $this->assertFalse($nextCalled, 'Closure $next tidak boleh dipanggil jika user belum memiliki role.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /**
     * TC-CR-03 (Positif): Pengguna ber-role 'super_admin' mengakses rute dengan parameter dipisah koma.
     * Ekspektasi: Lolos otorisasi dan diteruskan ke $next($request) dengan status 200.
     */
    public function test_tc_cr_03_super_admin_passes_comma_separated_roles(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek User dengan role 'super_admin'
        $superAdminStub = (object) [
            'role' => (object) ['name' => 'super_admin'],
        ];

        $request = Request::create('/admin/archives', 'GET');
        $request->setUserResolver(fn() => $superAdminStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak dipanggil
        Auth::shouldReceive('logout')->never();

        // [TEST DOUBLE - DUMMY]: Objek Response pengisi untuk nilai balik $next
        $dummyResponse = new Response('admin_archives_ok', 200);

        // [TEST DOUBLE - SPY]: Memantau pemanggilan $next
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'super_admin,admin');

        // Assert
        $this->assertTrue($nextCalled, 'Super Admin harus diizinkan lanjut ke $next.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * TC-CR-04 (Positif): Pengguna ber-role 'admin' mengakses rute dengan argumen variadic berspasi & elemen kosong.
     * Ekspektasi: Parsing explode + trim mengabaikan elemen kosong dan meloloskan 'admin' ke $next($request).
     */
    public function test_tc_cr_04_admin_passes_variadic_roles_with_spaces_and_empty_items(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek User dengan role 'admin'
        $adminStub = (object) [
            'role' => (object) ['name' => 'admin'],
        ];

        $request = Request::create('/admin/categories', 'GET');
        $request->setUserResolver(fn() => $adminStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak dipanggil
        Auth::shouldReceive('logout')->never();

        // [TEST DOUBLE - DUMMY]: Dummy Response untuk nilai balik $next
        $dummyResponse = new Response('admin_categories_ok', 200);

        // [TEST DOUBLE - SPY]: Memantau pemanggilan $next
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'super_admin', ' , admin , ');

        // Assert
        $this->assertTrue($nextCalled, 'Admin harus lolos setelah parsing variadic role dan elemen kosong diabaikan.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * TC-CR-05 (Positif): Pengguna ber-role 'user' mengakses rute khusus 'user'.
     * Ekspektasi: Lolos otorisasi dan diteruskan ke $next($request).
     */
    public function test_tc_cr_05_regular_user_passes_user_role(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek User dengan role 'user'
        $userStub = (object) [
            'role' => (object) ['name' => 'user'],
        ];

        $request = Request::create('/arsip', 'GET');
        $request->setUserResolver(fn() => $userStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak dipanggil
        Auth::shouldReceive('logout')->never();

        // [TEST DOUBLE - DUMMY]: Dummy Response
        $dummyResponse = new Response('user_archives_ok', 200);

        // [TEST DOUBLE - SPY]: Memantau pemanggilan $next
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled, $dummyResponse) {
            $nextCalled = true;
            return $dummyResponse;
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'user');

        // Assert
        $this->assertTrue($nextCalled, 'User biasa harus lolos saat mengakses rute dengan role user.');
        $this->assertSame($dummyResponse, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * TC-CR-06 (Negatif): Pengguna ber-role 'user' mencoba mengakses area admin ('super_admin,admin').
     * Ekspektasi: Ditolak, redirect 302 ke route 'dashboard', $next tidak dipanggil.
     */
    public function test_tc_cr_06_regular_user_blocked_from_admin_route_and_redirected_to_dashboard(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek User biasa
        $userStub = (object) [
            'role' => (object) ['name' => 'user'],
        ];

        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn() => $userStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak dipanggil (hanya redirect)
        Auth::shouldReceive('logout')->never();

        // [TEST DOUBLE - SPY]: Memastikan $next tidak dipanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'super_admin,admin');

        // Assert
        $this->assertFalse($nextCalled, 'User biasa tidak boleh masuk ke rute admin.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('dashboard'), $response->getTargetUrl());
    }

    /**
     * TC-CR-07 (Negatif): Pengguna ber-role 'super_admin' mencoba mengakses rute khusus 'user'.
     * Ekspektasi: Ditolak, redirect 302 ke route 'admin.dashboard', $next tidak dipanggil.
     */
    public function test_tc_cr_07_super_admin_blocked_from_user_route_and_redirected_to_admin_dashboard(): void
    {
        // Arrange
        // [TEST DOUBLE - STUB]: Stub objek Super Admin
        $superAdminStub = (object) [
            'role' => (object) ['name' => 'super_admin'],
        ];

        $request = Request::create('/dashboard', 'GET');
        $request->setUserResolver(fn() => $superAdminStub);

        // [TEST DOUBLE - MOCK]: Memastikan Auth::logout() tidak dipanggil
        Auth::shouldReceive('logout')->never();

        // [TEST DOUBLE - SPY]: Memastikan $next tidak dipanggil
        $nextCalled = false;
        $next = function (Request $req) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('should_not_reach_here', 500);
        };

        // Act
        $response = $this->middleware->handle($request, $next, 'user');

        // Assert
        $this->assertFalse($nextCalled, 'Super Admin yang mengakses rute khusus user harus dialihkan ke admin.dashboard.');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('admin.dashboard'), $response->getTargetUrl());
    }
}

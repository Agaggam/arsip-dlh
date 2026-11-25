<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\UserController;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UserControllerUnitTest extends TestCase
{
    protected UserController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new UserController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeUserStub(int $id, string $roleName, int $deptId, bool $isSystem = false): User
    {
        $role = new Role();
        $role->name = $roleName;

        $dept = new Department();
        $dept->id = $deptId;
        $dept->name = $isSystem ? 'System' : 'Bidang ' . $deptId;
        $dept->slug = $isSystem ? 'system' : 'bidang-' . $deptId;
        $dept->is_system = $isSystem;

        $user = new User();
        $user->id = $id;
        $user->name = 'User ' . $id;
        $user->department_id = $deptId;
        $user->setRelation('role', $role);
        $user->setRelation('department', $dept);

        return $user;
    }

    /** TC-USR-U01 (Positif): SuperAdminAccess mengizinkan Super Admin murni. */
    public function test_tc_usr_u01_super_admin_access_allows_pure_super_admin(): void
    {
        // Arrange — [STUB]
        $superAdmin = $this->makeUserStub(1, 'super_admin', 1, true);
        Auth::shouldReceive('user')->once()->andReturn($superAdmin);
        $method = new ReflectionMethod(UserController::class, 'SuperAdminAccess');

        // Act
        $result = $method->invoke($this->controller);

        // Assert
        $this->assertSame($superAdmin, $result);
    }

    /** TC-USR-U02 (Negatif): SuperAdminAccess menolak Admin biasa dengan HTTP 403. */
    public function test_tc_usr_u02_super_admin_access_blocks_regular_admin(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub(2, 'admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $method = new ReflectionMethod(UserController::class, 'SuperAdminAccess');

        // Assert & Act
        $this->expectException(HttpException::class);
        $method->invoke($this->controller);
    }

    /** TC-USR-U03 (Negatif): updateStatus menolak jika admin mencoba mengubah status dirinya sendiri. */
    public function test_tc_usr_u03_update_status_blocks_self_modification(): void
    {
        // Arrange — [STUB + MOCK]
        $superAdmin = $this->makeUserStub(1, 'super_admin', 1, true);
        Auth::shouldReceive('user')->once()->andReturn($superAdmin);
        Auth::shouldReceive('id')->once()->andReturn(1);

        $request = Request::create('/admin/users/1/status', 'PATCH', ['status' => 'approved']);

        // Act
        $response = $this->controller->updateStatus($request, $superAdmin);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('Anda tidak dapat mengubah status Anda sendiri.', $response->getSession()->get('error'));
    }

    /** TC-USR-U04 (Negatif): Admin biasa ditolak saat mencoba mengubah status admin lain. */
    public function test_tc_usr_u04_regular_admin_cannot_change_other_admin_status(): void
    {
        // Arrange — [STUB + MOCK]
        $admin1 = $this->makeUserStub(2, 'admin', 5);
        $admin2 = $this->makeUserStub(3, 'admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin1);
        Auth::shouldReceive('id')->once()->andReturn(2);

        $request = Request::create('/admin/users/3/status', 'PATCH', ['status' => 'rejected']);

        // Act
        $response = $this->controller->updateStatus($request, $admin2);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('Anda tidak dapat mengubah status admin lain.', $response->getSession()->get('error'));
    }

    /** TC-USR-U05 (Negatif): Admin bidang ditolak mengubah status user dari departemen lain. */
    public function test_tc_usr_u05_admin_cannot_change_status_of_user_from_other_department(): void
    {
        // Arrange — [STUB + MOCK]
        $admin = $this->makeUserStub(2, 'admin', 5);
        $otherUser = $this->makeUserStub(10, 'user', 9);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        Auth::shouldReceive('id')->once()->andReturn(2);

        $request = Request::create('/admin/users/10/status', 'PATCH', ['status' => 'approved']);

        // Act
        $response = $this->controller->updateStatus($request, $otherUser);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('Anda tidak memiliki izin untuk mengubah status user dari departemen lain.', $response->getSession()->get('error'));
    }
}

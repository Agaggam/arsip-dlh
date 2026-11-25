<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\CategoryController;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CategoryControllerUnitTest extends TestCase
{
    protected CategoryController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new CategoryController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeUserStub(string $roleName, int $deptId, bool $isSystem = false): User
    {
        $role = new Role();
        $role->name = $roleName;

        $dept = new Department();
        $dept->id = $deptId;
        $dept->name = $isSystem ? 'System' : 'Bidang ' . $deptId;
        $dept->slug = $isSystem ? 'system' : 'bidang-' . $deptId;
        $dept->is_system = $isSystem;

        $user = new User();
        $user->id = 1;
        $user->department_id = $deptId;
        $user->setRelation('role', $role);
        $user->setRelation('department', $dept);

        return $user;
    }

    /** TC-CAT-U01 (Positif): SuperAdminAccess mengizinkan Super Admin murni. */
    public function test_tc_cat_u01_super_admin_access_passes_for_pure_super_admin(): void
    {
        // Arrange — [STUB]
        $superAdmin = $this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->once()->andReturn($superAdmin);
        $method = new ReflectionMethod(CategoryController::class, 'SuperAdminAccess');

        // Act
        $result = $method->invoke($this->controller);

        // Assert
        $this->assertSame($superAdmin, $result);
    }

    /** TC-CAT-U02 (Negatif): SuperAdminAccess menolak Admin bidang biasa (HTTP 403). */
    public function test_tc_cat_u02_super_admin_access_blocks_department_admin(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $method = new ReflectionMethod(CategoryController::class, 'SuperAdminAccess');

        // Assert & Act
        $this->expectException(HttpException::class);
        $method->invoke($this->controller);
    }

    /** TC-CAT-U03 (Negatif): AllAdminAccess menolak pengguna guest (HTTP 403). */
    public function test_tc_cat_u03_all_admin_access_blocks_guest(): void
    {
        // Arrange — [STUB]
        Auth::shouldReceive('user')->once()->andReturn(null);
        $method = new ReflectionMethod(CategoryController::class, 'AllAdminAccess');

        // Assert & Act
        $this->expectException(HttpException::class);
        $method->invoke($this->controller, 5);
    }

    /** TC-CAT-U04 (Positif): AllAdminAccess mengizinkan Admin untuk departemen miliknya sendiri. */
    public function test_tc_cat_u04_all_admin_access_allows_matching_department_admin(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $method = new ReflectionMethod(CategoryController::class, 'AllAdminAccess');

        // Act
        $result = $method->invoke($this->controller, 5);

        // Assert
        $this->assertSame($admin, $result);
    }

    /** TC-CAT-U05 (Negatif): AllAdminAccess menolak Admin yang mengakses kategori departemen lain (HTTP 403). */
    public function test_tc_cat_u05_all_admin_access_blocks_different_department_admin(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $method = new ReflectionMethod(CategoryController::class, 'AllAdminAccess');

        // Assert & Act
        $this->expectException(HttpException::class);
        $method->invoke($this->controller, 9);
    }
}

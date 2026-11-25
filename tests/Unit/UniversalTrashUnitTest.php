<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\UniversalTrashController;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class UniversalTrashUnitTest extends TestCase
{
    protected UniversalTrashController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new UniversalTrashController();
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
        $user->password = 'hashed-secret-password';
        $user->setRelation('role', $role);
        $user->setRelation('department', $dept);

        return $user;
    }

    private function makeArchiveStub(int $deptId): Archive
    {
        $category = new Category();
        $category->department_id = $deptId;

        $archive = new Archive();
        $archive->setRelation('category', $category);

        return $archive;
    }

    /** TC-TRSH-U01 (Positif): Super Admin selalu memiliki akses arsip di Trash Bin. */
    public function test_tc_trsh_u01_super_admin_has_trash_archive_access(): void
    {
        // Arrange — [STUB]
        $superAdmin = $this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->once()->andReturn($superAdmin);
        $archive = $this->makeArchiveStub(7);
        $method = new ReflectionMethod(UniversalTrashController::class, 'hasArchiveAccess');

        // Act
        $result = $method->invoke($this->controller, $archive);

        // Assert
        $this->assertTrue($result);
    }

    /** TC-TRSH-U02 (Positif): Admin departemen memiliki akses arsip Trash Bin di bidangnya sendiri. */
    public function test_tc_trsh_u02_admin_has_access_to_same_department_trash(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 4);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $archive = $this->makeArchiveStub(4);
        $method = new ReflectionMethod(UniversalTrashController::class, 'hasArchiveAccess');

        // Act
        $result = $method->invoke($this->controller, $archive);

        // Assert
        $this->assertTrue($result);
    }

    /** TC-TRSH-U03 (Negatif): Admin departemen ditolak mengakses arsip Trash Bin bidang lain. */
    public function test_tc_trsh_u03_admin_denied_access_to_other_department_trash(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 4);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $archive = $this->makeArchiveStub(9);
        $method = new ReflectionMethod(UniversalTrashController::class, 'hasArchiveAccess');

        // Act
        $result = $method->invoke($this->controller, $archive);

        // Assert
        $this->assertFalse($result);
    }

    /** TC-TRSH-U04 (Negatif): forceDelete menolak request jika password konfirmasi kosong. */
    public function test_tc_trsh_u04_force_delete_requires_password(): void
    {
        // Arrange
        $request = Request::create('/admin/trash/archive/1/force-delete', 'DELETE', ['password' => '']);

        // Assert & Act
        $this->expectException(ValidationException::class);
        $this->controller->forceDelete($request, 'archive', 1);
    }

    /** TC-TRSH-U05 (Negatif): forceDelete ditolak jika password konfirmasi salah. */
    public function test_tc_trsh_u05_force_delete_rejects_wrong_password(): void
    {
        // Arrange — [STUB + MOCK]
        $superAdmin = $this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->once()->andReturn($superAdmin);
        Hash::shouldReceive('check')->once()->with('salah123', $superAdmin->password)->andReturn(false);

        $request = Request::create('/admin/trash/archive/1/force-delete', 'DELETE', ['password' => 'salah123']);

        // Act
        $response = $this->controller->forceDelete($request, 'archive', 1);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('Password salah!', $response->getSession()->get('error'));
    }
}
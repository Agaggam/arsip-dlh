<?php

namespace Tests\Unit;

use App\Http\Controllers\ArchiveController;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class ArchiveControllerUnitTest extends TestCase
{
    protected ArchiveController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ArchiveController();
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

    private function makeArchiveStub(int $deptId): Archive
    {
        $category = new Category();
        $category->department_id = $deptId;

        $archive = new Archive();
        $archive->title = 'Laporan Uji DLH';
        $archive->setRelation('category', $category);

        return $archive;
    }

    /** TC-ARS-U01 (Positif): formatBytes mengubah satuan byte ke B, KB, dan MB dengan tepat. */
    public function test_tc_ars_u01_format_bytes_returns_expected_units(): void
    {
        // Arrange
        $method = new ReflectionMethod(ArchiveController::class, 'formatBytes');

        // Act & Assert
        $this->assertEquals('0 B', $method->invoke($this->controller, 0));
        $this->assertEquals('1 KB', $method->invoke($this->controller, 1024));
        $this->assertEquals('10 MB', $method->invoke($this->controller, 10 * 1024 * 1024));
    }

    /** TC-ARS-U02 (Positif): Super Admin murni memiliki akses ke arsip departemen mana pun. */
    public function test_tc_ars_u02_super_admin_has_archive_access(): void
    {
        // Arrange — [STUB]
        $superAdmin = $this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->once()->andReturn($superAdmin);
        $archive = $this->makeArchiveStub(99);
        $method = new ReflectionMethod(ArchiveController::class, 'hasArchiveAccess');

        // Act
        $result = $method->invoke($this->controller, $archive);

        // Assert
        $this->assertTrue($result);
    }

    /** TC-ARS-U03 (Positif): Admin bidang memiliki akses ke arsip di departemennya sendiri. */
    public function test_tc_ars_u03_department_admin_has_access_to_own_department_archive(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $archive = $this->makeArchiveStub(5);
        $method = new ReflectionMethod(ArchiveController::class, 'hasArchiveAccess');

        // Act
        $result = $method->invoke($this->controller, $archive);

        // Assert
        $this->assertTrue($result);
    }

    /** TC-ARS-U04 (Negatif): Admin bidang ditolak mengakses arsip milik departemen lain. */
    public function test_tc_ars_u04_department_admin_denied_access_to_other_department_archive(): void
    {
        // Arrange — [STUB]
        $admin = $this->makeUserStub('admin', 5);
        Auth::shouldReceive('user')->once()->andReturn($admin);
        $archive = $this->makeArchiveStub(8);
        $method = new ReflectionMethod(ArchiveController::class, 'hasArchiveAccess');

        // Act
        $result = $method->invoke($this->controller, $archive);

        // Assert
        $this->assertFalse($result);
    }

    /** TC-ARS-U05 (Negatif): User biasa tidak berhak menghapus arsip via destroy(). */
    public function test_tc_ars_u05_regular_user_cannot_destroy_archive(): void
    {
        // Arrange — [STUB]
        $regularUser = $this->makeUserStub('user', 5);
        Auth::shouldReceive('user')->once()->andReturn($regularUser);
        $archive = $this->makeArchiveStub(5);
        $request = Request::create('/admin/archives/1', 'DELETE');

        // Act
        $response = $this->controller->destroy($request, $archive);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('Anda tidak berhak menghapus arsip ini.', $response->getSession()->get('error'));
    }
}

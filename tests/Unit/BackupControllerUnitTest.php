<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\BackupController;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mockery;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class BackupControllerUnitTest extends TestCase
{
    protected BackupController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new BackupController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeUserStub(string $roleName, int $deptId, bool$isSystem = false): User
    {
        $role = new Role();
        $role->name =$roleName;

        $dept = new Department();
        $dept->id =$deptId;
        $dept->name = $isSystem ? 'System' : 'Bidang ' . $deptId;
        $dept->slug = $isSystem ? 'system' : 'bidang-' . $deptId;
        $dept->is_system =$isSystem;

        $user = new User();$user->id = 1;
        $user->email = 'superadmin@gmail.com';
        $user->department_id =$deptId;
        $user->password = 'hashed-password';
        $user->setRelation('role',$role);
        $user->setRelation('department',$dept);

        return $user;
    }

    /** TC-BCK-U01 (Negatif): checkSuperAdmin menolak Admin biasa dengan HTTP 403. */
    public function test_tc_bck_u01_check_super_admin_blocks_non_super_admin(): void
    {
        // Arrange — [STUB]
        $admin =$this->makeUserStub('admin', 2);
        Auth::shouldReceive('user')->once()->andReturn($admin);$method = new ReflectionMethod(BackupController::class, 'checkSuperAdmin');

        // Assert & Act
        $this->expectException(HttpException::class);
        $method->invoke($this->controller);
    }

    /** TC-BCK-U02 (Negatif): resolveBackupPath menolak file non-.sql. */
    public function test_tc_bck_u02_resolve_backup_path_rejects_non_sql_extension(): void
    {
        // Arrange
        $method = new ReflectionMethod(BackupController::class, 'resolveBackupPath');

        // Act
        $result = $method->invoke($this->controller, 'malware.php');

        // Assert
        $this->assertNull($result);
    }

    /** TC-BCK-U03 (Negatif): resolveBackupPath menolak upaya path traversal (../../.env). */
    public function test_tc_bck_u03_resolve_backup_path_blocks_path_traversal(): void
    {
        // Arrange
        $method = new ReflectionMethod(BackupController::class, 'resolveBackupPath');

        // Act
        $result = $method->invoke($this->controller, '../../.env');

        // Assert
        $this->assertNull($result);
    }

    /** TC-BCK-U04 (Negatif): restore menolak file selain ekstensi .sql melalui validasi. */
    public function test_tc_bck_u04_restore_rejects_non_sql_upload(): void
    {
        // Arrange — [STUB + FAKE FILE]
        $superAdmin =$this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->andReturn($superAdmin);

        $fakeFile = UploadedFile::fake()->create('catatan.txt', 10);
        $request = Request::create('/admin/backup/restore', 'POST', [ 'restore_password' => 'password', ], [], ['backup_file' =>$fakeFile]);

        // Assert & Act
        $this->expectException(ValidationException::class);
        $this->controller->restore($request);
    }

    /** TC-BCK-U05 (Negatif): restore dibatalkan jika password konfirmasi salah. */
    public function test_tc_bck_u05_restore_rejects_wrong_confirmation_password(): void
    {
        // Arrange — [STUB + MOCK + FAKE FILE]
        $superAdmin =$this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->andReturn($superAdmin);
        Hash::shouldReceive('check')->once()->with('salah123', $superAdmin->password)->andReturn(false);

        $fakeSql = UploadedFile::fake()->createWithContent('backup.sql', 'SELECT 1;');
        $request = Request::create('/admin/backup/restore', 'POST', [ 'restore_password' => 'salah123', ], [], ['backup_file' =>$fakeSql]);

        // Act
        $response = $this->controller->restore($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class, $response);$this->assertEquals('Password konfirmasi salah. Restore database dibatalkan.', $response->getSession()->get('error'));
    }

    /** TC-BCK-U06 (Negatif): restore memblokir file SQL berbahaya (DROP DATABASE) dan mencatat Log::warning. */
    public function test_tc_bck_u06_restore_blocks_dangerous_sql_command(): void
    {
        // Arrange — [STUB + MOCK + FAKE FILE]
        $superAdmin =$this->makeUserStub('super_admin', 1, true);
        Auth::shouldReceive('user')->andReturn($superAdmin);
        Hash::shouldReceive('check')->once()->with('password', $superAdmin->password)->andReturn(true);
        Log::shouldReceive('warning')->once();

        $dangerousSql = UploadedFile::fake()->createWithContent('danger.sql', 'DROP DATABASE arsip_dlh;');
        $request = Request::create('/admin/backup/restore', 'POST', [ 'restore_password' => 'password', ], [], ['backup_file' =>$dangerousSql]);

        // Act
        $response = $this->controller->restore($request);

        // Assert
        $this->assertInstanceOf(RedirectResponse::class,$response);
        $this->assertStringContainsString('tidak diizinkan',$response->getSession()->get('error'));
    }
}
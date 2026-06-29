<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Category;
use App\Models\Archive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;

class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;
    
    private User $superAdmin;
    private User $admin;
    private User $regularUser;
    private Department $systemDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu testing
        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // 1. Create roles dengan ID terikat
        $superAdminRole = Role::create(['id' => 1, 'name' => 'super_admin']);
        $adminRole      = Role::create(['id' => 2, 'name' => 'admin']);
        $userRole       = Role::create(['id' => 3, 'name' => 'user']);

        // 2. Create master departemen 1 (SYSTEM)
        $this->systemDept = Department::create([
            'name' => 'System',
            'slug' => 'system'
        ]);

        // 3. Create departemen 2 & 3 sesuai data dari gambar UI
        $sekretariatDept = Department::create([
            'name' => 'Sekretariat',
            'slug' => 'sekretariat'
        ]);

        $tataLingkunganDept = Department::create([
            'name' => 'Tata Lingkungan',
            'slug' => 'tata-lingkungan'
        ]);

        // 4. Create Super Admin (Wajib berdepartemen SYSTEM)
        $this->superAdmin = User::create([
            'name'              => 'Super Admin Real',
            'email'             => 'superadmin@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => $superAdminRole->id,
            'department_id'     => $this->systemDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(), 
        ]);
        $this->superAdmin->markEmailAsVerified();
        $this->superAdmin->load(['role', 'department']); 

        // 5. Create Admin (Berada di departemen Sekretariat)
        $this->admin = User::create([
            'name'              => 'Admin Sekretariat',
            'email'             => 'admin@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => $adminRole->id,
            'department_id'     => $sekretariatDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->admin->markEmailAsVerified();
        $this->admin->load(['role', 'department']);

        // 6. Create Regular User (Berada di departemen Tata Lingkungan)
        $this->regularUser = User::create([
            'name'              => 'Regular User Tata Lingkungan',
            'email'             => 'user@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => $userRole->id,
            'department_id'     => $tataLingkunganDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->regularUser->markEmailAsVerified();
        $this->regularUser->load(['role', 'department']);
    }

    // ============================================================
    // TEST INDEX
    // ============================================================
    
    #[Test]
    public function super_admin_can_view_departments_index()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.departments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.departments.index');
        $response->assertViewHas('departments');
    }

    #[Test]
    public function non_super_admin_cannot_view_departments_index()
    {
        $nonSuperAdmins = [
            'Admin'        => $this->admin,
            'Regular User' => $this->regularUser,
        ];
        
        foreach ($nonSuperAdmins as $role => $user) {
            $response = $this->actingAs($user)
                ->get(route('admin.departments.index'));
            
            $response->assertStatus(403);
        }
    }

    // ============================================================
    // TEST STORE
    // ============================================================
    
    #[Test]
    public function super_admin_can_store_new_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.departments.store'), [
                'name'        => 'Pengelolaan Sampah',
                'description' => 'Deskripsi bidang pengelolaan sampah'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Departemen baru berhasil ditambahkan.');

        $this->assertDatabaseHas('departments', [
            'name' => 'Pengelolaan Sampah',
            'slug' => 'pengelolaan-sampah'
        ]);
    }

    #[Test]
    public function non_super_admin_cannot_store_department()
    {
        $nonSuperAdmins = [
            'Admin'        => $this->admin,
            'Regular User' => $this->regularUser,
        ];
        
        foreach ($nonSuperAdmins as $role => $user) {
            $response = $this->actingAs($user)
                ->post(route('admin.departments.store'), [
                    'name'        => 'Pengelolaan Sampah',
                    'description' => 'Deskripsi'
                ]);
            
            $response->assertStatus(403);
        }
    }

    #[Test]
    public function super_admin_cannot_store_department_with_duplicate_name()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.departments.store'), [
                'name'        => 'System',
                'description' => 'Deskripsi'
            ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function super_admin_cannot_store_department_without_name()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.departments.store'), [
                'description' => 'Deskripsi'
            ]);

        $response->assertSessionHasErrors('name');
    }

    // ============================================================
    // TEST UPDATE
    // ============================================================
    
    #[Test]
    public function super_admin_can_update_department()
    {
        $dept = Department::create([
            'name' => 'Bidang Lawas',
            'slug' => 'bidang-lawas'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.departments.update', $dept->id), [
                'name'        => 'Bidang Baru Updated',
                'description' => 'Deskripsi baru'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Data departemen berhasil diperbarui.');

        $dept->refresh();
        $this->assertEquals('Bidang Baru Updated', $dept->name);
        $this->assertEquals('bidang-baru-updated', $dept->slug);
    }

    #[Test]
    public function super_admin_cannot_update_system_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.departments.update', $this->systemDept->id), [
                'name'        => 'System Updated',
                'description' => 'Deskripsi baru'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen System tidak boleh diubah.');
        
        $this->systemDept->refresh();
        $this->assertEquals('System', $this->systemDept->name);
    }

    #[Test]
    public function non_super_admin_cannot_update_department()
    {
        $dept = Department::create([
            'name' => 'Sekretariat Edit',
            'slug' => 'sekretariat-edit'
        ]);

        $nonSuperAdmins = [
            'Admin'        => $this->admin,
            'Regular User' => $this->regularUser,
        ];
        
        foreach ($nonSuperAdmins as $role => $user) {
            $response = $this->actingAs($user)
                ->patch(route('admin.departments.update', $dept->id), [
                    'name'        => 'Ilegal Ubah',
                    'description' => 'Deskripsi baru'
                ]);
            
            $response->assertStatus(403);
        }
        
        $dept->refresh();
        $this->assertEquals('Sekretariat Edit', $dept->name);
    }

    // ============================================================
    // TEST DESTROY - TANPA RELASI (BERHASIL)
    // ============================================================
    
    #[Test]
    public function super_admin_can_delete_department_with_correct_password()
    {
        $dept = Department::create([
            'name' => 'Dept To Delete',
            'slug' => 'dept-to-delete'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Departemen ' . $dept->name . ' berhasil dihapus.');

        $this->assertDatabaseMissing('departments', [
            'id' => $dept->id
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_system_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $this->systemDept->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen System tidak bisa dihapus.');
        
        $this->assertDatabaseHas('departments', [
            'id'   => $this->systemDept->id,
            'name' => 'System'
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_department_with_wrong_password()
    {
        $dept = Department::create([
            'name' => 'Dept To Delete',
            'slug' => 'dept-to-delete'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'wrongpassword'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Konfirmasi gagal. Password salah.');

        $this->assertDatabaseHas('departments', [
            'id' => $dept->id
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_department_without_password()
    {
        $dept = Department::create([
            'name' => 'Dept To Delete',
            'slug' => 'dept-to-delete'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), []);

        $response->assertSessionHasErrors('password');
        
        $this->assertDatabaseHas('departments', [
            'id' => $dept->id
        ]);
    }

    #[Test]
    public function non_super_admin_cannot_delete_department()
    {
        $dept = Department::create([
            'name' => 'Dept To Delete',
            'slug' => 'dept-to-delete'
        ]);

        $nonSuperAdmins = [
            'Admin'        => $this->admin,
            'Regular User' => $this->regularUser,
        ];
        
        foreach ($nonSuperAdmins as $role => $user) {
            $response = $this->actingAs($user)
                ->delete(route('admin.departments.destroy', $dept->id), [
                    'password' => 'password'
                ]);
            
            $response->assertStatus(403);
        }
        
        $this->assertDatabaseHas('departments', [
            'id' => $dept->id
        ]);
    }

    // ============================================================
    // TEST DESTROY - DEPARTMENT DENGAN RELASI (HARUS GAGAL)
    // ============================================================
    
    #[Test]
    public function super_admin_cannot_delete_department_that_has_users()
    {
        $dept = Department::create([
            'name' => 'Dept With Users',
            'slug' => 'dept-with-users'
        ]);
        
        $userRelation = User::create([
            'name'              => 'Test User',
            'email'             => 'testuser@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $dept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $userRelation->markEmailAsVerified();
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id'   => $dept->id,
            'name' => 'Dept With Users'
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_department_that_has_categories()
    {
        $dept = Department::create([
            'name' => 'Dept With Categories',
            'slug' => 'dept-with-categories'
        ]);
        
        $dept->categories()->create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id'   => $dept->id,
            'name' => 'Dept With Categories'
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_department_that_has_archives()
    {
        $dept = Department::create([
            'name' => 'Dept With Archives',
            'slug' => 'dept-with-archives'
        ]);
        
        $category = $dept->categories()->create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
        
        $category->archives()->create([
            'title'       => 'Test Archive',
            'description' => 'Test Description',
            'file_path'   => '/test/file.pdf',
            'user_id'     => $this->superAdmin->id,
            'hash_token'  => Str::random(40)
        ]);
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id'   => $dept->id,
            'name' => 'Dept With Archives'
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_department_that_has_multiple_relations()
    {
        $dept = Department::create([
            'name' => 'Dept With Multiple Relations',
            'slug' => 'dept-with-multiple'
        ]);
        
        $userRelation = User::create([
            'name'              => 'Test User',
            'email'             => 'testuser2@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $dept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $userRelation->markEmailAsVerified();
        
        $category = $dept->categories()->create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
        
        $category->archives()->create([
            'title'       => 'Test Archive',
            'description' => 'Test Description',
            'file_path'   => '/test/file.pdf',
            'user_id'     => $this->superAdmin->id,
            'hash_token'  => Str::random(40)
        ]);
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id'   => $dept->id,
            'name' => 'Dept With Multiple Relations'
        ]);
    }

    // ============================================================
    // TEST VALIDASI TAMBAHAN
    // ============================================================
    
    #[Test]
    public function super_admin_cannot_update_department_with_duplicate_name()
    {
        $existingDept = Department::create([
            'name' => 'Existing Dept',
            'slug' => 'existing-dept'
        ]);
        
        $dept = Department::create([
            'name' => 'Dept Test',
            'slug' => 'dept-test'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.departments.update', $dept->id), [
                'name'        => 'Existing Dept',
                'description' => 'Deskripsi'
            ]);

        $response->assertSessionHasErrors('name');
    }
}
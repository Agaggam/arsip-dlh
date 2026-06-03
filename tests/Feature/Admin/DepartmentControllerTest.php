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

        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Create department System
        $this->systemDept = Department::create([
            'name' => 'System',
            'slug' => 'system'
        ]);

        // Create Super Admin
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'department_id' => $this->systemDept->id,
            'status' => 'approved'
        ]);

        // Create Admin
        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'department_id' => $this->systemDept->id,
            'status' => 'approved'
        ]);

        // Create Regular User
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'department_id' => $this->systemDept->id,
            'status' => 'approved'
        ]);
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
            'Admin' => $this->admin,
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
                'name' => 'Departemen Baru',
                'description' => 'Deskripsi departemen baru'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Departemen baru berhasil ditambahkan.');

        $this->assertDatabaseHas('departments', [
            'name' => 'Departemen Baru',
            'slug' => 'departemen-baru'
        ]);
    }

    #[Test]
    public function non_super_admin_cannot_store_department()
    {
        $nonSuperAdmins = [
            'Admin' => $this->admin,
            'Regular User' => $this->regularUser,
        ];
        
        foreach ($nonSuperAdmins as $role => $user) {
            $response = $this->actingAs($user)
                ->post(route('admin.departments.store'), [
                    'name' => 'Departemen Baru',
                    'description' => 'Deskripsi departemen baru'
                ]);
            
            $response->assertStatus(403);
        }
    }

    #[Test]
    public function super_admin_cannot_store_department_with_duplicate_name()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.departments.store'), [
                'name' => 'System',
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
            'name' => 'Dept Test',
            'slug' => 'dept-test'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.departments.update', $dept->id), [
                'name' => 'Dept Test Updated',
                'description' => 'Deskripsi baru'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Data departemen berhasil diperbarui.');

        $dept->refresh();
        $this->assertEquals('Dept Test Updated', $dept->name);
        $this->assertEquals('dept-test-updated', $dept->slug);
    }

    #[Test]
    public function super_admin_cannot_update_system_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.departments.update', $this->systemDept->id), [
                'name' => 'System Updated',
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
            'name' => 'Dept Test',
            'slug' => 'dept-test'
        ]);

        $nonSuperAdmins = [
            'Admin' => $this->admin,
            'Regular User' => $this->regularUser,
        ];
        
        foreach ($nonSuperAdmins as $role => $user) {
            $response = $this->actingAs($user)
                ->patch(route('admin.departments.update', $dept->id), [
                    'name' => 'Dept Test Updated',
                    'description' => 'Deskripsi baru'
                ]);
            
            $response->assertStatus(403);
        }
        
        $dept->refresh();
        $this->assertEquals('Dept Test', $dept->name);
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
            'id' => $this->systemDept->id,
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
            'Admin' => $this->admin,
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
        
        User::create([
            'name' => 'Test User',
            'email' => 'testuser@test.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'department_id' => $dept->id,
            'status' => 'approved'
        ]);
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
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
            'id' => $dept->id,
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
            'title' => 'Test Archive',
            'description' => 'Test Description',
            'file_path' => '/test/file.pdf',
            'user_id' => $this->superAdmin->id
        ]);
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
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
        
        User::create([
            'name' => 'Test User',
            'email' => 'testuser2@test.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'department_id' => $dept->id,
            'status' => 'approved'
        ]);
        
        $category = $dept->categories()->create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
        
        $category->archives()->create([
            'title' => 'Test Archive',
            'description' => 'Test Description',
            'file_path' => '/test/file.pdf',
            'user_id' => $this->superAdmin->id
        ]);
        
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.departments.destroy', $dept->id), [
                'password' => 'password'
            ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        
        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
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
                'name' => 'Existing Dept',
                'description' => 'Deskripsi'
            ]);

        $response->assertSessionHasErrors('name');
    }
}
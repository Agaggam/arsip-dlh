<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    
    private User $superAdmin;
    private User $admin;
    private User $regularUser;
    private Department $systemDept;
    private Department $sekretariatDept;
    private Department $tataLingkunganDept;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock fungsi log_activity jika berupa global helper agar tidak mengganggu testing
        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // 1. Create roles terikat ID secara eksplisit
        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);

        // 2. Create departments
        $this->systemDept = Department::create(['name' => 'System', 'slug' => 'system']);
        $this->sekretariatDept = Department::create(['name' => 'Sekretariat', 'slug' => 'sekretariat']);
        $this->tataLingkunganDept = Department::create(['name' => 'Tata Lingkungan', 'slug' => 'tata-lingkungan']);

        // 3. Create Users dengan kelengkapan status untuk bypass middleware route
        $this->superAdmin = User::create([
            'name'              => 'Super Admin',
            'email'             => 'superadmin@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 1,
            'department_id'     => $this->systemDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->superAdmin->markEmailAsVerified();
        $this->superAdmin->load(['role', 'department']);

        $this->admin = User::create([
            'name'              => 'Admin Sekretariat',
            'email'             => 'admin@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 2,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->admin->markEmailAsVerified();
        $this->admin->load(['role', 'department']);

        $this->regularUser = User::create([
            'name'              => 'Regular User',
            'email'             => 'user@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->regularUser->markEmailAsVerified();
        $this->regularUser->load(['role', 'department']);
    }

    // ============================================================
    // TEST INDEX (Menampilkan daftar user)
    // ============================================================
    
    #[Test]
    public function super_admin_can_view_users_index()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    #[Test]
    public function admin_can_view_users_index_only_their_department()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    #[Test]
    public function regular_user_cannot_access_users_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('users.index'));
        $response->assertStatus(403);
    }

    // ============================================================
    // TEST STORE (Menambah user baru)
    // ============================================================
    
    #[Test]
    public function super_admin_can_store_new_user()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('users.store'), [
                'name' => 'New User',
                'email' => 'newuser@test.com',
                'password' => 'password123',
                'role_id' => 3,
                'department_id' => $this->sekretariatDept->id
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User baru berhasil ditambahkan!');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@test.com'
        ]);
    }

    #[Test]
    public function admin_cannot_store_new_user()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'New User',
                'email' => 'newuser2@test.com',
                'password' => 'password123',
                'role_id' => 3,
                'department_id' => $this->sekretariatDept->id
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function super_admin_cannot_store_user_with_duplicate_email()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('users.store'), [
                'name' => 'Duplicate User',
                'email' => 'user@test.com',
                'password' => 'password123',
                'role_id' => 3,
                'department_id' => $this->sekretariatDept->id
            ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function super_admin_cannot_store_user_without_required_fields()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role_id']);
    }

    #[Test]
    public function super_admin_cannot_store_user_with_invalid_role_id()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('users.store'), [
                'name' => 'Invalid Role User',
                'email' => 'invalidrole@test.com',
                'password' => 'password123',
                'role_id' => 999,
                'department_id' => $this->sekretariatDept->id
            ]);

        $response->assertSessionHasErrors('role_id');
    }

    #[Test]
    public function super_admin_cannot_store_user_with_invalid_department_id()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('users.store'), [
                'name' => 'Invalid Dept User',
                'email' => 'invaliddept@test.com',
                'password' => 'password123',
                'role_id' => 3,
                'department_id' => 999
            ]);

        $response->assertSessionHasErrors('department_id');
    }

    // ============================================================
    // TEST UPDATE ROLE (Mengubah role user)
    // ============================================================
    
    #[Test]
    public function super_admin_can_update_user_role()
    {
        $newRoleId = 2;
        
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_role', $this->regularUser->id), [
                'role_id' => $newRoleId
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        $this->regularUser->refresh();
        $this->assertEquals($newRoleId, $this->regularUser->role_id);
    }

    #[Test]
    public function admin_cannot_update_user_role()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_role', $this->regularUser->id), [
                'role_id' => 2
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_update_own_role()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_role', $this->admin->id), [
                'role_id' => 3
            ]);

        $response->assertStatus(403);
        
        $this->admin->refresh();
        $this->assertEquals(2, $this->admin->role_id);
    }

    #[Test]
    public function super_admin_cannot_update_own_role()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_role', $this->superAdmin->id), [
                'role_id' => 2
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat mengubah role Anda sendiri.');
    }

    #[Test]
    public function super_admin_cannot_update_user_role_with_invalid_role_id()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_role', $this->regularUser->id), [
                'role_id' => 999
            ]);

        $response->assertSessionHasErrors('role_id');
    }

    // ============================================================
    // TEST UPDATE DEPARTMENT (Mengubah department user)
    // ============================================================
    
    #[Test]
    public function super_admin_can_update_user_department()
    {
        $newDept = Department::create(['name' => 'Dept Baru', 'slug' => 'dept-baru']);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_department', $this->regularUser->id), [
                'department_id' => $newDept->id
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->regularUser->refresh();
        $this->assertEquals($newDept->id, $this->regularUser->department_id);
    }

    #[Test]
    public function admin_cannot_update_user_department()
    {
        $newDept = Department::create(['name' => 'Dept Baru', 'slug' => 'dept-baru']);

        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_department', $this->regularUser->id), [
                'department_id' => $newDept->id
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_update_own_department()
    {
        $newDept = Department::create(['name' => 'Dept Baru', 'slug' => 'dept-baru']);

        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_department', $this->admin->id), [
                'department_id' => $newDept->id
            ]);

        $response->assertStatus(403);
        
        $this->admin->refresh();
        $this->assertEquals($this->sekretariatDept->id, $this->admin->department_id);
    }

    #[Test]
    public function super_admin_cannot_update_user_department_with_invalid_department_id()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_department', $this->regularUser->id), [
                'department_id' => 999
            ]);

        $response->assertSessionHasErrors('department_id');
    }

    // ============================================================
    // TEST UPDATE STATUS (Mengubah status user)
    // ============================================================
    
    #[Test]
    public function super_admin_can_update_user_status()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_status', $this->regularUser->id), [
                'status' => 'approved'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->regularUser->refresh();
        $this->assertEquals('approved', $this->regularUser->status);
    }

    #[Test]
    public function super_admin_can_update_user_status_to_rejected()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_status', $this->regularUser->id), [
                'status' => 'rejected'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->regularUser->refresh();
        $this->assertEquals('rejected', $this->regularUser->status);
    }

    #[Test]
    public function super_admin_cannot_update_user_status_with_invalid_status()
    {
        $response = $this->actingAs($this->superAdmin)
            ->patch(route('users.update_status', $this->regularUser->id), [
                'status' => 'invalid_status'
            ]);

        $response->assertSessionHasErrors('status');
    }

    #[Test]
    public function admin_can_update_user_status_only_in_their_department()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_status', $this->regularUser->id), [
                'status' => 'approved'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    #[Test]
    public function admin_cannot_update_user_status_in_other_department()
    {
        $otherUser = User::create([
            'name'              => 'Other User',
            'email'             => 'other@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->tataLingkunganDept->id,
            'status'            => 'pending',
            'email_verified_at' => now(),
        ]);
        $otherUser->markEmailAsVerified();
        $otherUser->load(['role', 'department']);

        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_status', $otherUser->id), [
                'status' => 'approved'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki izin untuk mengubah status user dari departemen lain.');
    }

    #[Test]
    public function admin_cannot_update_own_status()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_status', $this->admin->id), [
                'status' => 'rejected'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat mengubah status Anda sendiri.');
    }

    #[Test]
    public function admin_cannot_update_status_of_admin_user()
    {
        $anotherAdmin = User::create([
            'name'              => 'Another Admin',
            'email'             => 'another_admin2@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 2,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'pending',
            'email_verified_at' => now(),
        ]);
        $anotherAdmin->markEmailAsVerified();
        $anotherAdmin->load(['role', 'department']);

        $response = $this->actingAs($this->admin)
            ->patch(route('users.update_status', $anotherAdmin->id), [
                'status' => 'approved'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak dapat mengubah status admin lain.');
        
        $anotherAdmin->refresh();
        $this->assertEquals('pending', $anotherAdmin->status);
    }

    // ============================================================
    // TEST DESTROY (Menghapus user)
    // ============================================================
    
    #[Test]
    public function super_admin_can_delete_user_with_correct_password()
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('users.destroy', $this->regularUser->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User berhasil dihapus!');

        $this->assertDatabaseMissing('users', [
            'id' => $this->regularUser->id
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_user_with_wrong_password()
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('users.destroy', $this->regularUser->id), [
                'password' => 'wrongpassword'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Konfirmasi gagal. Password salah!');

        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_own_account()
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('users.destroy', $this->superAdmin->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak bisa menghapus akun sendiri!');

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdmin->id
        ]);
    }

    #[Test]
    public function super_admin_cannot_delete_nonexistent_user()
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('users.destroy', 99999), [
                'password' => 'password'
            ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function admin_can_only_delete_user_with_role_user_in_their_department()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->regularUser->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User berhasil dihapus!');

        $this->assertDatabaseMissing('users', [
            'id' => $this->regularUser->id
        ]);
    }

    #[Test]
    public function admin_cannot_delete_user_with_role_admin()
    {
        $anotherAdmin = User::create([
            'name'              => 'Another Admin',
            'email'             => 'another_admin@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 2,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $anotherAdmin->markEmailAsVerified();
        $anotherAdmin->load(['role', 'department']);

        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $anotherAdmin->id), [
                'password' => 'password'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Anda hanya dapat menghapus user dengan role "user".');
        
        $this->assertDatabaseHas('users', [
            'id' => $anotherAdmin->id,
            'email' => 'another_admin@test.com'
        ]);
    }

    #[Test]
    public function admin_cannot_delete_user_from_other_department()
    {
        $otherUser = User::create([
            'name'              => 'Other User',
            'email'             => 'other@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->tataLingkunganDept->id,
            'status'            => 'pending',
            'email_verified_at' => now(),
        ]);
        $otherUser->markEmailAsVerified();
        $otherUser->load(['role', 'department']);

        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $otherUser->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda hanya dapat menghapus user dari departemen Anda sendiri.');
    }

    #[Test]
    public function admin_cannot_delete_own_account()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->admin->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak bisa menghapus akun sendiri!');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id
        ]);
    }

    #[Test]
    public function admin_cannot_delete_user_without_password_confirmation()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->regularUser->id), []);

        $response->assertSessionHasErrors('password');
        
        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id
        ]);
    }

    #[Test]
    public function admin_cannot_delete_user_with_wrong_password()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->regularUser->id), [
                'password' => 'wrongpassword'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Konfirmasi gagal. Password salah!');

        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id
        ]);
    }

    #[Test]
    public function regular_user_cannot_delete_any_user()
    {
        $response = $this->actingAs($this->regularUser)
            ->delete(route('users.destroy', $this->admin->id), [
                'password' => 'password'
            ]);

        $response->assertStatus(403);
    }

    // ============================================================
    // TEST FILTER & SEARCH (Filter dan pencarian user)
    // ============================================================
    
    #[Test]
    public function super_admin_can_filter_users_by_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('users.index', ['department_id' => $this->sekretariatDept->id]));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    #[Test]
    public function super_admin_can_filter_users_by_status()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('users.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    #[Test]
    public function super_admin_can_filter_users_by_role()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('users.index', ['role_id' => 3]));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    #[Test]
    public function super_admin_can_search_users_by_name_or_email()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('users.index', ['search' => 'Regular']));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    #[Test]
    public function admin_filter_department_should_not_affect_results()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('users.index', ['department_id' => $this->tataLingkunganDept->id]));

        $response->assertStatus(200);
        
        $users = $response->viewData('users');
        foreach ($users as $user) {
            $this->assertEquals($this->sekretariatDept->id, $user->department_id);
        }
    }

    #[Test]
    public function admin_only_sees_users_from_their_department()
    {
        $otherUser = User::create([
            'name'              => 'Other Dept User',
            'email'             => 'otherdept@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->tataLingkunganDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $otherUser->markEmailAsVerified();
        $otherUser->load(['role', 'department']);

        $response = $this->actingAs($this->admin)
            ->get(route('users.index'));

        $response->assertStatus(200);
        
        $users = $response->viewData('users');
        $userEmails = $users->pluck('email')->toArray();
        
        $this->assertNotContains('otherdept@test.com', $userEmails);
        $this->assertContains('user@test.com', $userEmails);
    }
}
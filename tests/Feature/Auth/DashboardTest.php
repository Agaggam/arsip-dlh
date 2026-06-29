<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $adminDept;
    private User $approvedUser;
    private User $pendingUser;
    private Department $systemDept;
    private Department $sekretariatDept;

    protected function setUp(): void
    {
        parent::setUp();

        if (!function_exists('log_activity')) {
            function log_activity($user, $action, $description) {
                // Dimock agar bypass
            }
        }

        // 1. Inisialisasi data Master Role
        Role::create(['id' => 1, 'name' => 'super_admin']);
        Role::create(['id' => 2, 'name' => 'admin']);
        Role::create(['id' => 3, 'name' => 'user']);

        // 2. Inisialisasi data Master Department
        $this->systemDept = Department::create(['name' => 'System', 'slug' => 'system']);
        $this->sekretariatDept = Department::create(['name' => 'Sekretariat', 'slug' => 'sekretariat']);

        // 3. Buat Instance User: Super Admin
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

        // 4. Buat Instance User: Admin Departemen
        $this->adminDept = User::create([
            'name'              => 'Admin Sekretariat',
            'email'             => 'admin@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 2,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->adminDept->markEmailAsVerified();
        $this->adminDept->load(['role', 'department']);

        // 5. Buat Instance User: Regular User (Approved)
        $this->approvedUser = User::create([
            'name'              => 'User Approved',
            'email'             => 'approved@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'approved',
            'email_verified_at' => now(),
        ]);
        $this->approvedUser->markEmailAsVerified();
        $this->approvedUser->load(['role', 'department']);

        // 6. Buat Instance User: Regular User (Pending)
        $this->pendingUser = User::create([
            'name'              => 'User Pending',
            'email'             => 'pending@test.com',
            'password'          => Hash::make('password'),
            'role_id'           => 3,
            'department_id'     => $this->sekretariatDept->id,
            'status'            => 'pending',
            'email_verified_at' => now(),
        ]);
        $this->pendingUser->markEmailAsVerified();
        $this->pendingUser->load(['role', 'department']);
    }

    // ============================================================
    // 1. PENGUJIAN AKSES SUPER ADMIN & ADMIN BIDANG
    // ============================================================

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminDept)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_admin_and_super_admin_cannot_access_user_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    // ============================================================
    // 2. PENGUJIAN AKSES USER BIASA
    // ============================================================

    public function test_approved_regular_user_can_access_user_dashboard(): void
    {
        $response = $this->actingAs($this->approvedUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_approved_regular_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->approvedUser)
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_pending_regular_user_is_blocked_from_dashboard(): void
    {
        $response = $this->actingAs($this->pendingUser)
            ->get(route('dashboard'));

        $response->assertRedirect(route('login')); 
    }

    // ============================================================
    // 3. PENGUJIAN GUEST (BELUM LOGIN)
    // ============================================================

    public function test_guest_cannot_access_any_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
    //halo
}
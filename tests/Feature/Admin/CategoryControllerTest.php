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

class CategoryControllerTest extends TestCase
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

        // 1. Create roles terikat ID
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
    // TEST INDEX
    // ============================================================
    
    #[Test]
    public function super_admin_can_view_categories_index_with_all_departments()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertViewHas('departments');
    }

    #[Test]
    public function admin_can_view_categories_index_only_their_department()
    {
        $catSekretariat = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Surat Masuk',
            'slug'          => 'surat-masuk'
        ]);

        $catLingkungan = Category::create([
            'department_id' => $this->tataLingkunganDept->id,
            'name'          => 'Dokumen AMDAL',
            'slug'          => 'dokumen-amdal'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('categories.index'));

        $response->assertStatus(200);
        
        $categories = $response->viewData('categories');
        $this->assertTrue($categories->contains($catSekretariat));
        $this->assertFalse($categories->contains($catLingkungan));
    }

    #[Test]
    public function regular_user_cannot_access_categories_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('categories.index'));

        $response->assertStatus(403);
    }

    // ============================================================
    // TEST STORE
    // ============================================================
    
    #[Test]
    public function super_admin_can_store_new_category_to_their_system_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('categories.store'), [
                'name'        => 'Log System',
                'description' => 'Kategori log otomatis sistem'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil ditambahkan.');

        $this->assertDatabaseHas('categories', [
            'name'          => 'Log System',
            'department_id' => $this->systemDept->id,
            'slug'          => 'log-system'
        ]);
    }

    #[Test]
    public function super_admin_can_store_category_to_another_department()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('categories.store'), [
                'department_id' => $this->sekretariatDept->id,
                'name'          => 'Arsip Eksternal Sekr',
                'description'   => 'Kategori sekretariat dibuat oleh superadmin'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil ditambahkan.');

        $this->assertDatabaseHas('categories', [
            'name'          => 'Arsip Eksternal Sekr',
            'department_id' => $this->sekretariatDept->id,
            'slug'          => 'arsip-eksternal-sekr'
        ]);
    }

    #[Test]
    public function admin_can_store_new_category_to_their_department()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('categories.store'), [
                'name'        => 'Nota Dinas',
                'description' => 'Arsip nota dinas internal'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil ditambahkan.');

        $this->assertDatabaseHas('categories', [
            'name'          => 'Nota Dinas',
            'department_id' => $this->sekretariatDept->id,
            'slug'          => 'nota-dinas'
        ]);
    }

    #[Test]
    public function admin_cannot_store_category_with_duplicate_name_in_same_department()
    {
        Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Nota Dinas',
            'slug'          => 'nota-dinas'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('categories.store'), [
                'name'        => 'Nota Dinas',
                'description' => 'Mencoba duplikasi data'
            ]);

        $response->assertSessionHasErrors('name');
    }

    // ============================================================
    // TEST UPDATE
    // ============================================================
    
    #[Test]
    public function super_admin_can_update_category_belonging_to_any_department()
    {
        $category = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'SOP Lama',
            'slug'          => 'sop-lama'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('categories.update', $category->id), [
                'name'        => 'SOP Global Baru',
                'description' => 'Diubah paksa oleh Super Admin'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil diperbarui.');

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'SOP Global Baru',
            'slug' => 'sop-global-baru'
        ]);
    }

    #[Test]
    public function admin_can_update_category_in_their_department()
    {
        $category = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Draft Awal',
            'slug'          => 'draft-awal'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('categories.update', $category->id), [
                'name'        => 'Draft Final Revisi',
                'description' => 'Perubahan deskripsi berkas'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil diperbarui.');

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Draft Final Revisi',
            'slug' => 'draft-final-revisi'
        ]);
    }

    #[Test]
    public function admin_cannot_update_category_from_other_department()
    {
        $category = Category::create([
            'department_id' => $this->tataLingkunganDept->id,
            'name'          => 'Berkas AMDAL',
            'slug'          => 'berkas-amdal'
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('categories.update', $category->id), [
                'name' => 'Percobaan Hack'
            ]);

        $response->assertStatus(403);
    }

    // ============================================================
    // TEST MIGRATE ARCHIVES
    // ============================================================
    
    #[Test]
    public function super_admin_can_migrate_archives_across_different_departments()
    {
        $sourceCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Asal Sekr', 'slug' => 'asal-sekr']);
        $targetCat = Category::create(['department_id' => $this->tataLingkunganDept->id, 'name' => 'Tujuan Lingk', 'slug' => 'tujuan-lingk']);

        Archive::create([
            'category_id' => $sourceCat->id,
            'title'       => 'Berkas Nyasar',
            'description' => 'Dipindahkan silang departemen',
            'file_path'   => 'file.pdf',
            'user_id'     => $this->superAdmin->id,
            'hash_token'  => \Illuminate\Support\Str::random(40)
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('categories.migrate'), [
                'source_category_id' => $sourceCat->id,
                'target_category_id' => $targetCat->id,
                'password'           => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', "Berhasil memindahkan 1 arsip ke kategori '{$targetCat->name}'.");
        
        $this->assertDatabaseHas('archives', ['category_id' => $targetCat->id]);
    }

    #[Test]
    public function admin_can_migrate_archives_with_correct_password()
    {
        $sourceCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Asal', 'slug' => 'asal']);
        $targetCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Tujuan', 'slug' => 'tujuan']);

        Archive::create([
            'category_id' => $sourceCat->id,
            'title'       => 'Arsip Sekretariat 1',
            'description' => 'Keterangan berkas',
            'file_path'   => 'file.pdf',
            'user_id'     => $this->admin->id,
            'hash_token'  => \Illuminate\Support\Str::random(40)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('categories.migrate'), [
                'source_category_id' => $sourceCat->id,
                'target_category_id' => $targetCat->id,
                'password'           => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', "Berhasil memindahkan 1 arsip ke kategori '{$targetCat->name}'.");
    }

    #[Test]
    public function admin_cannot_migrate_archives_to_category_in_different_department()
    {
        $sourceCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Sekretariat Asal', 'slug' => 'sekretariat-asal']);
        $targetCat = Category::create(['department_id' => $this->tataLingkunganDept->id, 'name' => 'Lingkungan Tujuan', 'slug' => 'lingkungan-tujuan']);

        Archive::create([
            'category_id' => $sourceCat->id,
            'title'       => 'Arsip Rahasia',
            'description' => 'Keterangan berkas',
            'file_path'   => 'file.pdf',
            'user_id'     => $this->admin->id,
            'hash_token'  => \Illuminate\Support\Str::random(40)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('categories.migrate'), [
                'source_category_id' => $sourceCat->id,
                'target_category_id' => $targetCat->id,
                'password'           => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tidak dapat memindahkan arsip ke kategori dari departemen yang berbeda.');
    }

    #[Test]
    public function admin_cannot_migrate_archives_with_wrong_password()
    {
        $sourceCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Asal', 'slug' => 'asal']);
        $targetCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Tujuan', 'slug' => 'tujuan']);

        Archive::create([
            'category_id' => $sourceCat->id,
            'title'       => 'Arsip Penting',
            'file_path'   => 'file.pdf',
            'user_id'     => $this->admin->id,
            'hash_token'  => \Illuminate\Support\Str::random(40)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('categories.migrate'), [
                'source_category_id' => $sourceCat->id,
                'target_category_id' => $targetCat->id,
                'password'           => 'password-salah-wkwk'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
    }

    #[Test]
    public function admin_cannot_migrate_archives_to_the_same_category()
    {
        $sourceCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Sama', 'slug' => 'sama']);

        $response = $this->actingAs($this->admin)
            ->post(route('categories.migrate'), [
                'source_category_id' => $sourceCat->id,
                'target_category_id' => $sourceCat->id,
                'password'           => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Kategori sumber dan tujuan tidak boleh sama.');
    }

    #[Test]
    public function admin_cannot_migrate_archives_if_source_category_is_empty()
    {
        $sourceCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Kosong', 'slug' => 'kosong']);
        $targetCat = Category::create(['department_id' => $this->sekretariatDept->id, 'name' => 'Tujuan', 'slug' => 'tujuan']);

        $response = $this->actingAs($this->admin)
            ->post(route('categories.migrate'), [
                'source_category_id' => $sourceCat->id,
                'target_category_id' => $targetCat->id,
                'password'           => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Kategori ini tidak memiliki arsip untuk dipindahkan.');
    }

    // ============================================================
    // TEST DESTROY
    // ============================================================
    
    #[Test]
    public function super_admin_can_delete_any_department_category_when_empty()
    {
        $category = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Kategori Buangan',
            'slug'          => 'kategori-buangan'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('categories.destroy', $category->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil dihapus.');
        
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    #[Test]
    public function admin_can_delete_category_with_correct_password_when_empty()
    {
        $category = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Hapus Data',
            'slug'          => 'hapus-data'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kategori berhasil dihapus.');
    }

    #[Test]
    public function admin_cannot_delete_category_from_other_department()
    {
        $category = Category::create([
            'department_id' => $this->tataLingkunganDept->id,
            'name'          => 'Kategori Luar',
            'slug'          => 'kategori-luar'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category->id), [
                'password' => 'password'
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_delete_category_with_wrong_password()
    {
        $category = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Hapus Aku',
            'slug'          => 'hapus-aku'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category->id), [
                'password' => 'salah-lagi'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    #[Test]
    public function admin_cannot_delete_category_that_has_archives()
    {
        $category = Category::create([
            'department_id' => $this->sekretariatDept->id,
            'name'          => 'Kategori Berisi',
            'slug'          => 'kategori-berisi'
        ]);

        Archive::create([
            'category_id' => $category->id,
            'title'       => 'Dokumen Negara',
            'description' => 'Keterangan berkas',
            'file_path'   => 'file.pdf',
            'user_id'     => $this->admin->id,
            'hash_token'  => \Illuminate\Support\Str::random(40)
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('categories.destroy', $category->id), [
                'password' => 'password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', "Kategori tidak dapat dihapus karena masih memiliki 1 arsip. Pindahkan atau hapus arsip terlebih dahulu.");
    }
}
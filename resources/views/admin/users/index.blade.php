<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen User & Verifikasi') }}
    </x-slot>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Menunggu</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($pendingCount) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-100 text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs"><span class="text-amber-600 font-medium">Pending approval</span></div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aktif</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($approvedCount) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs"><span class="text-emerald-600 font-medium">Akun aktif (approved)</span></div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Nonaktif</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($rejectedCount) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-100 text-rose-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs"><span class="text-rose-600 font-medium">Akun ditolak / nonaktif</span></div>
            </div>
        </div>
        <div class="relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Terverifikasi</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-2">{{ number_format($verifiedCount) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs"><span class="text-blue-600 font-medium">Approved & verified email</span></div>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <x-table :isEmpty="$users->isEmpty()" emptyMessage="Tidak ada pengguna yang ditemukan.">

        <x-slot name="title">Daftar Pengguna Sistem</x-slot>

        <x-slot name="actions">
            {{-- Tombol Tambah User (hanya super admin) --}}
            @if(Auth::user()->isPureSuperAdmin())
                <button type="button"
                    onclick="document.getElementById('modal-tambah-user').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah User
                </button>
            @endif

            <x-search-input
                route="{{ route('users.index') }}"
                placeholder="Cari nama atau email..."
                searchParam="search"
                buttonText="Cari"
                :resetButton="true"
                :filters="[
                    'role_id' => [
                        'label' => 'Role',
                        'options' => $roles->pluck('name', 'id')->toArray()
                    ],
                    'department_id' => [
                        'label' => 'Bidang',
                        'options' => $departments->pluck('name', 'id')->toArray()
                    ],
                    'status' => [
                        'label' => 'Status Akun',
                        'options' => [
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected'
                        ]
                    ],
                    'verified' => [
                        'label' => 'Verifikasi Email',
                        'options' => [
                            'verified' => 'Verified',
                            'unverified' => 'Unverified'
                        ]
                    ]
                ]"
            />
        </x-slot>

        <x-slot name="thead">
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama & Email</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status Akun</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Verifikasi</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Approval</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
        </x-slot>

        <x-slot name="tbody">
            @php
                $currentUser = Auth::user();
                $isSuperAdmin = $currentUser->isPureSuperAdmin();
            @endphp

            @foreach($users as $user)
            @php
                $canApprove = $isSuperAdmin || ($currentUser->isAdmin() && $currentUser->department_id == $user->department_id);
                $canDelete = ($isSuperAdmin && $user->id !== $currentUser->id) ||
                             ($currentUser->isAdmin() && $currentUser->department_id == $user->department_id && $user->role->name === 'user');
                $canEditRole = $isSuperAdmin && $user->id !== $currentUser->id;
                $canEditDept = $isSuperAdmin && $user->id !== $currentUser->id;
                $canEdit     = $isSuperAdmin && $user->id !== $currentUser->id;
            @endphp
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold mr-3 text-sm">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    @if($canEditRole)
                        <form action="{{ route('users.update_role', $user->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <select name="role_id" onchange="this.form.submit()"
                                    class="text-xs font-bold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all bg-slate-50 cursor-pointer">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ strtoupper(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="text-xs font-bold text-slate-600 uppercase">
                            {{ strtoupper(str_replace('_', ' ', $user->role->name ?? '')) }}
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    @if($canEditDept)
                        <form action="{{ route('users.update_department', $user->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <select name="department_id" onchange="this.form.submit()"
                                    class="text-xs font-bold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all bg-slate-50 cursor-pointer">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="text-xs font-semibold text-slate-600 uppercase">
                            {{ $user->department->name ?? 'Tanpa Bidang' }}
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $statusClasses = [
                            'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200'
                        ];
                        $currentClass = $statusClasses[$user->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                    @endphp
                    <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-extrabold rounded-full border {{ $currentClass }} uppercase tracking-wider">
                        {{ $user->status }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    @if($user->hasVerifiedEmail())
                        <span class="inline-flex items-center text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Verified</span>
                    @else
                        <span class="inline-flex items-center text-slate-400 text-[10px] font-bold uppercase tracking-wider">Unverified</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    @if($canApprove && $user->id !== $currentUser->id)
                        <div class="flex justify-center gap-2">
                            @if($user->status !== 'approved')
                                <form action="{{ route('users.update_status', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded-lg transition active:scale-95 shadow-sm" title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($user->status !== 'rejected')
                                <form action="{{ route('users.update_status', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white p-1.5 rounded-lg transition active:scale-95 shadow-sm" title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <span class="text-[10px] text-slate-400 italic font-bold uppercase">-</span>
                    @endif
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center items-center gap-1.5">
                        {{-- Tombol Edit --}}
                        @if($canEdit)
                            <button type="button"
                                onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                                class="text-indigo-500 hover:text-indigo-700 p-1.5 hover:bg-indigo-50 rounded-xl transition-all"
                                title="Edit User">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Tombol Hapus --}}
                        @if($canDelete)
                            <button type="button" x-data
                                    x-on:click.prevent="$dispatch('open-modal', {
                                        id: 'confirm-user-delete',
                                        action: '{{ route('users.destroy', $user) }}',
                                        title: 'Hapus user {{ $user->name }} secara permanen?',
                                        warning: 'Seluruh data terkait user ini akan dihapus secara permanen!',
                                        withPassword: true
                                    })"
                                    class="text-rose-500 hover:text-rose-700 p-1.5 hover:bg-rose-50 rounded-xl transition-all"
                                    title="Hapus User">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        @endif

                        @if(!$canEdit && !$canDelete)
                            <span class="text-[10px] text-slate-400 italic font-bold uppercase">-</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </x-slot>
    </x-table>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif

    <x-confirm-modal id="confirm-user-delete" />


    {{-- =====================================================
         MODAL: TAMBAH USER BARU
    ====================================================== --}}
    @if(Auth::user()->isPureSuperAdmin())
    <div id="modal-tambah-user" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-xl">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Tambah User Baru</h3>
                </div>
                <button onclick="document.getElementById('modal-tambah-user').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('users.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Masukkan nama lengkap">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="contoh@email.com">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label>
                        <select name="role_id" required class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bidang</label>
                        <select name="department_id" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Tanpa Bidang --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Minimal 8 karakter">
                </div>
                <p class="text-xs text-slate-500 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                    ⚠️ Akun baru akan berstatus <strong>Pending</strong>. Approve manual setelah dibuat jika diperlukan.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition active:scale-95">
                        Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- =====================================================
         MODAL: EDIT USER
    ====================================================== --}}
    <div id="modal-edit-user" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg z-10 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-slate-100 rounded-xl">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Edit Data User</h3>
                </div>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="form-edit-user" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="edit-name" name="name" required
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Nama lengkap">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="edit-email" name="email" required
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Email">
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Password Baru
                        <span class="text-slate-400 font-normal">(kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" name="password" minlength="8"
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Password baru (opsional)">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                           placeholder="Ulangi password baru">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                            class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-bold text-white bg-slate-700 hover:bg-slate-800 rounded-xl shadow-sm transition active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function openEditModal(userId, userName, userEmail) {
            document.getElementById('edit-name').value  = userName;
            document.getElementById('edit-email').value = userEmail;
            document.getElementById('form-edit-user').action = '/admin/users/' + userId;
            document.getElementById('modal-edit-user').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modal-edit-user').classList.add('hidden');
            document.getElementById('form-edit-user').reset();
        }

        // Tutup modal jika tekan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('modal-tambah-user')?.classList.add('hidden');
                closeEditModal();
            }
        });
    </script>

</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User & Verifikasi') }}
        </h2>
    </x-slot>

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
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-amber-600 font-medium">Pending approval</span>
                </div>
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
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-emerald-600 font-medium">Akun aktif (approved)</span>
                </div>
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
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-rose-600 font-medium">Akun ditolak / nonaktif</span>
                </div>
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
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs">
                    <span class="text-blue-600 font-medium">Approved & verified email</span>
                </div>
            </div>
        </div>
    </div>


    <x-table :isEmpty="$users->isEmpty()" emptyMessage="Tidak ada pengguna yang ditemukan.">
        
        {{-- Slot Judul Tabel --}}
        <x-slot name="title">
            Daftar Pengguna Sistem
        </x-slot>

        {{-- Slot Fitur Pencarian / Filter --}}
        <x-slot name="actions">
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

        {{-- Slot Header Tabel (Thead) --}}
        <x-slot name="thead">
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama & Email</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Bidang</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status Akun</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Verifikasi</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Approval</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
        </x-slot>

        {{-- Slot Isi Tabel (Tbody) --}}
        <x-slot name="tbody">
            @php
                $currentUser = Auth::user();
                $isSuperAdmin = $currentUser->isPureSuperAdmin();
            @endphp
            
            @foreach($users as $user)
            @php
                // Hak akses approval
                $canApprove = false;
                if ($isSuperAdmin) {
                    $canApprove = true;
                } elseif ($currentUser->isAdmin() && $currentUser->department_id == $user->department_id) {
                    $canApprove = true;
                }
                
                // Hak akses hapus
                $canDelete = false;
                if ($isSuperAdmin && $user->id !== $currentUser->id) {
                    $canDelete = true;
                } elseif ($currentUser->isAdmin() && $currentUser->department_id == $user->department_id && $user->role->name === 'user') {
                    $canDelete = true;
                }
                
                // PERBAIKAN: Hak akses edit role (hanya super admin DAN tidak boleh mengedit dirinya sendiri)
                $canEditRole = $isSuperAdmin && $user->id !== $currentUser->id;
                
                // Hak akses edit departemen (hanya super admin & bukan dirinya sendiri)
                $canEditDept = $isSuperAdmin && $user->id !== $currentUser->id;
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
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            <select name="department_id" onchange="this.form.submit()" 
                                    class="text-xs font-bold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all bg-slate-50 cursor-pointer">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name  }}
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
                    @if($canDelete)
                        <button type="button" x-data
                                x-on:click.prevent="$dispatch('open-modal', { 
                                    id: 'confirm-user-delete',
                                    action: '{{ route('users.destroy', $user) }}',
                                    title: 'Hapus user {{ $user->name }} secara permanen?',
                                    warning: 'Seluruh data terkait user ini akan dihapus secara permanen!',
                                    withPassword: true 
                                })"
                                class="text-rose-500 hover:text-rose-700 p-1.5 sm:p-2 hover:bg-rose-50 rounded-xl transition-all">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    @endif
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
</x-app-layout>
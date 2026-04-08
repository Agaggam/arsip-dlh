<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User & Verifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-2xl shadow-sm border-l-4 border-green-500 transition-all">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[1.5rem] p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Pengguna Sistem</h3>
                    <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Total: {{ $users->count() }} User
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-50 rounded-xl overflow-hidden">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama & Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status Akun</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Verifikasi</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Approval</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($users as $user)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold mr-3 text-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <div class="text-sm font-bold text-slate-800">{{ $user->name }}</div>
                                                @if($user->hasVerifiedEmail())
                                                    <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20" title="Terverifikasi">
                                                        <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.304 1.25.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
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
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200'
                                        ];
                                        $currentClass = $statusClasses[$user->status] ?? 'bg-gray-50 text-gray-700';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-extrabold rounded-full border {{ $currentClass }} uppercase tracking-wider">
                                        {{ $user->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($user->hasVerifiedEmail())
                                        <span class="inline-flex items-center text-emerald-600 text-[10px] font-bold uppercase tracking-wider">
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                            Unverified
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($user->id !== auth()->id()) 
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
                                        <span class="text-[10px] text-slate-400 italic font-bold uppercase">Main Admin</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($user->id !== auth()->id())
                                        <button type="button" 
                                                data-modal-target="modal-hapus-user-{{ $user->id }}" 
                                                data-modal-toggle="modal-hapus-user-{{ $user->id }}" 
                                                class="text-rose-500 hover:text-rose-700 p-2 hover:bg-rose-50 rounded-xl transition-all active:scale-90"
                                                title="Hapus User">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <x-confirm-modal 
                                            id="modal-hapus-user-{{ $user->id }}" 
                                            title="Hapus user {{ $user->name }} secara permanen?" 
                                            type="danger" 
                                            :action="route('users.destroy', $user->id)" 
                                        />
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($users->isEmpty())
                    <div class="text-center py-20">
                        <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-slate-800 font-bold">Belum Ada Pengguna</h3>
                        <p class="text-slate-500 text-sm mt-1">Sistem belum mendeteksi adanya pendaftaran user baru.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
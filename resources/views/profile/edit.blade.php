<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Profil') }}
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 fade-in">
        <div class="max-w-full mx-auto space-y-8">
            
            {{-- Bagian Atas: Ringkasan Profil Singkat --}}
            <div class="relative overflow-hidden bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
                <div class="relative flex flex-col md:flex-row items-center gap-6">
                    <div class="w-24 h-24 bg-gradient-to-tr from-indigo-600 to-violet-500 rounded-3xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-indigo-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ Auth::user()->name }}</h3>
                        <p class="text-slate-500 font-medium">{{ Auth::user()->email }}</p>
                        <div class="mt-2 inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full uppercase tracking-wider">
                            Akun Terverifikasi
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Sidebar Informasi --}}
                <div class="lg:col-span-4 space-y-4">
                    <div class="p-6 bg-slate-800 rounded-[2rem] text-white shadow-xl shadow-slate-200">
                        <h4 class="font-bold text-lg mb-2 text-indigo-300">Keamanan Akun</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Pastikan informasi profil Anda selalu diperbarui untuk menjaga keamanan akses dan memudahkan administrasi dokumen.
                        </p>
                        <div class="mt-6 space-y-3 border-t border-slate-700 pt-6">
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                Password terenkripsi AES-256
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-300">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                Sesi login aktif: 1 perangkat
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Utama --}}
                <div class="lg:col-span-8 space-y-8">
                    
                    {{-- Form Informasi Profil --}}
                    <div class="bg-white border border-slate-200 shadow-sm rounded-[2rem] overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-4 bg-slate-50/50">
                            <div class="p-2 bg-white rounded-xl shadow-sm text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Informasi Pribadi</h3>
                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">General Information</p>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    {{-- Form Update Password --}}
                    <div class="bg-white border border-slate-200 shadow-sm rounded-[2rem] overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-4 bg-slate-50/50">
                            <div class="p-2 bg-white rounded-xl shadow-sm text-amber-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Kata Sandi</h3>
                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Security Settings</p>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    {{-- Form Hapus Akun (Danger Zone) --}}
                    <div class="bg-red-50/20 border border-red-100 shadow-sm rounded-[2rem] overflow-hidden">
                        <div class="px-8 py-6 border-b border-red-100 flex items-center gap-4 bg-red-50/50">
                            <div class="p-2 bg-white rounded-xl shadow-sm text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-red-800">Zona Bahaya</h3>
                                <p class="text-[11px] text-red-400 font-bold uppercase tracking-widest">Danger Zone</p>
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="max-w-xl text-left">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
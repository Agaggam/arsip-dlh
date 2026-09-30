<nav x-data="{ open: false }">
    <div class="hidden sm:flex flex-col w-64 bg-white border-r border-slate-200 fixed h-full z-40 shadow-sm transition-all duration-300">
        
        <div class="flex items-center px-8 h-20 border-b border-slate-50">
            @php
                $logoRoute = auth()->user()->isUser() ? route('dashboard') : route('admin.dashboard');
            @endphp
            <a href="{{ $logoRoute }}" class="flex items-center gap-3 group">
                <div class="p-1.5 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 transition-colors">
                    <x-application-logo class="block h-7 w-auto fill-current text-indigo-600 transition-transform group-hover:scale-110" />
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-tight text-slate-800 leading-tight block">
                        {{ config('app.name') }}
                    </span>
                    <span class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">
                        @if(auth()->user()->isPureSuperAdmin()) Super Admin
                        @elseif(auth()->user()->isAdmin()) Admin
                        @else User
                        @endif
                    </span>
                </div>
            </a>
        </div>

        <div class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-5 px-4 opacity-70">Menu Utama</p>
            
            @php
                $activeClass = 'bg-indigo-100 text-indigo-700 shadow-sm border-l-4 border-indigo-600';
                $inactiveClass = 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 border-l-4 border-transparent';
                $baseClass = 'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group font-medium text-sm';
            @endphp

            @php $isAdmin = auth()->user()->role->name === 'super_admin' | auth()->user()->role->name === 'admin'; @endphp
            
            @php $isDashboardActive = request()->routeIs('admin.dashboard') || request()->routeIs('dashboard'); @endphp
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}" 
               class="{{ $baseClass }} {{ $isDashboardActive ? $activeClass : $inactiveClass }}">
                <svg class="w-5 h-5 transition-colors {{ $isDashboardActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>{{ __('Dashboard') }}</span>
            </a>

            @if($isAdmin)
                {{-- GROUP: MASTER DATA --}}
                @php 
                    $isMasterDataActive = request()->routeIs('categories.*') || request()->routeIs('admin.departments.*') || request()->routeIs('kepegawaian.*');
                @endphp
                <div x-data="{ open: {{ $isMasterDataActive ? 'true' : 'false' }} }" class="space-y-1 mt-1">
                    <button @click="open = !open" 
                            class="w-full group flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200
                            {{ $isMasterDataActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ $isMasterDataActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            <span>{{ __('Master Data') }}</span>
                        </div>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300 {{ $isMasterDataActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="pl-11 pr-4 space-y-1 mt-1">
                        @php $isCategoriesActive = request()->routeIs('categories.*'); @endphp
                        <a href="{{ route('categories.index') }}" 
                           class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isCategoriesActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                            {{ __('Kategori Arsip') }}
                        </a>
                        
                        @if(auth()->user()->isPureSuperAdmin())
                            @php $isDepartmentsActive = request()->routeIs('admin.departments.*'); @endphp
                            <a href="{{ route('admin.departments.index') }}" 
                               class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isDepartmentsActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                                {{ __('Departemen') }}
                            </a>
                        @endif

                        @php $isKepegawaianActive = request()->routeIs('kepegawaian.*'); @endphp
                        <a href="{{ route('kepegawaian.index') }}" 
                           class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isKepegawaianActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                            {{ __('Data Kepegawaian') }}
                        </a>
                    </div>
                </div>

                {{-- DATA ARSIP (STANDALONE) --}}
                @php $isArchivesActive = request()->routeIs('admin.archives.index'); @endphp
                <a href="{{ route('admin.archives.index') }}"
                   class="{{ $baseClass }} {{ $isArchivesActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isArchivesActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                    <span>{{ __('Data Arsip') }}</span>
                </a>

                {{-- GROUP: USULAN HARGA SSH/SBU --}}
                @php $isSurveyActive = request()->routeIs('survey-harga.*'); @endphp
                <a href="{{ route('survey-harga.index') }}"
                   class="{{ $baseClass }} {{ $isSurveyActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isSurveyActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                    <span>{{ __('Usulan Harga') }}</span>
                    @if($isSurveyActive)
                    <span class="ml-auto text-[10px] font-bold bg-indigo-600 text-white px-2 py-0.5 rounded-full">SSH/SBU</span>
                    @else
                    <span class="ml-auto text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">SSH/SBU</span>
                    @endif
                </a>

                {{-- DATA PENGAWASAN --}}
                @php $isPengawasanActive = request()->routeIs('pengawasan.*'); @endphp
                <a href="{{ route('pengawasan.index') }}"
                   class="{{ $baseClass }} {{ $isPengawasanActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isPengawasanActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>{{ __('Pengawasan') }}</span>
                </a>

                {{-- PORTAL TAMAN KOTA LINK --}}
                @php $isPortalTamanActive = request()->is('portal-taman*'); @endphp
                <a href="/portal-taman/" target="_blank" rel="noopener noreferrer"
                   class="{{ $baseClass }} {{ $isPortalTamanActive ? $activeClass : $inactiveClass }} group">
                    <svg class="w-5 h-5 transition-colors {{ $isPortalTamanActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ __('Portal Taman Kota') }}</span>
                    <span class="ml-auto flex items-center gap-1">
                        <svg class="w-3 h-3 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>

                @if(auth()->user()->isPureSuperAdmin() || (auth()->user()->role && auth()->user()->role->name === 'super_admin'))
                {{-- ADMIN PORTAL TAMAN (SSO) --}}
                <a href="{{ route('portal-taman.sso') }}" target="_blank" rel="noopener noreferrer"
                   class="{{ $baseClass }} text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 border-l-4 border-transparent hover:border-emerald-500 group">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1"/>
                    </svg>
                    <span>{{ __('Admin Taman (SSO)') }}</span>
                    <span class="ml-auto flex items-center gap-1">
                        <span class="text-[9px] font-bold tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">SSO</span>
                    </span>
                </a>
                @endif

                {{-- BANK SAMPAH EXTERNAL LINK --}}
                <a href="https://mybanksampah.netlify.app/" target="_blank" rel="noopener noreferrer"
                   class="{{ $baseClass }} text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 border-l-4 border-transparent hover:border-emerald-500 group">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>{{ __('Bank Sampah') }}</span>
                    <span class="ml-auto flex items-center gap-1">
                        <svg class="w-3 h-3 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                </a>

                {{-- GROUP: PENGATURAN SISTEM --}}
                @php 
                    $isSuperAdmin = auth()->user()->isPureSuperAdmin();
                    $isAdmin = auth()->user()->isAdmin();
                    $isSettingsActive = (($isSuperAdmin || $isAdmin) && request()->routeIs('users.*')) || ($isSuperAdmin && (request()->routeIs('activity-logs.*') || request()->routeIs('admin.backup.*'))) || request()->routeIs('admin.trash.*');
                @endphp
                <div x-data="{ open: {{ $isSettingsActive ? 'true' : 'false' }} }" class="space-y-1 mt-1">
                    <button @click="open = !open" 
                            class="w-full group flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200
                            {{ $isSettingsActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ $isSettingsActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37-2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ __('Pengaturan') }}</span>
                        </div>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300 {{ $isSettingsActive ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="pl-11 pr-4 space-y-1 mt-1">
                        @php $isTrashActive = request()->routeIs('admin.trash.index'); @endphp
                        <a href="{{ route('admin.trash.index') }}" 
                           class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isTrashActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                            {{ __('Tong Sampah') }}
                        </a>

                        @if($isSuperAdmin || $isAdmin)
                        @php $isUsersActive = request()->routeIs('users.*'); @endphp
                        <a href="{{ route('users.index') }}" 
                           class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isUsersActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                            {{ __('Manajemen User') }}
                        </a>
                        @endif

                        @if($isSuperAdmin)
                        @php $isLogsActive = request()->routeIs('activity-logs.index'); @endphp
                        <a href="{{ route('activity-logs.index') }}" 
                           class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isLogsActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                            {{ __('Aktivitas Log') }}
                        </a>
                        
                        @php $isBackupActive = request()->routeIs('admin.backup.*'); @endphp
                        <a href="{{ route('admin.backup.index') }}" 
                           class="block px-3 py-2 text-[13px] font-semibold rounded-lg transition-all {{ $isBackupActive ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-indigo-600 hover:bg-slate-50' }}">
                            {{ __('Backup Database') }}
                        </a>
                        @endif
                    </div>
                </div>
                
@else
    @php 
        $isUserArchiveActive = request()->routeIs('arsip.user');
        $sidebarDepts = \App\Models\Department::where('name', '!=', 'System')->get();
    @endphp

    <div x-data="{ open: {{ $isUserArchiveActive ? 'true' : 'false' }} }" class="space-y-1">
        <button @click="open = !open" 
            class="w-full group flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200
            {{ $isUserArchiveActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
            
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 {{ $isUserArchiveActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5M5 19v-2a2 2 0 00-2-2m14 4h2a2 2 0 002-2v-5m-2 3h.01"></path>
                </svg>
                <span>{{ __('Daftar Arsip') }}</span>
            </div>

            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300 {{ $isUserArchiveActive ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-cloak 
             class="mx-2 mt-1 py-2 px-2 bg-slate-50/80 border border-slate-100 rounded-2xl space-y-1">
            
            <a href="{{ route('arsip.user') }}" 
               class="flex items-center px-4 py-2 text-[13px] font-bold uppercase tracking-wider rounded-xl transition-all
               {{ !request('dept') && $isUserArchiveActive ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-indigo-500 hover:bg-white/50' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-3 {{ !request('dept') ? 'bg-indigo-500' : 'bg-slate-300' }}"></span>
                Semua Bidang
            </a>

            @foreach($sidebarDepts as $dept)
                <a href="{{ route('arsip.user', ['dept' => $dept->id]) }}" 
                   class="flex items-center px-4 py-2 text-[13px] font-bold uppercase tracking-wider rounded-xl transition-all
                   {{ request('dept') == $dept->id ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-indigo-500 hover:bg-white/50' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-3 {{ request('dept') == $dept->id ? 'bg-indigo-500' : 'bg-slate-300' }}"></span>
                    {{ $dept->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- USULAN HARGA (USER) --}}
    @php $isSurveyActive = request()->routeIs('survey-harga.*'); @endphp
    <a href="{{ route('survey-harga.index') }}"
       class="{{ $baseClass }} {{ $isSurveyActive ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 transition-colors {{ $isSurveyActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
        </svg>
        <span>{{ __('Usulan Harga') }}</span>
        <span class="ml-auto text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">SSH/SBU</span>
    </a>

    {{-- DATA PENGAWASAN (USER) --}}
    @php $isPengawasanActive = request()->routeIs('pengawasan.*'); @endphp
    <a href="{{ route('pengawasan.index') }}"
       class="{{ $baseClass }} {{ $isPengawasanActive ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 transition-colors {{ $isPengawasanActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span>{{ __('Pengawasan') }}</span>
    </a>

    {{-- PORTAL TAMAN KOTA (USER) --}}
    @php $isPortalTamanActive = request()->is('portal-taman*'); @endphp
    <a href="/portal-taman/" target="_blank" rel="noopener noreferrer"
       class="{{ $baseClass }} {{ $isPortalTamanActive ? $activeClass : $inactiveClass }} group">
        <svg class="w-5 h-5 transition-colors {{ $isPortalTamanActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>{{ __('Portal Taman Kota') }}</span>
        <span class="ml-auto flex items-center gap-1">
            <svg class="w-3 h-3 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </span>
    </a>

    {{-- BANK SAMPAH (USER) --}}
    <a href="https://mybanksampah.netlify.app/" target="_blank" rel="noopener noreferrer"
       class="{{ $baseClass }} text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 border-l-4 border-transparent hover:border-emerald-500 group">
        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span>{{ __('Bank Sampah') }}</span>
        <span class="ml-auto flex items-center gap-1">
            <svg class="w-3 h-3 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </span>
    </a>
@endif
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="bg-white border border-slate-200/60 rounded-2xl p-3 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 px-1 mb-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-medium text-indigo-600 truncate uppercase tracking-wider">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-[11px] font-semibold text-slate-600 rounded-lg hover:bg-slate-50 hover:text-indigo-600 transition py-2 px-2">
                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        {{ __('Profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 text-[11px] font-semibold text-rose-500 rounded-lg hover:bg-rose-50 transition py-2 px-2">
                            <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="sm:hidden bg-white/80 backdrop-blur-md border-b border-slate-200 w-full fixed top-0 z-50">
        <div class="flex justify-between h-16 px-6">
            <div class="flex items-center gap-3">
                <x-application-logo class="block h-8 w-auto fill-current text-indigo-600" />
                <span class="text-sm font-bold tracking-tight text-slate-800 uppercase">{{ config('app.name') }}</span>
            </div>

            <div class="flex items-center">
                <button @click="open = ! open" class="p-2 rounded-xl text-slate-500 bg-slate-50 hover:bg-slate-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden bg-white border-t border-slate-100 shadow-2xl">
            <div class="p-4 space-y-1">
                @php $mobileClasses = 'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200'; @endphp
                
                @php $mobileIsAdmin = auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'super_admin'; @endphp
                @if($mobileIsAdmin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="{{ $mobileClasses }}">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    @if(auth()->user()->isPureSuperAdmin())
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="{{ $mobileClasses }}">
                        {{ __('Manajemen User') }}
                    </x-responsive-nav-link>
                    @endif
                    @if(auth()->user()->isPureSuperAdmin())
                    <x-responsive-nav-link :href="route('admin.departments.index')" :active="request()->routeIs('admin.departments.*')" class="{{ $mobileClasses }}">
                        {{ __('Departemen') }}
                    </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="{{ $mobileClasses }}">
                        {{ __('Kategori Arsip') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.archives.index')" :active="request()->routeIs('admin.archives.index')" class="{{ $mobileClasses }}">
                        {{ __('Data Arsip') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pengawasan.index')" :active="request()->routeIs('pengawasan.*')" class="{{ $mobileClasses }}">
                        {{ __('Pengawasan') }}
                    </x-responsive-nav-link>
                    <a href="/portal-taman/" target="_blank" rel="noopener noreferrer"
                       class="{{ $mobileClasses }} text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ __('Portal Taman Kota') }}
                        <svg class="w-3 h-3 ml-auto opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @if(auth()->user()->isPureSuperAdmin() || (auth()->user()->role && auth()->user()->role->name === 'super_admin'))
                    <a href="{{ route('portal-taman.sso') }}" target="_blank" rel="noopener noreferrer"
                       class="{{ $mobileClasses }} text-emerald-700 hover:bg-emerald-50 font-semibold">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('Admin Taman (SSO)') }}
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 ml-auto">SSO</span>
                    </a>
                    @endif
                    <a href="https://mybanksampah.netlify.app/" target="_blank" rel="noopener noreferrer"
                       class="{{ $mobileClasses }} text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('Bank Sampah') }}
                        <svg class="w-3 h-3 ml-auto opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <x-responsive-nav-link :href="route('admin.trash.index')" :active="request()->routeIs('admin.trash.index')" class="{{ $mobileClasses }}">
                        {{ __('Tong Sampah') }}
                    </x-responsive-nav-link>
                    @if(auth()->user()->isPureSuperAdmin() || auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="{{ $mobileClasses }}">
                        {{ __('Manajemen User') }}
                    </x-responsive-nav-link>
                    @endif
                    @if(auth()->user()->isPureSuperAdmin())
                    <x-responsive-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')" class="{{ $mobileClasses }}">
                        {{ __('Log Aktivitas') }}
                    </x-responsive-nav-link>
                    @endif
                @else
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="{{ $mobileClasses }}">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <div x-data="{ openMobileDept: {{ request('dept') ? 'true' : 'false' }} }" class="space-y-1">
                        
                        <button @click="openMobileDept = !openMobileDept" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all duration-200
                            {{ request()->routeIs('arsip.user') 
                                ? 'text-indigo-600 bg-indigo-50/50' 
                                : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg {{ request()->routeIs('arsip.user') ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5M5 19v-2a2 2 0 00-2-2m14 4h2a2 2 0 002-2v-5m-2 3h.01"></path>
                                    </svg>
                                </div>
                                <span>{{ __('Daftar Arsip') }}</span>
                            </div>

                            <svg :class="openMobileDept ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-300 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="openMobileDept" 
                            x-cloak 
                            x-collapse
                            class="relative ml-4 border-l-2 border-slate-100 mt-1 mb-4 space-y-1">
                            
                            <a href="{{ route('arsip.user') }}" 
                            class="block pl-8 pr-4 py-3 text-sm font-semibold transition-all
                            {{ request()->routeIs('arsip.user') && !request('dept') 
                                    ? 'text-indigo-600' 
                                    : 'text-slate-400 hover:text-slate-600' }}">
                                {{ __('Semua Bidang') }}
                            </a>

                            @php 
                                $sidebarDepts = \App\Models\Department::where('name', '!=', 'System')->get();
                            @endphp

                            @foreach($sidebarDepts as $dept)
                                <a href="{{ route('arsip.user', ['dept' => $dept->id]) }}" 
                                class="block pl-8 pr-4 py-3 text-sm font-semibold transition-all
                                {{ request('dept') == $dept->id 
                                        ? 'text-indigo-600' 
                                        : 'text-slate-400 hover:text-slate-600' }}">
                                    {{ $dept->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <x-responsive-nav-link :href="route('survey-harga.index')" :active="request()->routeIs('survey-harga.*')" class="{{ $mobileClasses }}">
                        {{ __('Usulan Harga') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pengawasan.index')" :active="request()->routeIs('pengawasan.*')" class="{{ $mobileClasses }}">
                        {{ __('Pengawasan') }}
                    </x-responsive-nav-link>
                    <a href="https://mybanksampah.netlify.app/" target="_blank" rel="noopener noreferrer"
                       class="{{ $mobileClasses }} text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('Bank Sampah') }}
                        <svg class="w-3 h-3 ml-auto opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl">
                <div class="flex items-center gap-3 px-2 mb-4">
                    <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold border border-slate-200 shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="{{ $mobileClasses }}">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
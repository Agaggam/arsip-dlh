<nav x-data="{ open: false }">
    <div class="hidden sm:flex flex-col w-64 bg-white border-r border-slate-200 fixed h-full z-40 shadow-sm transition-all duration-300">
        
        <div class="flex items-center px-8 h-20 border-b border-slate-50">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="p-1.5 bg-indigo-50 rounded-xl group-hover:bg-indigo-100 transition-colors">
                    <x-application-logo class="block h-7 w-auto fill-current text-indigo-600 transition-transform group-hover:scale-110" />
                </div>
                <span class="text-base font-extrabold tracking-tight text-slate-800 leading-tight">
                    {{ config('app.name') }}
                </span>
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
                @php $isUsersActive = request()->routeIs('users.*'); @endphp
                <a href="{{ route('users.index') }}" 
                   class="{{ $baseClass }} {{ $isUsersActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isUsersActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>{{ __('Manajemen User') }}</span>
                </a>

                @php $isDepartmentsActive = request()->routeIs('admin.departments.*'); @endphp
                <a href="{{ route('admin.departments.index') }}" 
                   class="{{ $baseClass }} {{ $isDepartmentsActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isDepartmentsActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <span>{{ __('Departemen') }}</span>
                </a>

                @php $isCategoriesActive = request()->routeIs('categories.*'); @endphp
                <a href="{{ route('categories.index') }}" 
                   class="{{ $baseClass }} {{ $isCategoriesActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isCategoriesActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <span>{{ __('Kategori Arsip') }}</span>
                </a>

                @php $isArchivesActive = request()->routeIs('admin.archives.index'); @endphp
                <a href="{{ route('admin.archives.index') }}" 
                   class="{{ $baseClass }} {{ $isArchivesActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isArchivesActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                    <span>{{ __('Manajemen Arsip') }}</span>
                </a>

                @php $isTrashActive = request()->routeIs('admin.archives.trash'); @endphp
                <a href="{{ route('admin.archives.trash') }}" 
                   class="{{ $baseClass }} {{ $isTrashActive ? $activeClass : $inactiveClass }}">
                    <svg class="w-5 h-5 transition-colors {{ $isTrashActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>{{ __('Tong Sampah') }}</span>
                </a>
                
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
                
                @if(auth()->user()->role->name === 'super_admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="{{ $mobileClasses }}">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="{{ $mobileClasses }}">
                        {{ __('Manajemen User') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="{{ $mobileClasses }}">
                        {{ __('Kategori Arsip') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.archives.index')" :active="request()->routeIs('admin.archives.index')" class="{{ $mobileClasses }}">
                        {{ __('Manajemen Arsip') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.archives.trash')" :active="request()->routeIs('admin.archives.trash')" class="{{ $mobileClasses }}">
                        {{ __('Tong Sampah') }}
                    </x-responsive-nav-link>
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
                    <x-responsive-nav-link :href="route('profile.edit')" class="justify-center py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold">
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
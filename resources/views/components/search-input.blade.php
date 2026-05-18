@props([
    'route' => '#',
    'placeholder' => 'Cari...',
    'searchParam' => 'search',
    'buttonText' => 'Cari',
    'resetButton' => true,
    'filters' => []
])

@php
    $hasActiveFilter = false;
    foreach(array_keys($filters) as $filterName) {
        if(request($filterName)) {
            $hasActiveFilter = true;
            break;
        }
    }
@endphp

<div x-data="{ filterOpen: false }" class="relative">
    <form method="GET" action="{{ $route }}" class="flex flex-wrap gap-2 items-center">
        <!-- Search Input -->
        <div class="relative">
            <input type="text" name="{{ $searchParam }}" value="{{ request($searchParam) }}" 
            placeholder="{{ $placeholder }}" 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 text-sm w-64 bg-white">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        <!-- Tombol Cari -->
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
            {{ $buttonText }}
        </button>

        <!-- Tombol Filter -->
        <div class="relative">
            <button type="button" @click="filterOpen = !filterOpen" 
                class="relative px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium bg-gray-300 hover:bg-gray-200 transition flex items-center gap-2 text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter
                @if($hasActiveFilter)
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 rounded-full"></span>
                @endif
            </button>

            <!-- Dropdown Panel Filter -->
            <div x-show="filterOpen" @click.away="filterOpen = false" x-cloak
                class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-lg z-20 p-4">
                <div class="space-y-3">
                    @foreach($filters as $filterName => $filter)
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $filter['label'] }}</label>
                            <select name="{{ $filterName }}" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua</option>
                                @foreach($filter['options'] as $optValue => $optLabel)
                                    <option value="{{ $optValue }}" {{ request($filterName) == $optValue ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    <div class="flex justify-end pt-2">
                        <button type="button" @click="filterOpen = false" class="text-xs text-gray-500 hover:text-gray-700">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Reset -->
        @if($resetButton && (request($searchParam) || $hasActiveFilter))
            <a href="{{ $route }}" class="bg-gray-300 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium transition border border-gray-300">
                Reset
            </a>
        @endif
    </form>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
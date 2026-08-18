<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('survey-harga.index') }}" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Buat Usulan Harga Baru</h2>
                <p class="text-sm text-slate-500 mt-0.5">Formulir Survei Harga Pasar (SSH / SBU / HSPK / ASB)</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <form method="POST" action="{{ route('survey-harga.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.survey-harga._form')
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('survey-harga.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-7 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition">
                    Simpan Usulan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

{{-- 1. Mengubah overflow-hidden menjadi overflow-visible agar dropdown search/filter tidak terpotong, 
     ditambahkan flex-col dan gap agar di mobile posisi judul & search rapi berjarak vertical --}}
<div class="bg-white overflow-visible shadow-sm sm:rounded-[1.5rem] p-4 sm:p-6 border border-gray-100 flex flex-col gap-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $title ?? 'Daftar Data' }}</h3>
        </div>
        
        {{-- Tempat untuk menyisipkan input pencarian / filter --}}
        @if(isset($actions))
            <div class="w-full sm:w-auto">
                {{ $actions }}
            </div>
        @endif
    </div>
    
    {{-- 2. Menambahkan z-10 agar area tabel berada di layer yang benar.
         - Di mobile (layar kecil), pembungkus ini akan mengaktifkan scroll horizontal (w-full overflow-x-auto block).
         - Di desktop/tablet (md ke atas), kita kembalikan ke w-full biasa agar overflow-visible dari dropdown tidak terganggu sampingnya. --}}
    <div class="w-full overflow-x-auto md:overflow-visible block relative z-10 rounded-xl border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            {{-- MODIFIKASI: Menambahkan class styling seragam ke level tr/thead --}}
            <thead class="bg-slate-50">
                <tr class="text-slate-500 uppercase text-[10px] sm:text-xs font-bold tracking-wider border-b border-gray-100">
                    {{ $thead }}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                {{ $tbody }}
            </tbody>
        </table>

        {{-- Jika data kosong --}}
        @if(isset($isEmpty) && $isEmpty)
            <div class="text-center py-20 sm:py-40 bg-white">
                <p class="text-gray-500 italic text-sm">{{ $emptyMessage ?? 'Tidak ada data yang ditemukan.' }}</p>
            </div>
        @endif
    </div>
</div>
{{-- Partial form dipakai oleh modal Tambah & Edit --}}
@php $isEdit = $isEdit ?? false; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

    {{-- NIK --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">NIK <span class="text-rose-500">*</span></label>
        <input type="text" name="nik" maxlength="16" pattern="\d{16}" required
               value="{{ old('nik') }}"
               placeholder="16 digit angka"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-mono">
        @error('nik') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- NIP / NRK --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">NIP / NRK</label>
        <input type="text" name="nip_nrk"
               value="{{ old('nip_nrk') }}"
               placeholder="NIP untuk PNS/P3K, NRK untuk BSN"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-mono">
        @error('nip_nrk') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Nama Lengkap --}}
    <div class="sm:col-span-2">
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap (termasuk gelar) <span class="text-rose-500">*</span></label>
        <input type="text" name="nama_lengkap" required
               value="{{ old('nama_lengkap') }}"
               placeholder="Contoh: Drs. Ahmad Fadilah, M.Sc."
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50">
        @error('nama_lengkap') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Jenis Kelamin --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
        <select name="jenis_kelamin" required class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            <option value="">Pilih...</option>
            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
        @error('jenis_kelamin') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Kategori --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kategori Kepegawaian <span class="text-rose-500">*</span></label>
        <select name="kategori" required class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            <option value="">Pilih...</option>
            @foreach (\App\Models\Pegawai::kategoriList() as $kat)
                <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
            @endforeach
        </select>
        @error('kategori') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Jabatan --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jabatan <span class="text-rose-500">*</span></label>
        <input type="text" name="jabatan" required
               value="{{ old('jabatan') }}"
               placeholder="Jabatan saat ini"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
        @error('jabatan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Unit Kerja --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Unit Kerja / Divisi <span class="text-rose-500">*</span></label>
        <select name="unit_kerja" required class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            <option value="">Pilih Departemen...</option>
            @foreach (\App\Models\Department::orderBy('name')->pluck('name') as $dept)
                <option value="{{ $dept }}" {{ old('unit_kerja') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
        @error('unit_kerja') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Pangkat Golongan --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pangkat / Golongan Ruang</label>
        <input type="text" name="pangkat_golongan"
               value="{{ old('pangkat_golongan') }}"
               placeholder="Contoh: Penata Tk. I / III-d"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
    </div>

    {{-- Pendidikan Terakhir --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pendidikan Terakhir</label>
        <select name="pendidikan_terakhir" class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            <option value="">Pilih...</option>
            @foreach (\App\Models\Pegawai::pendidikanList() as $pend)
                <option value="{{ $pend }}" {{ old('pendidikan_terakhir') === $pend ? 'selected' : '' }}>{{ $pend }}</option>
            @endforeach
        </select>
    </div>

    {{-- TMT SK --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">TMT SK</label>
        <input type="date" name="tmt_sk"
               value="{{ old('tmt_sk') }}"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
    </div>

    {{-- Masa Kontrak --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Masa Kontrak</label>
        <input type="text" name="masa_kontrak"
               value="{{ old('masa_kontrak') }}"
               placeholder="Contoh: 2026-12-31 atau Permanen"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
    </div>

    {{-- Jam Kerja Mingguan --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jam Kerja/Minggu <span class="text-rose-500">*</span></label>
        <input type="number" name="jam_kerja_mingguan" step="0.5" min="1" max="60" required
               value="{{ old('jam_kerja_mingguan', 37.5) }}"
               placeholder="37.5"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
        @error('jam_kerja_mingguan') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Status Keaktifan <span class="text-rose-500">*</span></label>
        <select name="status" required class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            @foreach (\App\Models\Pegawai::statusList() as $st)
                <option value="{{ $st }}" {{ old('status', 'Aktif') === $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>
        @error('status') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- No HP --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">No. HP</label>
        <input type="text" name="no_hp"
               value="{{ old('no_hp') }}"
               placeholder="0812xxxxxxxx"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-xs font-bold text-slate-600 mb-1.5">Email</label>
        <input type="email" name="email"
               value="{{ old('email') }}"
               placeholder="nama@instansi.go.id"
               class="w-full text-sm border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-50">
    </div>

</div>

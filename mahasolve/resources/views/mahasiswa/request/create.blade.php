@extends('layouts.app')

@section('title', 'Buat Request Layanan Baru — Mahasolve')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 py-4">
    <!-- BREADCRUMB & HEADER -->
    <div class="space-y-1">
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
            &larr; Kembali ke Katalog Layanan
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 font-display tracking-tight pt-2">Buat Request Layanan Baru</h1>
        <p class="text-xs text-slate-500">Ajukan spesifikasi kebutuhan jasa atau tugas khusus agar mitra provider dapat memberikan penawaran terbaik.</p>
    </div>

    <!-- FORM CARD -->
    <form method="POST" action="{{ route('mahasiswa.request.store') }}"
          novalidate
          x-data="{
              currentKat: '{{ $selectedKategori ?? old('kategori', 'Antar Jemput') }}',
              detailError: false,
              alertMessage: '',
              validateForm(e) {
                  const textarea = $el.querySelector('textarea[name=\'detail_kebutuhan\']');
                  if (!textarea || !textarea.value.trim()) {
                      e.preventDefault();
                      this.detailError = true;
                      this.alertMessage = 'Mohon lengkapi detail kebutuhan jasa sebelum membuat request.';
                      $el.scrollIntoView({ behavior: 'smooth' });
                      return false;
                  }
                  this.detailError = false;
                  this.alertMessage = '';
              }
          }"
          @submit="validateForm($event)"
          class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm space-y-5">
        @csrf

        {{-- CUSTOM VALIDATION ALERT BANNER --}}
        <div x-show="alertMessage" x-transition class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center justify-between gap-3 shadow-sm" style="display: none;">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-rose-500 flex items-center justify-center text-white shrink-0 font-bold shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-[11px] font-extrabold text-rose-900 uppercase tracking-wider font-display">Peringatan Validasi</p>
                    <p class="text-xs font-semibold text-rose-700 mt-0.5" x-text="alertMessage"></p>
                </div>
            </div>
            <button type="button" @click="alertMessage = ''" class="text-rose-400 hover:text-rose-600 font-bold text-xs cursor-pointer px-2">✕</button>
        </div>

        @if (isset($selectedProvider) && $selectedProvider)
            <input type="hidden" name="id_provider" value="{{ $selectedProvider->id_provider }}">
            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shrink-0" style="background:#4F46E5;">
                        {{ strtoupper(substr($selectedProvider->user->username, 0, 1)) }}
                    </span>
                    <div>
                        <p class="text-xs font-bold text-indigo-900">Request Ditujukan Khusus Ke Mitra:</p>
                        <p class="text-sm font-extrabold text-indigo-700">{{ $selectedProvider->user->name ?? $selectedProvider->user->username }} (@ {{ $selectedProvider->user->username }})</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 bg-indigo-600 text-white rounded-full text-[10px] font-bold uppercase tracking-wider">Mitra Pilihan</span>
            </div>
        @endif

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Layanan</label>
            <select name="kategori" x-model="currentKat" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                <option value="Antar Jemput">🛵 Antar Jemput</option>
                <option value="Print & Fotokopi">🖨️ Print &amp; Fotokopi</option>
                <option value="Bimbingan">🎓 Bimbingan &amp; Tutor</option>
                <option value="Desain & Editing">🎨 Desain &amp; Editing</option>
                <option value="Titip Makan">🍱 Titip Makan</option>
                <option value="Titip Beli">🛍️ Titip Beli</option>
            </select>
        </div>

        {{-- LOKASI TERSTRUKTUR KHUSUS MOBILITAS & TITIP --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200/80" x-show="['Antar Jemput', 'Titip Makan', 'Titip Beli'].includes(currentKat)">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Titik Jemput / Toko Asal (opsional)</label>
                <input type="text" name="lokasi_jemput" value="{{ old('lokasi_jemput') }}" placeholder="Contoh: Parkiran Belakang Unikom / Kantin TC"
                       class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Titik Antar / Tujuan (opsional)</label>
                <input type="text" name="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}" placeholder="Contoh: Kos Dago 120 / Stasiun Bandung"
                       class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-indigo-500 transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Detail Kebutuhan Jasa <span class="text-rose-500">*</span></label>
            <textarea name="detail_kebutuhan" rows="4" placeholder="Jelaskan kebutuhan tugas, spesifikasi koding, atau detail pesanan..." required
                      @input="detailError = false; alertMessage = ''"
                      :class="detailError ? 'border-rose-500 ring-2 ring-rose-200 bg-rose-50/20' : 'border-slate-200 bg-slate-50'"
                      class="w-full px-4 py-2.5 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 transition">{{ old('detail_kebutuhan') }}</textarea>
            <p x-show="detailError" class="text-xs font-bold text-rose-600 mt-1.5 flex items-center gap-1.5" style="display:none;">
                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01"/></svg>
                Detail kebutuhan jasa wajib diisi sebelum mengirim request.
            </p>
        </div>

        <div x-show="['Print & Fotokopi', 'Bimbingan', 'Desain & Editing'].includes(currentKat)">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kriteria Output / Format Hasil (opsional)</label>
            <textarea name="kriteria_output" rows="2" placeholder="Contoh: File PDF & PPT, revisi maksimal 2 kali, dsb."
                      class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">{{ old('kriteria_output') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Perkiraan Budget (Rp)</label>
                <input type="number" name="harga_awal" value="{{ old('harga_awal') }}" min="0" placeholder="Contoh: 50000"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Batas Waktu / Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline') }}"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-md shadow-indigo-500/20 transition cursor-pointer">
                Buat Request Layanan
            </button>
            <a href="{{ route('catalog.index') }}" class="px-5 py-3 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs rounded-2xl transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Kelola Layanan — Provider Mahasolve')

@section('content')
<div x-data="myServiceApp()" class="mx-auto max-w-7xl px-6 py-10 space-y-8">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold uppercase tracking-wider mb-2">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Provider Center
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kelola Layanan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Atur daftar jasa, tarif, dan deskripsi layanan yang kamu tawarkan untuk mahasiswa Unikom.</p>
        </div>
        <div>
            <button @click="openModal = true" type="button" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs rounded-2xl transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/35 hover:-translate-y-0.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Layanan Baru
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50/90 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- LAYANAN GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- LOOP DATA DARI DATABASE -->
        @forelse($services as $service)
        @php
            $bgGradient = match($service->kategori) {
                'Antar Jemput' => 'from-indigo-600 to-blue-700',
                'Print & Fotokopi' => 'from-teal-500 to-emerald-600',
                'Bimbingan' => 'from-amber-500 to-orange-600',
                'Desain & Editing' => 'from-pink-500 to-rose-600',
                'Titip Makan' => 'from-rose-500 to-red-600',
                'Titip Beli' => 'from-purple-600 to-indigo-700',
                default => 'from-indigo-600 to-violet-700',
            };
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 h-full group">
            <div>
                <!-- HEADER VISUAL BANNER -->
                <div class="h-36 bg-gradient-to-r {{ $bgGradient }} p-5 relative flex flex-col justify-between text-white overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <span class="text-xs backdrop-blur-md bg-white/20 px-3 py-1 rounded-xl flex items-center gap-1.5 shadow-sm border border-white/20 font-bold">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ $service->kategori ?? 'Layanan' }}
                        </span>
                        <span class="bg-white/25 backdrop-blur-md text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm border border-white/20">
                            {{ $service->status ?? 'Aktif' }}
                        </span>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-bold text-white text-base truncate">{{ $service->nama_layanan }}</h3>
                    </div>
                </div>

                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span class="font-medium text-slate-500">Estimasi: <strong>{{ $service->estimasi_pengerjaan ?? '1 Hari' }}</strong></span>
                        <span class="flex items-center gap-1 font-bold text-slate-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-100">
                            <svg class="w-3.5 h-3.5 fill-current text-amber-400" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            0.0 (0)
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                        {{ $service->deskripsi ?? 'Tidak ada deskripsi layanan.' }}
                    </p>
                </div>
            </div>

            <div class="p-6 pt-0 space-y-4">
                <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Harga Pas</span>
                    <span class="text-lg font-black text-indigo-600 font-display">Rp{{ number_format($service->harga, 0, ',', '.') }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button"
                        @click="openEdit({{ json_encode($service) }})"
                        class="py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl transition text-center cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>

                    <button type="button"
                        @click="confirmDelete({{ json_encode($service) }})"
                        class="py-2.5 border border-rose-200/80 hover:bg-rose-50 text-rose-600 font-bold text-xs rounded-xl transition text-center cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
        @empty
        @endforelse

        <!-- KARTU TAMBAH LAYANAN -->
        <div @click="openModal = true"
            class="border-2 border-dashed border-slate-200 rounded-3xl p-6 sm:p-8 flex flex-col items-center justify-center text-center gap-3 bg-slate-50/50 hover:bg-white hover:border-indigo-400 hover:shadow-lg transition-all duration-300 group cursor-pointer min-h-[290px] w-full">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition">Buat Layanan Baru</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-[200px]">Tawarkan keahlian barumu kepada mahasiswa Unikom lainnya.</p>
            </div>
        </div>

    </div>

    <!-- STATISTIK SECTION -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 grid grid-cols-1 md:grid-cols-3 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">

        <div class="flex items-center gap-4 pt-4 md:pt-0">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 leading-none mb-1 font-display">{{ $totalLayanan ?? 0 }}</h3>
                <p class="text-xs font-semibold text-slate-400">Total layanan aktif</p>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 md:pt-0 md:pl-6">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 leading-none mb-1 font-display">{{ $totalOrder ?? 0 }}</h3>
                <p class="text-xs font-semibold text-slate-400">Total order selesai</p>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 md:pt-0 md:pl-6">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-black text-slate-900 leading-none mb-1 font-display">{{ number_format($rataRataRating ?? 0, 1) }}</h3>
                <p class="text-xs font-semibold text-slate-400">Rating rata-rata</p>
            </div>
        </div>

    </div>

    <!-- MODAL FORM TAMBAH LAYANAN -->
    <div x-show="openModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">

        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl shadow-indigo-950/20 border border-slate-100 relative my-auto max-h-[85vh] overflow-y-auto" @click.away="openModal = false">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100 sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base font-display">Tambah Layanan Baru</h3>
                        <p class="text-[11px] text-slate-400">Tampilkan penawaran jasa kamu di katalog Unikom.</p>
                    </div>
                </div>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-700 p-2 rounded-2xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('provider.services.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Layanan</label>
                    <input type="text" name="nama_layanan" required placeholder="Contoh: Desain PPT Sidang Aesthetic & Rapi"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Resmi Mahasolve</label>
                    <select name="kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        <option value="Antar Jemput">Antar Jemput</option>
                        <option value="Print & Fotokopi">Print & Fotokopi</option>
                        <option value="Bimbingan">Bimbingan</option>
                        <option value="Desain & Editing" selected>Desain & Editing</option>
                        <option value="Titip Makan">Titip Makan</option>
                        <option value="Titip Beli">Titip Beli</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Pas (Rp)</label>
                        <input type="number" name="harga" required placeholder="45000"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Pengerjaan</label>
                        <input type="text" name="estimasi_pengerjaan" placeholder="Contoh: 1-2 hari kerja" value="1 hari"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Layanan</label>
                    <textarea name="deskripsi" rows="3" placeholder="Jelaskan detail cakupan jasa yang kamu tawarkan..."
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openModal = false"
                        class="px-5 py-2.5 border border-slate-200 text-slate-600 text-xs font-bold rounded-2xl hover:bg-slate-50 transition text-center cursor-pointer">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-2xl shadow-md shadow-indigo-500/20 transition text-center cursor-pointer">Publikasikan Layanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL FORM EDIT LAYANAN -->
    <div x-show="openEditModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">

        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl shadow-indigo-950/20 border border-slate-100 relative my-auto max-h-[85vh] overflow-y-auto" @click.away="openEditModal = false">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100 sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base font-display">Edit Layanan</h3>
                        <p class="text-[11px] text-slate-400">Perbarui rincian tarif atau deskripsi layanan kamu.</p>
                    </div>
                </div>
                <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-slate-700 p-2 rounded-2xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('/my-service') }}/' + editItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Layanan</label>
                    <input type="text" name="nama_layanan" x-model="editItem.nama" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Resmi Mahasolve</label>
                    <select name="kategori" x-model="editItem.kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        <option value="Antar Jemput">Antar Jemput</option>
                        <option value="Print &amp; Fotokopi">Print &amp; Fotokopi</option>
                        <option value="Bimbingan">Bimbingan</option>
                        <option value="Desain &amp; Editing">Desain &amp; Editing</option>
                        <option value="Titip Makan">Titip Makan</option>
                        <option value="Titip Beli">Titip Beli</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Harga Pas (Rp)</label>
                    <input type="number" name="harga" x-model="editItem.harga" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Layanan</label>
                    <textarea name="deskripsi" x-model="editItem.deskripsi" rows="3"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="openEditModal = false"
                        class="px-5 py-2.5 border border-slate-200 text-slate-600 text-xs font-bold rounded-2xl hover:bg-slate-50 transition text-center cursor-pointer">Batal</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-2xl shadow-md shadow-indigo-500/20 transition text-center cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CONFIRMATION HAPUS LAYANAN -->
    <div x-show="openDeleteModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">

            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl shadow-indigo-950/20 relative text-center space-y-4 border border-slate-100" @click.away="openDeleteModal = false">
                <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner border border-rose-100">
                    <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h3 class="font-bold text-slate-900 text-base font-display">Hapus Layanan Ini?</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Apakah kamu yakin ingin menghapus <strong class="text-slate-800" x-text="deleteItem.nama"></strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>

                <form :action="'{{ url('/my-service') }}/' + deleteItem.id" method="POST" class="flex items-center gap-3 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="w-1/2 py-3 border border-slate-200 text-slate-600 text-xs font-bold rounded-2xl hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-2xl shadow-md shadow-rose-500/20 transition cursor-pointer">
                        Hapus Layanan
                    </button>
                </form>
            </div>
        </div>

</div>

<script>
function myServiceApp() {
    return {
        openModal: false,
        openEditModal: false,
        openDeleteModal: false,
        editItem: { id: '', nama: '', kategori: 'Desain & Editing', harga: 0, deskripsi: '' },
        deleteItem: { id: '', nama: '' },
        openEdit(service) {
            this.editItem = {
                id: service.id_layanan,
                nama: service.nama_layanan || '',
                kategori: service.kategori || 'Desain & Editing',
                harga: service.harga || 0,
                deskripsi: service.deskripsi || ''
            };
            this.openEditModal = true;
        },
        confirmDelete(service) {
            this.deleteItem = {
                id: service.id_layanan,
                nama: service.nama_layanan || 'layanan ini'
            };
            this.openDeleteModal = true;
        }
    }
}
</script>
@endsection
@extends('layouts.app')

@section('title', 'Kelola Order & Negosiasi — Provider Mahasolve')

@section('content')
<script>
    function orderApp() {
        return {
            openNegoModal: false,
            counterPrice: '',
            counterNote: '',

            // Safe Blade JSON Mapping
            orders: @json($orders),

            get activeOrderIndex() {
                const urlParams = new URLSearchParams(window.location.search);
                const activeId = parseInt(urlParams.get('active'));
                
                if (activeId) {
                    const index = this.orders.findIndex(o => (o.raw_id === activeId || o.id === activeId || o.id_request === activeId));
                    return index !== -1 ? index : (this.orders.length > 0 ? 0 : null);
                }
                return this.orders.length > 0 ? 0 : null;
            },

            get activeOrder() {
                if (this.activeOrderIndex !== null && this.orders.length > 0) {
                    return this.orders[this.activeOrderIndex];
                }
                return null;
            },

            formatNumber(num) {
                if (!num) return '0';
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        };
    }
</script>

<div x-data="orderApp()" class="w-full space-y-8">

        <!-- FLASH NOTIFICATION -->
        @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex justify-between items-center shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        @endif

        <!-- HEADER BANNER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 font-display">Pesanan Masuk</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">Kelola negosiasi &amp; pesanan dari mahasiswa.</p>
            </div>
        </div>

        <!-- GRID ORDER & NEGO LAYOUT -->
        <template x-if="orders.length === 0">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-5 shadow-sm">
                <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto text-3xl font-bold shadow-inner">
                    📥
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-slate-900 text-lg font-display">Belum Ada Order Masuk</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                        Saat ini belum ada pesanan atau negosiasi aktif. Buka Dashboard Provider untuk memantau permintaan baru dari mahasiswa atau kelola daftar layanan yang Anda tawarkan.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <a href="{{ route('provider.dashboard') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Cek Permintaan Baru
                    </a>
                    <a href="{{ route('my-service') }}" class="px-5 py-2.5 border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs rounded-xl transition">
                        Kelola Layanan Saya
                    </a>
                </div>
            </div>
        </template>

        <template x-if="orders.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- SIDEBAR DAFTAR ORDER (Kiri) -->
                <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-4 space-y-3">
                    <template x-for="(order, index) in orders" :key="order.id">
                        <a :href="'{{ route('order') }}?active=' + order.raw_id"
                            :class="activeOrderIndex === index ? 'border-indigo-500 bg-indigo-50/40 shadow-sm' : 'border-slate-100 hover:border-indigo-200 hover:bg-slate-50/50'"
                            class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-center justify-between gap-3 block">

                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-white font-bold text-sm"
                                    :class="order.avatarBg || 'bg-indigo-600'">
                                    <span x-text="(order.customerName || 'M').charAt(0)"></span>
                                </div>
                                <div class="truncate">
                                    <h4 class="font-bold text-slate-800 text-sm truncate" x-text="order.title"></h4>
                                    <p class="text-xs text-slate-400 mt-0.5 truncate" x-text="order.customerName + ' • ' + order.date"></p>
                                </div>
                            </div>

                            <div class="shrink-0 text-right">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider block"
                                    :class="{
                                        'bg-amber-100 text-amber-700': order.status === 'Negosiasi',
                                        'bg-sky-100 text-sky-700': order.status === 'Diproses',
                                        'bg-emerald-100 text-emerald-700': order.status === 'Selesai',
                                        'bg-rose-100 text-rose-700': order.status === 'Ditolak'
                                    }"
                                    x-text="order.status">
                                </span>
                            </div>
                        </a>
                    </template>
                </div>

                <!-- DETAIL ORDER & NEGO CHAT (Kanan) -->
                <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <template x-if="activeOrder">
                        <div>
                        <!-- BANNER STATUS ORDER -->
                        <div class="bg-gradient-to-r from-sky-500 via-indigo-600 to-indigo-700 p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-white/20 p-1 shrink-0 backdrop-blur-md">
                                    <div class="w-full h-full rounded-full bg-amber-300 flex items-center justify-center font-bold text-amber-900 text-lg"
                                        x-text="(activeOrder.customerName || 'M').charAt(0)"></div>
                                </div>
                                <div>
                                    <span class="bg-white/20 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider backdrop-blur-md"
                                        x-text="'STATUS • ' + activeOrder.status"></span>
                                    <h2 class="text-xl font-bold mt-1 text-white font-display" x-text="activeOrder.title"></h2>
                                    <p class="text-xs text-indigo-100 mt-0.5" x-text="activeOrder.customerName + ' • ' + activeOrder.category"></p>
                                </div>
                            </div>

                            <div class="sm:text-right border-t sm:border-t-0 border-white/10 pt-3 sm:pt-0">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-200 block">Penawaran</span>
                                <span class="text-2xl font-black text-white font-display" x-text="'Rp' + formatNumber(activeOrder.currentPrice)"></span>
                            </div>
                        </div>

                        <!-- INFO METADATA -->
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">ID Pesanan</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="activeOrder.id"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">Tanggal</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="activeOrder.date"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">Kategori</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="activeOrder.category"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-400">Penawaran Mahasiswa</p>
                                    <p class="text-xs font-bold text-indigo-600 mt-0.5 font-display" x-text="'Rp' + formatNumber(activeOrder.customerOffer)"></p>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/50 p-4 rounded-2xl border border-slate-100"
                                x-text="activeOrder.description"></p>

                            <!-- PERCAKAPAN NEGO CHAT BOX -->
                            <div class="border border-slate-200/80 rounded-3xl p-5 space-y-4">
                                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Percakapan Negosiasi
                                </h3>

                                <div class="space-y-3 max-h-80 overflow-y-auto p-1">
                                    <template x-for="chat in activeOrder.chats" :key="chat.id">
                                        <div :class="chat.sender === 'provider' ? 'flex flex-col items-end' : 'flex flex-col items-start'">
                                            <div :class="chat.sender === 'provider' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none' : 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none'"
                                                class="p-3.5 max-w-sm text-xs leading-relaxed shadow-sm">
                                                <p x-text="chat.message"></p>
                                                <template x-if="chat.offeredPrice">
                                                    <div class="mt-2 pt-2 border-t text-[11px] font-bold flex justify-between gap-4"
                                                        :class="chat.sender === 'provider' ? 'border-white/20 text-amber-200' : 'border-slate-200 text-indigo-600'">
                                                        <span>Pengajuan Harga:</span>
                                                        <span x-text="'Rp' + formatNumber(chat.offeredPrice)"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-400 mt-1 px-1" x-text="chat.time"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- FORM BALAS CHAT -->
                                <template x-if="activeOrder.status === 'Negosiasi'">
                                    <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/chat'" method="POST" class="flex gap-2 pt-2 border-t border-slate-100">
                                        @csrf
                                        <input type="text" name="pesan" required placeholder="Ketik balasan pesan..."
                                            class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition shadow-sm cursor-pointer">
                                            Kirim
                                        </button>
                                    </form>
                                </template>
                            </div>

                            <!-- ACTION BUTTONS LARAVEL -->
                            <template x-if="activeOrder.status === 'Negosiasi'">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                    <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/accept'" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Terima Negosiasi
                                        </button>
                                    </form>

                                    <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/reject'" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full py-3 px-4 border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-2xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Tolak Negosiasi
                                        </button>
                                    </form>

                                    <button @click="openNegoModal = true" class="py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-2xl transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Ajukan Negosiasi
                                    </button>
                                </div>
                            </template>

                            <!-- STATUS FINAL BANNER & PROGRESS / DELIVERABLE UPLOAD FORM -->
                            <template x-if="activeOrder.status !== 'Negosiasi'">
                                <div class="space-y-4">
                                    <div class="p-4 rounded-2xl text-xs font-semibold text-center"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border border-emerald-200': activeOrder.status === 'Diproses' || activeOrder.status === 'Selesai' || activeOrder.status === 'Menunggu_pengerjaan',
                                            'bg-rose-50 text-rose-700 border border-rose-200': activeOrder.status === 'Ditolak'
                                        }">
                                        <span x-text="'Status Pesanan: ' + activeOrder.status"></span>
                                    </div>

                                    <template x-if="activeOrder.status !== 'Ditolak'">
                                        <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/progress'" method="POST" class="bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-3">
                                            @csrf
                                            <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                Update Progress &amp; Kirim Deliverable
                                            </h4>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Pengerjaan</label>
                                                <select name="status_pesanan" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500">
                                                    <option value="diproses">Sedang Diproses / Dikerjakan</option>
                                                    <option value="selesai">Selesai &amp; Deliverable Siap</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pesan Progress (opsional)</label>
                                                <input type="text" name="pesan_progress" placeholder="Contoh: Draf awal pengerjaan telah selesai 50%..." class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500">
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Link Dokumen / File Deliverable (opsional)</label>
                                                <input type="text" name="dokumen" placeholder="Contoh: https://drive.google.com/file/d/xyz atau file.pdf" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500">
                                            </div>

                                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                                                Simpan Progress &amp; Submit Deliverable
                                            </button>
                                        </form>

                                        <form :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/cancel'" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini secara darurat?');" class="pt-1">
                                            @csrf
                                            <button type="submit" class="w-full py-2.5 px-4 border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-2xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Batalkan Pesanan (Emergency)
                                            </button>
                                        </form>
                                    </template>
                                </div>
                            </template>

                        </div>
                    </div>
                </template>

                <template x-if="!activeOrder">
                    <div class="p-12 sm:p-16 text-center space-y-4">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto text-2xl font-bold shadow-inner">
                            📋
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-bold text-slate-800 text-base font-display">Belum Ada Order Aktif</h3>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                Belum ada pesanan atau negosiasi yang dipilih. Anda dapat memantau permintaan baru dari mahasiswa di Dashboard Provider.
                            </p>
                        </div>
                        <a href="{{ route('provider.dashboard') }}" class="inline-block px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                            Lihat Dashboard Provider
                        </a>
                    </div>
                </template>
            </div>
        </div>
        </template>

    <!-- MODAL AJUKAN NEGO BALIK -->
    <template x-teleport="body">
        <div x-show="openNegoModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4" x-transition.opacity style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl space-y-4" @click.away="openNegoModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-base font-display">Ajukan Negosiasi Balik</h3>
                <button type="button" @click="openNegoModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form :action="activeOrder ? '{{ url('/order') }}/' + activeOrder.raw_id + '/counter-nego' : '#'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Harga Tawaran Balik (Rp)</label>
                    <input type="number" name="harga_tawaran" x-model="counterPrice" required placeholder="Contoh: 45000"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Penjelasan</label>
                    <textarea name="pesan" x-model="counterNote" rows="3" placeholder="Contoh: Masih bisa kak, kalau Rp45.000 pengerjaan lusa pagi sudah beres."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openNegoModal = false" class="px-4 py-2 border border-slate-200 text-xs font-semibold text-slate-600 rounded-xl hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-xs font-semibold text-white rounded-xl hover:bg-indigo-700">Kirim Penawaran</button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>
@endsection
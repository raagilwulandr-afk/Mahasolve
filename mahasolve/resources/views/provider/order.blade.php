@extends('layouts.app')

@section('title', 'Kelola Order & Negosiasi — Provider Mahasolve')

@section('content')
<script>
    function orderApp() {
        return {
            openNegoModal: false,
            showCancelModal: false,
            counterPrice: '',
            counterNote: '',

            chatInputText: '',

            async sendChatMessage(e) {
                const msg = this.chatInputText ? this.chatInputText.trim() : '';
                if (!msg || !this.activeOrder) return;

                const form = e.target;
                const activeOrder = this.activeOrder;

                // Push message bubble instantly to chat UI (0ms latency)
                const now = new Date();
                const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                
                activeOrder.chats.push({
                    id: Date.now(),
                    message: msg,
                    text: msg,
                    sender: 'provider',
                    isProvider: true,
                    time: timeStr,
                    offeredPrice: null
                });

                this.chatInputText = '';

                // Scroll to bottom of chat
                this.$nextTick(() => {
                    const chatContainer = document.getElementById('chatContainer');
                    if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
                });

                // Send background fetch without page reload
                try {
                    const formData = new FormData(form);
                    await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                } catch (err) {
                    console.error(err);
                }
            },

            async sendCounterNego(e) {
                const form = e.target;
                const activeOrder = this.activeOrder;
                if (!activeOrder) return;

                const formData = new FormData(form);
                const price = formData.get('harga_tawaran');
                const note = formData.get('pesan');
                const msg = note ? note : ('Penawaran balik: Rp' + this.formatNumber(price));

                const now = new Date();
                const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

                activeOrder.chats.push({
                    id: Date.now(),
                    message: msg,
                    text: msg,
                    sender: 'provider',
                    isProvider: true,
                    time: timeStr,
                    offeredPrice: price
                });

                activeOrder.customerOffer = price;
                activeOrder.currentPrice = price;

                // Scroll to bottom
                this.$nextTick(() => {
                    const chatContainer = document.getElementById('chatContainer');
                    if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
                });

                try {
                    await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                } catch (err) {
                    console.error(err);
                }
            },

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
            },

            getStepIndex(status) {
                if (!status) return 0;
                const s = status.toLowerCase();
                if (s === 'selesai') return 3;
                if (['diproses', 'dikerjakan', 'revisi'].includes(s)) return 2;
                if (['menunggu_pengerjaan', 'dikonfirmasi'].includes(s)) return 1;
                return 0;
            }
        };
    }
</script>

<div x-data="orderApp()" class="w-full space-y-8">

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h4 class="text-xs font-bold text-rose-900 uppercase tracking-wider font-display">Terdapat Kesalahan Input</h4>
                    <ul class="mt-1 list-disc list-inside text-xs text-rose-700 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- GRID ORDER & NEGO LAYOUT -->
        <template x-if="orders.length === 0">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-5 shadow-sm">
                <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
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
                                        'bg-indigo-100 text-indigo-700': order.status === 'Menunggu Pengerjaan' || order.status === 'Menunggu_pengerjaan',
                                        'bg-emerald-100 text-emerald-700': order.status === 'Selesai',
                                        'bg-rose-100 text-rose-700': order.status === 'Ditolak' || order.status === 'Dibatalkan'
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

                        <!-- VISUAL STEPPER PROGRESS TRACKER FOR PROVIDER -->
                        <div class="px-6 pt-6">
                            <div class="flex items-center p-4 bg-slate-50 border border-slate-200/70 rounded-2xl">
                                <template x-for="(stepLabel, idx) in ['Dipesan', 'Dikonfirmasi', 'Diproses', 'Selesai']" :key="idx">
                                    <div class="contents">
                                        <div class="flex flex-col items-center flex-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-xs"
                                                 :class="getStepIndex(activeOrder.status) >= idx ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : 'bg-slate-200 text-slate-500'">
                                                <template x-if="getStepIndex(activeOrder.status) >= idx">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                                <template x-if="getStepIndex(activeOrder.status) < idx">
                                                    <span x-text="idx + 1"></span>
                                                </template>
                                            </div>
                                            <span class="text-[11px] font-semibold mt-1" :class="getStepIndex(activeOrder.status) >= idx ? 'text-indigo-700 font-bold' : 'text-slate-400'" x-text="stepLabel"></span>
                                        </div>
                                        <template x-if="idx < 3">
                                            <div class="flex-1 h-1 rounded-full -mt-4 mx-1" :class="getStepIndex(activeOrder.status) > idx ? 'bg-indigo-600' : 'bg-slate-200'"></div>
                                        </template>
                                    </div>
                                </template>
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

                             <!-- PERCAKAPAN NEGO CHAT BOX -->
                             <div class="border border-slate-200/80 rounded-3xl p-5 space-y-4">
                                 <h3 class="font-bold text-slate-800 text-sm flex items-center justify-between">
                                     <span class="flex items-center gap-2 font-display">
                                         <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                         </svg>
                                         Percakapan &amp; Log Pengerjaan
                                     </span>
                                     <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-100">
                                         Live Chat
                                     </span>
                                 </h3>

                                 <div id="chatContainer" class="space-y-3 max-h-80 overflow-y-auto p-1 scroll-smooth">
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

                                 <!-- QUICK INLINE NEGO PRICE COUNTER-OFFER FORM (Nego Langsung In-Chat) -->
                                <template x-if="activeOrder.status === 'Negosiasi'">
                                    <form @submit.prevent="sendCounterNego($event)" :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/counter-nego'" method="POST" class="p-3.5 bg-amber-50/80 border border-amber-200/80 rounded-2xl space-y-2 mb-3">
                                        @csrf
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5 font-display">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 0 0118 0z"/>
                                                </svg>
                                                Tawar Harga Balik Ke Mahasiswa
                                            </span>
                                            <span class="text-[10px] text-amber-700 font-semibold" x-text="'Tawaran Mahasiswa: Rp' + formatNumber(activeOrder.customerOffer)"></span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <div class="relative flex-1">
                                                <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                                                <input type="number" name="harga_tawaran" required :value="activeOrder.currentPrice" placeholder="Nominal tawaran..." autocomplete="off"
                                                    class="w-full pl-9 pr-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-400">
                                            </div>
                                            <input type="text" name="pesan" placeholder="Pesan nego (opsional)..." autocomplete="off"
                                                class="flex-1 px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-400">
                                            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer whitespace-nowrap flex items-center justify-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                </svg>
                                                Kirim Tawaran
                                            </button>
                                        </div>
                                    </form>
                                </template>

                                <!-- INLINE DELIVERABLE & PROGRESS SUBMISSION FORM INSIDE CHAT BOX -->
                                <template x-if="activeOrder.status !== 'Negosiasi' && activeOrder.status !== 'Ditolak' && activeOrder.status !== 'Dibatalkan'">
                                    <div x-data="{ openDeliverableForm: false }" class="pt-2 border-t border-slate-100">
                                        <div class="flex items-center justify-between p-3 bg-indigo-50/80 border border-indigo-100 rounded-2xl">
                                            <span class="text-xs font-bold text-indigo-900 flex items-center gap-2 font-display">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                Update Progress &amp; Upload Deliverable
                                            </span>
                                            <button type="button" @click="openDeliverableForm = !openDeliverableForm"
                                                class="text-xs font-bold text-indigo-700 bg-white hover:bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-200 shadow-2xs transition cursor-pointer flex items-center gap-1.5">
                                                <span>📎</span>
                                                <span x-text="openDeliverableForm ? 'Tutup Panel Upload ▲' : 'Open Upload Berkas ▼'"></span>
                                            </button>
                                        </div>

                                        <form x-show="openDeliverableForm" x-transition :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/progress'" method="POST" enctype="multipart/form-data" class="mt-3 bg-white border border-indigo-100 rounded-2xl p-4 space-y-3 shadow-xs">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pengerjaan</label>
                                                <select name="status_pengerjaan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-indigo-500">
                                                    <option value="dikerjakan">Sedang Diproses / Dikerjakan</option>
                                                    <option value="revisi">Revisi Pengerjaan</option>
                                                    <option value="selesai">Pengerjaan Selesai (Submit Final Deliverable)</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan Progress / Catatan Hasil</label>
                                                <input type="text" name="pesan_progress" autocomplete="off" required placeholder="Contoh: Draf pengerjaan 100% selesai, silakan cek berkas terlampir."
                                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-indigo-500">
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Upload File Berkas (.pdf, .zip, .docx)</label>
                                                    <input type="file" name="file_dokumen" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Atau Link Google Drive / Dropbox</label>
                                                    <input type="url" name="link_drive" placeholder="https://drive.google.com/file/d/..." autocomplete="off"
                                                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-indigo-500">
                                                </div>
                                            </div>

                                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer flex items-center justify-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Simpan Progress &amp; Submit Deliverable
                                            </button>
                                        </form>
                                    </div>
                                </template>

                                <!-- FORM BALAS CHAT STANDARD (Selalu aktif selama pesanan berjalan / tidak dibatalkan) -->
                                <template x-if="activeOrder.status !== 'Ditolak' && activeOrder.status !== 'Dibatalkan'">
                                    <form @submit.prevent="sendChatMessage($event)" :action="'{{ url('/order') }}/' + activeOrder.raw_id + '/chat'" method="POST" class="flex gap-2 pt-2 border-t border-slate-100">
                                        @csrf
                                        <input type="text" x-model="chatInputText" name="pesan" autocomplete="off" required placeholder="Ketik balasan pesan atau instruksi ke mahasiswa..."
                                            class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold transition shadow-sm cursor-pointer flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            Kirim Chat
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

                            <!-- EMERGENCY CANCEL BUTTON FOR ACTIVE ORDERS -->
                            <template x-if="activeOrder.status !== 'Negosiasi' && activeOrder.status !== 'Ditolak' && activeOrder.status !== 'Dibatalkan'">
                                <div class="pt-2">
                                    <button type="button" @click="showCancelModal = true" class="w-full py-2.5 px-4 border border-rose-200 bg-rose-50/60 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-2xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Batalkan Pesanan (Emergency)
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="!activeOrder">
                    <div class="p-12 sm:p-16 text-center space-y-4">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto shadow-inner">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9m12 0a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
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

    <!-- MODAL BATALKAN PESANAN DARURAT -->
    <div x-show="showCancelModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4" x-transition.opacity style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl space-y-4" @click.away="showCancelModal = false">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="text-center space-y-1">
                <h3 class="font-bold text-slate-900 text-base font-display">Batalkan Pesanan Secara Darurat?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Tindakan ini akan menghentikan transaksi secara permanen. Pastikan Anda telah menginformasikan alasan pembatalan kepada mahasiswa pemesan.
                </p>
            </div>
            <form :action="activeOrder ? '{{ url('/order') }}/' + activeOrder.raw_id + '/cancel' : '#'" method="POST" class="flex items-center gap-2 pt-2">
                @csrf
                <button type="button" @click="showCancelModal = false" class="w-1/2 py-2.5 border border-slate-200 text-xs font-bold text-slate-700 rounded-xl hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm cursor-pointer">
                    Ya, Batalkan Pesanan
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
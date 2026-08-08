{{-- resources/views/receipt.blade.php --}}

<!-- STYLES KHUSUS UNTUK MODAL & PRINT STRUK -->
<style>
    @keyframes qrGlow {
        0%, 100% { box-shadow: 0 0 0 rgba(79, 70, 229, 0); }
        50% { box-shadow: 0 0 14px rgba(79, 70, 229, 0.22); }
    }
    .receipt-qr-code { 
        animation: qrGlow 2.5s ease-in-out infinite; 
    }

    /* Tampilan Khusus Cetak/Print */
    @media print {
        body * { 
            visibility: hidden !important; 
        }
        #printable-receipt, #printable-receipt * { 
            visibility: visible !important; 
        }
        #printable-receipt { 
            position: fixed !important; 
            left: 0 !important; 
            top: 0 !important; 
            width: 100% !important; 
            padding: 20px !important;
            box-shadow: none !important;
            border: none !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

<!-- MODAL POP-UP STRUK (Diatur oleh Alpine.js) -->
<div 
    x-show="showReceiptModal" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
    style="display: none;"
>
    <!-- Modal Dialog Box -->
    <div 
        @click.away="showReceiptModal = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-100"
    >
        <!-- Tombol Close (X) -->
        <button 
            @click="showReceiptModal = false" 
            type="button"
            class="no-print absolute top-5 right-5 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition"
        >
            ✕
        </button>

        <!-- AREA KONTEN STRUK (Area Ini yang Akan Dicetak) -->
        <div id="printable-receipt">
            
            <!-- Notifikasi Pembayaran Berhasil -->
            <div class="no-print mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm flex items-start gap-3">
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">
                    ✓
                </div>
                <div>
                    <h2 class="text-sm font-bold text-emerald-700">Pembayaran Berhasil</h2>
                    <p class="mt-0.5 text-xs text-emerald-600">Struk pembayaran telah diverifikasi secara resmi oleh sistem.</p>
                </div>
            </div>

            <!-- KARTU STRUK PEMBAYARAN -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                
                <!-- Header Struk -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-slate-50/50">
                    <div>
                        <h1 class="text-lg font-bold text-slate-800">Struk Pembayaran</h1>
                        <p class="mt-0.5 text-xs text-slate-400 font-mono">No. INV20260731038</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
                            M
                        </div>
                        <span class="font-bold text-slate-700 text-sm">Mahasolve</span>
                    </div>
                </div>

                <!-- Detail Transaksi -->
                <div class="grid grid-cols-2 gap-6 px-6 py-5 text-xs">
                    <div>
                        <p class="mb-1 text-slate-400">Detail Mahasiswa</p>
                        <h3 class="font-semibold text-slate-800">Andini Pratama</h3>
                        <p class="mt-0.5 text-slate-500">NIM 10124087 · IF</p>
                    </div>
                    <div>
                        <p class="mb-1 text-slate-400">Tanggal Pembayaran</p>
                        <h3 class="font-semibold text-slate-800">31 Juli 2026</h3>
                        <p class="mt-0.5 text-slate-500">14:16 WIB</p>
                    </div>
                    <div>
                        <p class="mb-1 text-slate-400">Penyedia Jasa</p>
                        <h3 class="font-semibold text-slate-800">Alya Rahmawati</h3>
                        <p class="mt-0.5 text-slate-500">Desain & Editing</p>
                    </div>
                    <div>
                        <p class="mb-1 text-slate-400">Metode Pembayaran</p>
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                            Mahasolve Pay
                        </span>
                    </div>
                </div>

                <!-- Rincian Biaya -->
                <div class="border-t border-slate-200 px-6 py-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-slate-800">Desain Layanan</p>
                            <p class="text-[11px] text-slate-500">Desain PPT · 20 slide</p>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Rp40.000</p>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <p class="text-xs text-slate-500">Biaya layanan (5%)</p>
                        <p class="text-xs font-medium text-slate-700">Rp2.000</p>
                    </div>
                    <div class="flex items-center justify-between pt-4">
                        <p class="text-sm font-bold text-slate-800">Total Pembayaran</p>
                        <p class="text-xl font-bold text-indigo-600">Rp42.000</p>
                    </div>
                </div>

                <!-- QR Code Verification -->
                <div class="mx-6 mb-6 flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="receipt-qr-code grid h-16 w-16 shrink-0 grid-cols-7 gap-0.5 bg-white p-1">
                        <span class="bg-slate-900"></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span>
                        <span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span>
                        <span class="bg-slate-900"></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span>
                        <span></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span></span>
                        <span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span>
                        <span class="bg-slate-900"></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span>
                        <span></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span></span><span class="bg-slate-900"></span><span class="bg-slate-900"></span><span></span>
                    </div>
                    <div>
                        <h3 class="text-xs font-semibold text-slate-800">Scan Verifikasi</h3>
                        <p class="mt-0.5 text-[11px] leading-4 text-slate-500">Struk digital resmi Mahasolve. Simpan sebagai bukti transaksi.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Tombol Aksi Bawah Modal -->
        <div class="no-print mt-6 flex items-center justify-end gap-3">
            <button 
                @click="showReceiptModal = false"
                type="button" 
                class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition"
            >
                Tutup
            </button>
            <button 
                onclick="window.print()" 
                type="button" 
                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition"
            >
                🖨 Cetak Struk
            </button>
        </div>

    </div>
</div>
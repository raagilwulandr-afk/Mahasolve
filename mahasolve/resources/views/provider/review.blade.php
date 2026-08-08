@extends('layouts.app')

@section('title', 'Riwayat Transaksi Provider — Mahasolve')

@section('content')
<div x-data="{ activeTab: 'semua', showReceiptModal: false, selectedReceipt: null }" class="mx-auto max-w-7xl px-6 py-6 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Riwayat Penyedia Jasa</h1>
            <p class="text-sm text-slate-500 mt-1">Pesanan yang telah selesai beserta pendapatan & ulasan dari mahasiswa.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1 -->
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-900">{{ $stats->total_pesanan }}</p>
                <p class="text-xs font-medium text-slate-500">Total pesanan</p>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-600 shrink-0">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-900">Rp{{ number_format($stats->total_pendapatan, 0, ',', '.') }}</p>
                <p class="text-xs font-medium text-slate-500">Total pendapatan</p>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500 shrink-0">
                <svg class="w-6 h-6 text-amber-500 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-900">{{ $stats->rating }}</p>
                <p class="text-xs font-medium text-slate-500">Rating diterima</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 p-1.5 bg-white rounded-2xl border border-slate-200 shadow-sm w-fit">
        <button
            @click="activeTab = 'semua'"
            :class="activeTab === 'semua' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 font-medium hover:bg-slate-50'"
            class="px-5 py-2 rounded-xl text-xs transition">
            Semua
        </button>

        <button
            @click="activeTab = 'selesai'"
            :class="activeTab === 'selesai' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 font-medium hover:bg-slate-50'"
            class="px-5 py-2 rounded-xl text-xs transition">
            Selesai
        </button>

        <button
            @click="activeTab = 'dibatalkan'"
            :class="activeTab === 'dibatalkan' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 font-medium hover:bg-slate-50'"
            class="px-5 py-2 rounded-xl text-xs transition">
            Dibatalkan
        </button>
    </div>

    <!-- History List -->
    <div class="space-y-4">
        @forelse($histories as $history)
        <div x-show="activeTab === 'semua' || activeTab === '{{ strtolower($history->status) }}'"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">

            <!-- Transaction Header -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex gap-4">
                    <!-- Icon -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>

                    <!-- Title & Meta -->
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-slate-900">{{ $history->title }}</h3>
                            <!-- Status Badge -->
                            @if($history->status === 'Selesai')
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">Selesai</span>
                            @else
                            <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-700">Dibatalkan</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 0 0118 0z" />
                            </svg>
                            {{ $history->date }} · {{ $history->category }} · {{ $history->customer_name }}
                        </p>
                    </div>
                </div>

                <!-- Income -->
                <div class="text-left sm:text-right">
                    @if($history->status === 'Selesai')
                    <p class="text-lg font-bold text-indigo-600">Rp{{ number_format($history->income, 0, ',', '.') }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Pendapatan bersih</p>
                    @else
                    <p class="text-sm font-bold text-slate-400 line-through">Rp{{ number_format($history->income, 0, ',', '.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Review Section -->
            @if($history->status === 'Selesai' && $history->has_review)
            <div class="mt-4 sm:ml-14 rounded-xl border border-slate-100 bg-slate-50 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-200 text-xs font-bold text-indigo-700 shrink-0">
                        {{ strtoupper(substr($history->customer_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700">Ulasan dari {{ $history->customer_name }}</p>
                        <div class="flex text-amber-400 text-[10px] mt-0.5">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-3 h-3 {{ $i < $history->rating ? 'text-amber-400 fill-current' : 'text-slate-300 fill-current' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-600 italic">"{{ $history->review_text }}"</p>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($history->status === 'Selesai')
            <div class="mt-4 sm:ml-14 flex items-center gap-3">
                <button @click="showReceiptModal = true" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Lihat Struk Digital
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
            <p class="font-semibold text-slate-700">Belum ada riwayat pesanan.</p>
            <p class="mt-1 text-xs text-slate-400">Pesanan yang selesai atau dibatalkan akan muncul di sini.</p>
        </div>
        @endforelse
    </div> 

    <!-- Include Pop-up Struk -->
    @include('provider.receipt')

</div>
@endsection
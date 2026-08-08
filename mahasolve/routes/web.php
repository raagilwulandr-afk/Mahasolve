<?php

use App\Http\Controllers\Mahasiswa\CatalogController;
use App\Http\Controllers\Mahasiswa\RequestLayananController;
use App\Http\Controllers\DetailPekerjaanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NegosiasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RatingReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provider\ProviderDashboardController;
use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\Provider\OrderController as ProviderOrderController;
use App\Http\Controllers\Provider\ReviewController as ProviderReviewController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTES ---
Route::get('/favicon.ico', function () {
    return response()->file(public_path('favicon.ico'), [
        'Content-Type' => 'image/x-icon',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
});
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- PROFILE ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- DASHBOARD REDIRECT BASED ON ROLE ---
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'provider') {
        return redirect()->route('provider.dashboard');
    }
    return redirect()->route('catalog.index');
})->name('dashboard');

// --- SHARED AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/katalog/provider/{provider}', [CatalogController::class, 'showProvider'])->name('catalog.provider');
});

// --- MAHASISWA & CLIENT ORDERING ROUTES ---
Route::middleware(['auth'])->group(function () {
    Route::post('/katalog/order-direct', [CatalogController::class, 'storeDirectOrder'])->name('catalog.direct-order');

    // Request layanan milik mahasiswa
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::resource('request', RequestLayananController::class);
    });

    // Negosiasi
    Route::post('/request/{requestLayanan}/negosiasi/{provider}', [NegosiasiController::class, 'store'])->name('negosiasi.store');
    Route::get('/negosiasi/{negosiasi}', [NegosiasiController::class, 'show'])->name('negosiasi.show');
    Route::post('/negosiasi/{negosiasi}/tawar-ulang', [NegosiasiController::class, 'counterOffer'])->name('negosiasi.counter');
    Route::post('/negosiasi/{negosiasi}/setuju', [NegosiasiController::class, 'accept'])->name('negosiasi.accept');
    Route::post('/negosiasi/{negosiasi}/tolak', [NegosiasiController::class, 'reject'])->name('negosiasi.reject');

    // Pesanan
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{pesanan}/struk', [PesananController::class, 'struk'])->name('pesanan.struk');

    // Review & Pembayaran
    Route::get('/review', [RatingReviewController::class, 'index'])->name('review.index');
    Route::post('/pesanan/{pesanan}/detail-pekerjaan', [DetailPekerjaanController::class, 'store'])->name('detailPekerjaan.store');
    Route::post('/pesanan/{pesanan}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::post('/pesanan/{pesanan}/review', [RatingReviewController::class, 'store'])->name('review.store');
    Route::patch('/pesanan/{pesanan}/review', [RatingReviewController::class, 'update'])->name('review.update');
});

// --- PROVIDER ROUTES ---
Route::middleware(['auth', 'role:provider'])->group(function () {
    Route::get('/provider/dashboard', [ProviderDashboardController::class, 'index'])->name('provider.dashboard');

    // Kelola Layanan
    Route::get('/my-service', [ServiceController::class, 'index'])->name('my-service');
    Route::get('/provider/my-service', [ServiceController::class, 'index'])->name('provider.my-service');
    Route::post('/my-service', [ServiceController::class, 'store'])->name('provider.services.store');
    Route::delete('/my-service/{id}', [ServiceController::class, 'destroy'])->name('provider.services.destroy');
    Route::put('/my-service/{id}', [ServiceController::class, 'update'])->name('provider.services.update');

    // Manajemen Order & Negosiasi Masuk
    Route::get('/order', [ProviderOrderController::class, 'index'])->name('order');
    Route::get('/provider/order', [ProviderOrderController::class, 'index'])->name('provider.order');
    Route::post('/order/{id}/chat', [ProviderOrderController::class, 'sendMessage'])->name('order.chat');
    Route::post('/order/{id}/counter-nego', [ProviderOrderController::class, 'counterNego'])->name('order.counter');
    Route::post('/order/{id}/accept', [ProviderOrderController::class, 'acceptNego'])->name('order.accept');
    Route::post('/order/{id}/reject', [ProviderOrderController::class, 'rejectNego'])->name('order.reject');
    Route::post('/order/{id}/progress', [ProviderOrderController::class, 'updateProgress'])->name('order.progress');

    // Review Provider
    Route::get('/provider/review', [ProviderReviewController::class, 'index'])->name('provider.review');

    // Provider Request Routes & Order Complete Action
    Route::get('/provider/requests', function () {
        return redirect()->route('order');
    })->name('provider.requests.index');

    Route::get('/provider/requests/{id}', function ($id) {
        return redirect()->route('order', ['active' => $id]);
    })->name('provider.requests.show');

    Route::post('/provider/withdraw', [\App\Http\Controllers\Provider\ProviderDashboardController::class, 'withdraw'])->name('provider.withdraw');
    Route::post('/order/{id}/cancel', [ProviderOrderController::class, 'cancelOrder'])->name('order.cancel');
});

require __DIR__.'/auth.php';

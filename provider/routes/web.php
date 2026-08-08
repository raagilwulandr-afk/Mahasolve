<?php

use Illuminate\Support\Carbon;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Provider\ReviewController;

// --- DUMMY HYBRID CLASS SUPER LENGKAP ---
if (!class_exists('BladeSmartDummy')) {
    class BladeSmartDummy implements \IteratorAggregate, \Stringable, \Countable
    {
        public $mahasiswa;
        public $user;
        public $nama_layanan = 'Layanan Dummy';
        public $status = 'pending';
        public $id = 1;

        public function __construct()
        {
            $this->mahasiswa = new \stdClass();
            $this->mahasiswa->name = 'Siswa Dummy';
            $this->mahasiswa->nama = 'Siswa Dummy';

            $this->user = new \stdClass();
            $this->user->name = 'User Dummy';
        }

        public function __toString(): string
        {
            return '0';
        }

        public function getIterator(): \Traversable
        {
            return new \ArrayIterator([]);
        }

        public function count(): int
        {
            return 0;
        }

        public function __get($name)
        {
            if ($name === 'mahasiswa') {
                return $this->mahasiswa;
            }
            if ($name === 'user') {
                return $this->user;
            }
            return '0';
        }

        public function __call($method, $args)
        {
            if ($method === 'count') {
                return 0;
            }
            if (in_array($method, ['first', 'last'])) {
                return $this;
            }
            return collect([]);
        }
    }
}

// --- PUBLIC ROUTES ---
Route::get('/', function () {
    return view('welcome');
});

// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- PROVIDER DASHBOARD (UTAMA) ---
    Route::get('/dashboard', function () {
        $smartDummy = new \BladeSmartDummy();

        // Variabel Angka / Statistik Murni
        $totalPendapatan   = 0;
        $pesananAktif      = 0;
        $jumlahReview      = 0;
        $totalLayanan      = 0;
        $jumlahLayanan     = 0;
        $totalPesanan      = 0;
        $pesananSelesai    = 0;
        $pesananDibatalkan = 0;
        $pesananBatal      = 0;
        $rating            = 0;
        $rataRataRating    = 0;
        $saldo             = 0;

        // Data Collection Kosong (Akan terisi jika ada data di database)
        $permintaanBaru = collect([]);
        $layananAktif   = collect([]);

        // Variabel Smart Dummy
        $mahasiswa          = $smartDummy->mahasiswa;
        $pesananBaru        = $smartDummy;
        $requests           = $smartDummy;
        $orders             = $smartDummy;
        $listPesananBaru    = $smartDummy;
        $listPermintaanBaru = $permintaanBaru;
        $pesananTerbaru     = $smartDummy;
        $transaksiTerbaru   = $smartDummy;
        $layananPopuler     = $smartDummy;
        $notifikasi         = $smartDummy;

        return view('provider.dashboard', compact(
            'mahasiswa',
            'totalPendapatan',
            'pesananAktif',
            'layananAktif',
            'pesananBaru',
            'permintaanBaru',
            'jumlahReview',
            'totalLayanan',
            'jumlahLayanan',
            'totalPesanan',
            'pesananSelesai',
            'pesananDibatalkan',
            'pesananBatal',
            'rating',
            'rataRataRating',
            'saldo',
            'listPesananBaru',
            'listPermintaanBaru',
            'requests',
            'orders',
            'pesananTerbaru',
            'transaksiTerbaru',
            'layananPopuler',
            'notifikasi'
        ));
    })->name('dashboard');

    // Alias URL untuk /provider/dashboard
    Route::redirect('/provider/dashboard', '/dashboard')->name('provider.dashboard');

    // --- KELOLA LAYANAN (MY SERVICE) ---
    Route::get('/my-service', [ServiceController::class, 'index'])->name('my-service');
    Route::post('/my-service', [ServiceController::class, 'store'])->name('provider.services.store');

    // --- MANAJEMEN ORDER & NEGOSIASI (DITERAPKAN) ---
    Route::get('/order', [OrderController::class, 'index'])->name('order');
    Route::post('/order/{id}/chat', [OrderController::class, 'sendMessage'])->name('order.chat');
    Route::post('/order/{id}/counter-nego', [OrderController::class, 'counterNego'])->name('order.counter');
    Route::post('/order/{id}/accept', [OrderController::class, 'acceptNego'])->name('order.accept');
    Route::post('/order/{id}/reject', [OrderController::class, 'rejectNego'])->name('order.reject');

    // --- PLACEHOLDER ROUTES LAINNYA ---

    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

    Route::get('/review', [ReviewController::class, 'index'])->name('review');

    Route::get('/', function () {
        return view('welcome');
    })->name('home'); // <-- Tambahkan ->name('home') di sini

    Route::post('/orders/complete/{id?}', function () {
        return back();
    })->name('orders.complete');

    // Route Requests (Index, Show & Accept)
    Route::get('/provider/requests', function () {
        return "Halaman Permintaan Masuk (Sementara)";
    })->name('provider.requests.index');

    Route::get('/provider/requests/{request?}', function ($request = null) {
        $id = is_object($request) ? ($request->id ?? 1) : $request;
        return "Halaman Detail Permintaan ID: " . $id;
    })->name('provider.requests.show');

    Route::patch('/provider/requests/{id}/accept', function ($id) {
        return back();
    })->name('provider.requests.accept');
}); // Penutup Route::middleware('auth')

require __DIR__ . '/auth.php';

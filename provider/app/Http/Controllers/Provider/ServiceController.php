<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Service; 

class ServiceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil semua layanan milik provider yang sedang login
        $services = Service::where('user_id', $userId)->latest()->get();

        // 2. Hitung statistik riil dari database
        $totalLayanan = $services->count();

        $totalOrder = Schema::hasTable('orders') 
            ? DB::table('orders')->where('provider_id', $userId)->count() 
            : 0;

        $rataRataRating = Schema::hasTable('reviews') 
            ? DB::table('reviews')->where('provider_id', $userId)->avg('rating') ?? 0.0 
            : 0.0;

        // 3. Ambil Notifikasi Penting (Cek tabel notifications jika ada)
        $notifications = Schema::hasTable('notifications')
            ? DB::table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->latest()
                ->take(5)
                ->get()
            : collect([]); // Collection kosong jika belum ada tabel/data

        return view('provider.my-service', compact(
            'services',
            'totalLayanan',
            'totalOrder',
            'rataRataRating',
            'notifications'
        ));
    }

    // ... method store() tetap sama
}
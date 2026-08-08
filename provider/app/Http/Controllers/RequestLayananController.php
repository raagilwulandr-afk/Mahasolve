<?php

namespace App\Http\Controllers;

use App\Models\MatchingProvider;
use App\Models\RequestLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RequestLayananController extends Controller
{
    public function indexProvider(Request $request)
    {
        $provider = $request->user()->provider;

        abort_if(!$provider, 403, 'Data provider tidak ditemukan.');

        $permintaan = MatchingProvider::query()
            ->with([
                'requestLayanan.mahasiswa',
                'requestLayanan.layanan',
            ])
            ->where('id_provider', $provider->id_provider)
            ->latest('tanggal_matching')
            ->paginate(10);

        return view('requests.index', compact(
            'provider',
            'permintaan'
        ));
    }

    public function show(
        Request $request,
        MatchingProvider $matching
    ) {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $matching->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke permintaan ini.'
        );

        $matching->load([
            'requestLayanan.mahasiswa',
            'requestLayanan.layanan',
            'negosiasi',
        ]);

        return view('requests.show', compact('matching'));
    }

    public function accept(
        Request $request,
        MatchingProvider $matching
    ) {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $matching->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke permintaan ini.'
        );

        DB::beginTransaction();

        try {
            $matching->update([
                'status_matching' => 'dipilih',
            ]);

            $matching->requestLayanan()->update([
                'status_request' => 'dinegosiasikan',
            ]);

            DB::commit();

            return redirect()
                ->route('provider.requests.show', $matching)
                ->with(
                    'success',
                    'Permintaan diterima. Silakan buat penawaran harga.'
                );
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->with(
                'error',
                'Permintaan gagal diterima.'
            );
        }
    }

    public function reject(
        Request $request,
        MatchingProvider $matching
    ) {
        $provider = $request->user()->provider;

        abort_if(
            !$provider ||
                $matching->id_provider !== $provider->id_provider,
            403,
            'Anda tidak memiliki akses ke permintaan ini.'
        );

        $matching->update([
            'status_matching' => 'ditolak',
        ]);

        return redirect()
            ->route('provider.requests.index')
            ->with('success', 'Permintaan berhasil ditolak.');
    }
}

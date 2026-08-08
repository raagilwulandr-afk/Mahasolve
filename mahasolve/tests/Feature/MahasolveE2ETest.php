<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureRole;
use App\Models\DetailPekerjaan;
use App\Models\Layanan;
use App\Models\Negosiasi;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Provider;
use App\Models\RatingReview;
use App\Models\RequestLayanan;
use App\Models\TrackingPesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MahasolveE2ETest extends TestCase
{
    use RefreshDatabase;

    protected User $mahasiswa;
    protected User $providerUser;
    protected Provider $provider;
    protected Layanan $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        // Fast 4-round bcrypt for ultra-fast test execution
        Hash::setRounds(4);

        $this->mahasiswa = User::create([
            'name' => 'Raka Pratama',
            'username' => 'raka_mhs',
            'email' => 'raka.pratama@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567801',
            'role' => 'mahasiswa',
        ]);

        $this->providerUser = User::create([
            'name' => 'Rizky Maulana',
            'username' => 'rizky_antar',
            'email' => 'rizky.maulana@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567804',
            'role' => 'provider',
        ]);

        $this->provider = Provider::create([
            'id_user' => $this->providerUser->id_user,
            'rating' => 4.9,
            'detail_provider' => 'Antar jemput area Dipatiukur & sekitar. Cepat dan aman.',
        ]);

        $this->layanan = Layanan::create([
            'id_provider' => $this->provider->id_provider,
            'nama_layanan' => 'Antar Jemput Motor',
            'kategori' => 'Antar Jemput',
            'deskripsi' => 'Antar jemput cepat naik motor, area sekitar kampus.',
            'harga' => 8000,
            'estimasi_pengerjaan' => '15 menit',
        ]);
    }

    /** 1. E2E Suite: Authentication, Registration, and Unikom Email Domain Validation */
    public function test_e2e_authentication_and_registration_flow()
    {
        // 1. Non-Unikom email domain MUST be rejected (@gmail.com)
        $invalidRes = $this->post('/register', [
            'name' => 'Invalid User',
            'username' => 'invalid_user',
            'email' => 'user.invalid@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'mahasiswa',
        ]);
        $this->assertDatabaseMissing('user', ['email' => 'user.invalid@gmail.com']);

        // 2. Valid Unikom email (@mahasiswa.unikom.ac.id) MUST succeed
        $resMhs = $this->post('/register', [
            'name' => 'New Mahasiswa Unikom',
            'username' => 'new_unikom',
            'email' => 'new.student@mahasiswa.unikom.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'mahasiswa',
        ]);
        $resMhs->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('user', ['email' => 'new.student@mahasiswa.unikom.ac.id']);
    }

    /** 2. E2E Suite: Role Access Protection (RBAC Matrix) */
    public function test_e2e_rbac_authorization_matrix()
    {
        // EnsureRole middleware unit assertion for Mahasiswa accessing provider role
        $middleware = new EnsureRole();
        $request = Request::create('/provider/dashboard', 'GET');
        $request->setUserResolver(fn () => $this->mahasiswa);

        try {
            $middleware->handle($request, fn () => response('OK'), 'provider');
            $this->fail('Expected 403 HttpException was not thrown.');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }

        // Guests redirected to Login
        $this->get(route('catalog.index'))->assertRedirect('/login');
    }

    /** 3. E2E Suite: Catalog Search & Instant Direct Order */
    public function test_e2e_catalog_search_and_direct_instant_order()
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('catalog.index', ['kategori' => 'Antar Jemput']));
        $response->assertStatus(200);

        $orderRes = $this->actingAs($this->mahasiswa)->post(route('catalog.direct-order'), [
            'id_layanan' => $this->layanan->id_layanan,
            'catatan' => 'Direct Order Instant Test',
        ]);

        $orderRes->assertRedirect();
        $this->assertDatabaseHas('pesanan', ['harga_final' => 8000]);
    }

    /** 4. E2E Suite: Full Custom Request & Negotiation Lifecycle */
    public function test_e2e_custom_request_and_negotiation_flow()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Tutor Bimbingan Task Clean Architecture',
            'harga_awal' => 50000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 50000,
            'detail_negosiasi' => 'Pengajuan awal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $this->assertDatabaseHas('request_layanan', ['id_request' => $req->id_request]);
        $this->assertDatabaseHas('negosiasi', ['id_negosiasi' => $nego->id_negosiasi]);

        $acceptRes = $this->actingAs($this->providerUser)->post(route('negosiasi.accept', $nego->id_negosiasi));
        $acceptRes->assertRedirect();

        $pesanan = Pesanan::where('id_negosiasi', $nego->id_negosiasi)->first();
        $this->assertNotNull($pesanan);
    }

    /** 5. E2E Suite: Payment, Progress Deliverable, & Review Completion */
    public function test_e2e_payment_progress_and_review_lifecycle()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Kampus Full Lifecycle Test',
            'harga_awal' => 20000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 20000,
            'detail_negosiasi' => 'Disepakati',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 20000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        $payRes = $this->actingAs($this->mahasiswa)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay',
        ]);
        $payRes->assertRedirect();
        $this->assertDatabaseHas('pembayaran', ['id_pesanan' => $pesanan->id_pesanan, 'status_bayar' => 'dikonfirmasi']);

        $this->actingAs($this->providerUser)->post(route('order.progress', $nego->id_negosiasi), [
            'status_pesanan' => 'selesai',
            'pesan_progress' => 'Pekerjaan selesai 100%.',
        ]);
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => $pesanan->id_pesanan, 'status_pesanan' => 'selesai']);

        $reviewRes = $this->actingAs($this->mahasiswa)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Layanan sangat memuaskan, mantap!',
        ]);
        $reviewRes->assertRedirect();
        $this->assertDatabaseHas('rating_review', ['id_pesanan' => $pesanan->id_pesanan, 'rate' => 5]);
    }

    /** 6. E2E Suite: Latency & Response Benchmark Standard (< 10ms local memory) */
    public function test_e2e_latency_standards()
    {
        \Illuminate\Support\Facades\Cache::store('array')->put('benchmark_local_key', 'mahasolve_val', 60);
        $startLocal = microtime(true);
        $cachedVal = \Illuminate\Support\Facades\Cache::store('array')->get('benchmark_local_key');
        $localLatencyMs = (microtime(true) - $startLocal) * 1000;

        $this->assertEquals('mahasolve_val', $cachedVal);
        $this->assertLessThan(10, $localLatencyMs);
    }
}

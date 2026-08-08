<?php

namespace Tests\Feature;

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

        // 1. Setup Mahasiswa User
        $this->mahasiswa = User::create([
            'name' => 'Raka Pratama',
            'username' => 'raka_mhs',
            'email' => 'raka.pratama@student.ac.id',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567801',
            'role' => 'mahasiswa',
        ]);

        // 2. Setup Provider User & Provider Profile
        $this->providerUser = User::create([
            'name' => 'Rizky Maulana',
            'username' => 'rizky_antar',
            'email' => 'rizky.maulana@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567804',
            'role' => 'provider',
        ]);

        $this->provider = Provider::create([
            'id_user' => $this->providerUser->id_user,
            'rating' => 4.9,
            'detail_provider' => 'Antar jemput area Dipatiukur & sekitar. Cepat dan aman.',
        ]);

        // 3. Setup Catalog Service
        $this->layanan = Layanan::create([
            'id_provider' => $this->provider->id_provider,
            'nama_layanan' => 'Antar Jemput Motor',
            'kategori' => 'Antar Jemput',
            'deskripsi' => 'Antar jemput cepat naik motor, area sekitar kampus.',
            'harga' => 8000,
            'estimasi_pengerjaan' => '15 menit',
        ]);
    }

    /** 1. Test User Registration by Role */
    public function test_guest_can_register_as_mahasiswa_and_provider()
    {
        $responseMhs = $this->post('/register', [
            'name' => 'New Mahasiswa User',
            'username' => 'new_mahasiswa',
            'email' => 'new.mhs@student.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_hp' => '081299990001',
            'role' => 'mahasiswa',
        ]);
        $responseMhs->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('user', ['email' => 'new.mhs@student.ac.id', 'role' => 'mahasiswa']);

        // Logout
        $this->post('/logout');

        $responsePrv = $this->post('/register', [
            'name' => 'New Provider User',
            'username' => 'new_provider',
            'email' => 'new.provider@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_hp' => '081299990002',
            'role' => 'provider',
            'detail_provider' => 'Jasa Titip Makan Terpercaya',
        ]);
        $responsePrv->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('user', ['email' => 'new.provider@gmail.com', 'role' => 'provider']);
        $this->assertDatabaseHas('provider', ['id_user' => User::where('email', 'new.provider@gmail.com')->first()->id_user]);
    }

    /** 2. Test Login Authentication & Role Redirection */
    public function test_auth_login_redirection_by_role()
    {
        $responseMhs = $this->post('/login', [
            'email' => 'raka.pratama@student.ac.id',
            'password' => 'password123',
        ]);
        $responseMhs->assertRedirect(route('catalog.index'));

        $this->post('/logout');

        $responsePrv = $this->post('/login', [
            'email' => 'rizky.maulana@gmail.com',
            'password' => 'password123',
        ]);
        $responsePrv->assertRedirect(route('provider.dashboard'));
    }

    /** 3. Test Logout Session Termination */
    public function test_auth_logout_clears_session()
    {
        $this->actingAs($this->mahasiswa);
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** 4. Test RBAC Route Authorization Protection */
    public function test_rbac_authorization_blocks_unauthorized_access()
    {
        // Mahasiswa cannot access Provider Dashboard or My Service
        $response = $this->actingAs($this->mahasiswa)->get(route('provider.dashboard'));
        $response->assertStatus(403);

        $responseService = $this->actingAs($this->mahasiswa)->get(route('my-service'));
        $responseService->assertStatus(403);

        $responseOrder = $this->actingAs($this->mahasiswa)->get(route('order'));
        $responseOrder->assertStatus(403);
    }

    /** 5. Test Catalog Browsing, Search, and Category Filtering */
    public function test_mahasiswa_catalog_search_and_category_filters()
    {
        $responseCatalog = $this->actingAs($this->mahasiswa)->get(route('catalog.index'));
        $responseCatalog->assertStatus(200);
        $responseCatalog->assertSee('rizky_antar');

        $responseCategory = $this->actingAs($this->mahasiswa)->get(route('catalog.index', ['kategori' => 'Antar Jemput']));
        $responseCategory->assertStatus(200);
        $responseCategory->assertSee('Antar Jemput');

        $responseSearch = $this->actingAs($this->mahasiswa)->get(route('catalog.index', ['search' => 'rizky']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('rizky_antar');
    }

    /** 6. Test Custom Service Request CRUD */
    public function test_mahasiswa_custom_request_crud()
    {
        // Create Request
        $responseCreate = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.request.store'), [
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Banner Seminar Nasional Unikom',
            'kriteria_output' => 'Format PNG HD & PDF',
            'harga_awal' => 50000,
            'deadline' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $req = RequestLayanan::first();
        $responseCreate->assertRedirect(route('mahasiswa.request.show', $req->id_request));
        $this->assertDatabaseHas('request_layanan', ['detail_kebutuhan' => 'Desain Banner Seminar Nasional Unikom']);

        // View Request
        $responseShow = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.request.show', $req->id_request));
        $responseShow->assertStatus(200);

        // Edit Request
        $responseUpdate = $this->actingAs($this->mahasiswa)->put(route('mahasiswa.request.update', $req->id_request), [
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Banner Seminar Nasional Unikom (Revisi Budget)',
            'kriteria_output' => 'Format PNG HD & PDF',
            'harga_awal' => 55000,
            'deadline' => now()->addDays(6)->format('Y-m-d'),
        ]);
        $this->assertDatabaseHas('request_layanan', ['harga_awal' => 55000]);

        // Delete / Cancel Request
        $responseDelete = $this->actingAs($this->mahasiswa)->delete(route('mahasiswa.request.destroy', $req->id_request));
        $this->assertDatabaseHas('request_layanan', ['id_request' => $req->id_request, 'status_request' => 'dibatalkan']);
    }

    /** 7. Test Direct Instant Order Creation */
    public function test_direct_instant_order_creation()
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('catalog.direct-order'), [
            'id_layanan' => $this->layanan->id_layanan,
            'catatan' => 'Jemput di depan Gerbang Utama Unikom.',
        ]);

        $pesanan = Pesanan::first();
        $response->assertRedirect(route('pesanan.show', $pesanan->id_pesanan));
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $pesanan->id_pesanan,
            'harga_final' => 8000,
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);
    }

    /** 8. Test Negotiation Chat Thread and Counter Offer Flow */
    public function test_negosiasi_chat_thread_and_counter_offer()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Slide Presentasi 15 Halaman',
            'harga_awal' => 60000,
            'status_request' => 'open',
        ]);

        // 1. Mahasiswa initiates negotiation
        $responseInit = $this->actingAs($this->mahasiswa)->post(route('negosiasi.store', [$req->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => 60000,
            'detail_negosiasi' => 'Halo kak, apakah bisa pengerjaan 2 hari?',
        ]);

        $nego = Negosiasi::where('id_request', $req->id_request)->first();
        $this->assertNotNull($nego);

        // 2. Provider counters offer
        $this->actingAs($this->providerUser)->post(route('order.counter', $nego->id_negosiasi), [
            'harga_tawaran' => 50000,
            'pesan' => 'Bisa kak, Rp50.000 saja pengerjaan lusa.',
        ]);
        $this->assertDatabaseHas('negosiasi', ['harga_tawaran' => 50000, 'status_negosiasi' => 'ditawar_ulang']);

        // 3. Mahasiswa accepts offer
        $responseAccept = $this->actingAs($this->mahasiswa)->post(route('negosiasi.accept', $nego->id_negosiasi));
        $responseAccept->assertSessionHas('success');
        $this->assertDatabaseHas('negosiasi', ['id_negosiasi' => $nego->id_negosiasi, 'status_negosiasi' => 'disepakati']);
    }

    /** 9. Test QRIS Payment Confirmation and Pesanan Status Update */
    public function test_qris_payment_confirmation_and_status_update()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Motor Campus',
            'harga_awal' => 8000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Checkout instan',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        $responsePayment = $this->actingAs($this->mahasiswa)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
        ]);

        $responsePayment->assertSessionHas('success');
        $this->assertDatabaseHas('pembayaran', ['id_pesanan' => $pesanan->id_pesanan, 'status_bayar' => 'dikonfirmasi']);
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => $pesanan->id_pesanan, 'status_pesanan' => 'dikerjakan']);
    }

    /** 10. Test Provider Service Management CRUD */
    public function test_provider_service_management_crud()
    {
        // Create Service
        $responseCreate = $this->actingAs($this->providerUser)->post(route('provider.services.store'), [
            'nama_layanan' => 'Jasa Titip Print Skripsi',
            'kategori' => 'Print & Fotokopi',
            'harga' => 25000,
            'deskripsi' => 'Print & jilid lakban skripsi rapi.',
            'estimasi_pengerjaan' => '1 hari',
        ]);
        $responseCreate->assertSessionHas('success');
        $this->assertDatabaseHas('layanan', ['nama_layanan' => 'Jasa Titip Print Skripsi']);

        $service = Layanan::where('nama_layanan', 'Jasa Titip Print Skripsi')->first();

        // Update Service
        $responseUpdate = $this->actingAs($this->providerUser)->put(route('provider.services.update', $service->id_layanan), [
            'nama_layanan' => 'Jasa Titip Print Skripsi Premium',
            'kategori' => 'Print & Fotokopi',
            'harga' => 30000,
            'deskripsi' => 'Print & jilid softcover skripsi rapi.',
            'estimasi_pengerjaan' => '1 hari',
        ]);
        $this->assertDatabaseHas('layanan', ['id_layanan' => $service->id_layanan, 'harga' => 30000]);

        // Delete Service
        $responseDelete = $this->actingAs($this->providerUser)->delete(route('provider.services.destroy', $service->id_layanan));
        $this->assertDatabaseMissing('layanan', ['id_layanan' => $service->id_layanan]);
    }

    /** 11. Test Provider Order Management and Deliverable Upload */
    public function test_provider_order_management_and_deliverable_upload()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Poster Event',
            'harga_awal' => 50000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 50000,
            'detail_negosiasi' => 'Deal poster event',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 50000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'dikerjakan',
        ]);

        // Provider updates progress & uploads deliverables
        $responseProgress = $this->actingAs($this->providerUser)->post(route('order.progress', $nego->id_negosiasi), [
            'status_pesanan' => 'selesai',
            'pesan_progress' => 'Pekerjaan selesai! Berkas poster telah diunggah.',
            'dokumen' => 'https://drive.google.com/file/d/sample-poster/view',
        ]);

        $responseProgress->assertSessionHas('success');
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => $pesanan->id_pesanan, 'status_pesanan' => 'selesai']);
        $this->assertDatabaseHas('tracking_pesanan', ['id_pesanan' => $pesanan->id_pesanan, 'file_progress' => 'https://drive.google.com/file/d/sample-poster/view']);
    }

    /** 12. Test Digital Receipt Generation */
    public function test_digital_receipt_generation()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Motor Campus',
            'harga_awal' => 8000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Checkout instan',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
            'total_bayar' => 8000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        $responseStruk = $this->actingAs($this->mahasiswa)->get(route('pesanan.struk', $pesanan->id_pesanan));
        $responseStruk->assertStatus(200);
        $responseStruk->assertSee('Struk Pembayaran');
        $responseStruk->assertSee('8.000');
    }

    /** 13. Test Rating Review Submission and Provider Rating Calculation */
    public function test_rating_review_submission_and_rating_calculation()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Motor Campus',
            'harga_awal' => 8000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Checkout instan',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
            'total_bayar' => 8000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        // Submit Review & Rating
        $responseReview = $this->actingAs($this->mahasiswa)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Layanan sangat cepat, ramah, dan tepat waktu!',
        ]);

        $responseReview->assertSessionHas('success');
        $this->assertDatabaseHas('rating_review', [
            'id_pesanan' => $pesanan->id_pesanan,
            'rate' => 5,
            'review' => 'Layanan sangat cepat, ramah, dan tepat waktu!',
        ]);
    }

    /** 14. Test Provider Review History View */
    public function test_provider_review_history_view()
    {
        $responseReviewPage = $this->actingAs($this->providerUser)->get(route('provider.review'));
        $responseReviewPage->assertStatus(200);
        $responseReviewPage->assertSee('Riwayat & Ulasan');
    }

    /** 15. Test Profile Information and Password Update */
    public function test_user_profile_information_and_password_update()
    {
        // Update Profile Info
        $responseProfile = $this->actingAs($this->mahasiswa)->patch(route('profile.update'), [
            'name' => 'Raka Pratama Updated',
            'username' => 'raka_updated',
            'email' => 'raka.pratama@student.ac.id',
            'no_hp' => '081299887766',
        ]);
        $responseProfile->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('user', ['id_user' => $this->mahasiswa->id_user, 'username' => 'raka_updated']);
    }

    /** 16. Test Provider Can View Assigned Negotiation Thread and Pesanan Details */
    public function test_provider_can_view_assigned_negotiation_thread_and_pesanan_details()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Motor Campus',
            'harga_awal' => 8000,
            'status_request' => 'open',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Negosiasi antar',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        // Provider can view negotiation thread
        $responseNego = $this->actingAs($this->providerUser)->get(route('negosiasi.show', $nego->id_negosiasi));
        $responseNego->assertStatus(200);

        // Provider can view pesanan detail
        $responsePesanan = $this->actingAs($this->providerUser)->get(route('pesanan.show', $pesanan->id_pesanan));
        $responsePesanan->assertStatus(200);
    }

    /** 17. Test Unauthorized 3rd Party User Cannot Access Another User's Negotiation or Pesanan */
    public function test_unauthorized_third_party_user_cannot_view_negotiation_or_pesanan()
    {
        $thirdPartyUser = User::create([
            'name' => 'Third Party Mahasiswa',
            'username' => 'third_party',
            'email' => 'thirdparty@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Private',
            'harga_awal' => 8000,
            'status_request' => 'open',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Private nego',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        // 3rd Party Mahasiswa gets 403 on another user's negotiation
        $responseNego = $this->actingAs($thirdPartyUser)->get(route('negosiasi.show', $nego->id_negosiasi));
        $responseNego->assertStatus(403);

        // 3rd Party Mahasiswa gets 403 on another user's pesanan
        $responsePesanan = $this->actingAs($thirdPartyUser)->get(route('pesanan.show', $pesanan->id_pesanan));
        $responsePesanan->assertStatus(403);
    }

    /** 18. Test Form Validation Rejects Invalid Payloads */
    public function test_form_validation_rejects_invalid_payloads()
    {
        // Invalid registration payload (missing password confirmation)
        $responseReg = $this->post('/register', [
            'name' => 'Invalid User',
            'email' => 'invalid@gmail.com',
            'password' => 'password123',
        ]);
        $responseReg->assertSessionHasErrors(['password']);

        // Invalid service payload (missing price and name)
        $responseService = $this->actingAs($this->providerUser)->post(route('provider.services.store'), [
            'kategori' => 'Antar Jemput',
        ]);
        $responseService->assertSessionHasErrors(['nama_layanan', 'harga']);
    }

    /** 19. Test Duplicate Payment Submission Handled Gracefully */
    public function test_duplicate_payment_submission_handled_gracefully()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Motor',
            'harga_awal' => 8000,
            'status_request' => 'open',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        // First Payment
        $this->actingAs($this->mahasiswa)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
        ]);

        // Second Payment Attempt
        $responseSecond = $this->actingAs($this->mahasiswa)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
        ]);

        $responseSecond->assertSessionHas('success');
        $this->assertEquals(1, Pembayaran::where('id_pesanan', $pesanan->id_pesanan)->count());
    }

    /** 20. Test Guest Can View Landing Page */
    public function test_guest_can_view_landing_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** 21. Test Guest Can View Login and Register Forms */
    public function test_guest_can_view_login_and_register_forms()
    {
        $responseLogin = $this->get('/login');
        $responseLogin->assertStatus(200);

        $responseRegister = $this->get('/register');
        $responseRegister->assertStatus(200);
    }

    /** 22. Test Login With Wrong Password Fails */
    public function test_login_with_wrong_password_fails()
    {
        $response = $this->post('/login', [
            'email' => 'raka.pratama@student.ac.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** 23. Test Guest Redirect to Login for Protected Routes */
    public function test_guest_redirect_to_login_for_protected_routes()
    {
        $this->get(route('pesanan.index'))->assertRedirect('/login');
        $this->get(route('catalog.index'))->assertRedirect('/login');
        $this->get(route('profile.edit'))->assertRedirect('/login');
    }

    /** 24. Test Mahasiswa Dashboard Shows Active Activities and History */
    public function test_mahasiswa_dashboard_shows_active_activities_and_history()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Dashboard Test',
            'harga_awal' => 8000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Dashboard nego',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'dikerjakan',
        ]);

        $response = $this->actingAs($this->mahasiswa)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Antar Jemput Dashboard Test');
    }

    /** 25. Test Mahasiswa Pesanan Index Master Detail View */
    public function test_mahasiswa_pesanan_index_master_detail_view()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Master Detail Pesanan',
            'harga_awal' => 20000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 20000,
            'detail_negosiasi' => 'Nego detail',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 20000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        $response = $this->actingAs($this->mahasiswa)->get(route('pesanan.index', ['pesanan' => $pesanan->id_pesanan]));
        $response->assertStatus(200);
        $response->assertSee('Master Detail Pesanan');
    }

    /** 26. Test Mahasiswa Can View Provider Detail Page */
    public function test_mahasiswa_can_view_provider_detail_page()
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('catalog.provider', $this->provider->id_provider));
        $response->assertStatus(200);
        $response->assertSee('rizky_antar');
        $response->assertSee('Antar Jemput Motor');
    }

    /** 27. Test Request Layanan Index Redirects to Pesanan */
    public function test_request_layanan_index_redirects_to_pesanan()
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.request.index'));
        $response->assertRedirect(route('pesanan.index'));
    }

    /** 28. Test Request Layanan Create Form View */
    public function test_request_layanan_create_form_view()
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.request.create'));
        $response->assertStatus(200);
    }

    /** 29. Test Request Layanan Edit Form and Block Processed */
    public function test_request_layanan_edit_form_and_block_processed()
    {
        $reqOpen = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Request Open',
            'harga_awal' => 10000,
            'status_request' => 'open',
        ]);

        $responseEdit = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.request.edit', $reqOpen->id_request));
        $responseEdit->assertStatus(200);

        $reqProcessed = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Request Processed',
            'harga_awal' => 10000,
            'status_request' => 'diproses',
        ]);

        $responseEditProcessed = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.request.edit', $reqProcessed->id_request));
        $responseEditProcessed->assertSessionHas('error');
    }

    /** 30. Test Request Layanan Cancel Blocked When Processed */
    public function test_request_layanan_cancel_blocked_when_processed()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Titip Makan',
            'detail_kebutuhan' => 'Nasi Goreng Kampus',
            'harga_awal' => 15000,
            'status_request' => 'diproses',
        ]);

        $response = $this->actingAs($this->mahasiswa)->delete(route('mahasiswa.request.destroy', $req->id_request));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('request_layanan', ['id_request' => $req->id_request, 'status_request' => 'diproses']);
    }

    /** 31. Test Request Layanan Ownership 403 for Other User */
    public function test_request_layanan_ownership_403_for_other_user()
    {
        $otherMhs = User::create([
            'name' => 'User Lain',
            'username' => 'user_lain',
            'email' => 'userlain@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Bimbingan Kalkulus',
            'harga_awal' => 50000,
            'status_request' => 'open',
        ]);

        $this->actingAs($otherMhs)->get(route('mahasiswa.request.show', $req->id_request))->assertStatus(403);
        $this->actingAs($otherMhs)->put(route('mahasiswa.request.update', $req->id_request), [
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Bimbingan Kalkulus Hack',
        ])->assertStatus(403);
        $this->actingAs($otherMhs)->delete(route('mahasiswa.request.destroy', $req->id_request))->assertStatus(403);
    }

    /** 32. Test Negosiasi Store Blocked for Non Owner Request */
    public function test_negosiasi_store_blocked_for_non_owner_request()
    {
        $otherMhs = User::create([
            'name' => 'User Lain Nego',
            'username' => 'user_lain_nego',
            'email' => 'userlainnego@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Bimbingan Coding',
            'harga_awal' => 40000,
            'status_request' => 'open',
        ]);

        $response = $this->actingAs($otherMhs)->post(route('negosiasi.store', [$req->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => 35000,
            'detail_negosiasi' => 'Hack nego',
        ]);

        $response->assertStatus(403);
    }

    /** 33. Test Negosiasi Store Blocked for Already Processed Request */
    public function test_negosiasi_store_blocked_for_already_processed_request()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Titip Beli',
            'detail_kebutuhan' => 'Titip Beli Alat Tulis',
            'harga_awal' => 10000,
            'status_request' => 'diproses',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('negosiasi.store', [$req->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => 10000,
            'detail_negosiasi' => 'Double store attempt',
        ]);

        $response->assertStatus(400);
    }

    /** 34. Test Mahasiswa Counter Offer Creates New Thread Entry */
    public function test_mahasiswa_counter_offer_creates_new_thread_entry()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Logo',
            'harga_awal' => 50000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 50000,
            'detail_negosiasi' => 'Awal nego',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'pending',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('negosiasi.counter', $nego->id_negosiasi), [
            'harga_tawaran' => 45000,
            'detail_negosiasi' => 'Nawar 45rb aja kak',
        ]);

        $response->assertRedirect(route('negosiasi.show', $nego->id_negosiasi));
        $this->assertDatabaseHas('negosiasi', [
            'id_request' => $req->id_request,
            'harga_tawaran' => 45000,
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);
    }

    /** 35. Test Negosiasi Counter Blocked If Already Agreed */
    public function test_negosiasi_counter_blocked_if_already_agreed()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Print Tugas',
            'harga_awal' => 10000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 10000,
            'detail_negosiasi' => 'Deal awal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('negosiasi.counter', $nego->id_negosiasi), [
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Nawar setelah deal',
        ]);

        $response->assertStatus(400);
    }

    /** 36. Test Negosiasi Reject Resets Request to Open */
    public function test_negosiasi_reject_resets_request_to_open()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Titip Makan',
            'detail_kebutuhan' => 'Makan Siang Nasi Padang',
            'harga_awal' => 20000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 20000,
            'detail_negosiasi' => 'Nego Padang',
            'dibuat_oleh' => 'provider',
            'status_negosiasi' => 'pending',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('negosiasi.reject', $nego->id_negosiasi));
        $response->assertRedirect(route('mahasiswa.request.show', $req->id_request));
        $this->assertDatabaseHas('negosiasi', ['id_negosiasi' => $nego->id_negosiasi, 'status_negosiasi' => 'ditolak']);
        $this->assertDatabaseHas('request_layanan', ['id_request' => $req->id_request, 'status_request' => 'open']);
    }

    /** 37. Test Negosiasi Accept Blocked If Already Agreed */
    public function test_negosiasi_accept_blocked_if_already_agreed()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Stasiun',
            'harga_awal' => 15000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 15000,
            'detail_negosiasi' => 'Agreed nego',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('negosiasi.accept', $nego->id_negosiasi));
        $response->assertStatus(400);
    }

    /** 38. Test Mahasiswa Submit Detail Pekerjaan */
    public function test_mahasiswa_submit_detail_pekerjaan()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Kartu Nama',
            'harga_awal' => 30000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 30000,
            'detail_negosiasi' => 'Deal kartu nama',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 30000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('detailPekerjaan.store', $pesanan->id_pesanan), [
            'instruksi_pengerjaan' => 'Tolong gunakan tema warna biru dongker dan emas.',
            'format_hasil' => 'PDF & AI Vector',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('detail_pekerjaan', ['id_pesanan' => $pesanan->id_pesanan, 'instruksi_pengerjaan' => 'Tolong gunakan tema warna biru dongker dan emas.']);
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => $pesanan->id_pesanan, 'status_pesanan' => 'dikerjakan']);
    }

    /** 39. Test Payment Blocked for Non Owner */
    public function test_payment_blocked_for_non_owner()
    {
        $otherMhs = User::create([
            'name' => 'Pembayar Lain',
            'username' => 'pembayar_lain',
            'email' => 'pembayarlain@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Unikom',
            'harga_awal' => 8000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        $response = $this->actingAs($otherMhs)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
        ]);

        $response->assertStatus(403);
    }

    /** 40. Test Mahasiswa Review Index Shows History and Stats */
    public function test_mahasiswa_review_index_shows_history_and_stats()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Riwayat Test',
            'harga_awal' => 8000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
            'total_bayar' => 8000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'rate' => 5,
            'review' => 'Mantap sekali',
        ]);

        $responseSemua = $this->actingAs($this->mahasiswa)->get(route('review.index'));
        $responseSemua->assertRedirect(route('pesanan.index', ['status' => 'selesai']));

        $responsePesanan = $this->actingAs($this->mahasiswa)->get(route('pesanan.index', ['status' => 'selesai']));
        $responsePesanan->assertStatus(200);
        $responsePesanan->assertSee('Mantap sekali');

        $responseSelesai = $this->actingAs($this->mahasiswa)->get(route('review.index', ['status' => 'selesai']));
        $responseSelesai->assertRedirect(route('pesanan.index', ['status' => 'selesai']));

        $responseBatal = $this->actingAs($this->mahasiswa)->get(route('review.index', ['status' => 'dibatalkan']));
        $responseBatal->assertRedirect(route('pesanan.index', ['status' => 'dibatalkan']));
    }

    /** 41. Test Review Blocked for Unfinished Order */
    public function test_review_blocked_for_unfinished_order()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Print Brosur',
            'harga_awal' => 15000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 15000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 15000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'dikerjakan',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Premature review attempt',
        ]);

        $response->assertStatus(400);
    }

    /** 42. Test Review Blocked for Non Owner */
    public function test_review_blocked_for_non_owner()
    {
        $otherMhs = User::create([
            'name' => 'Reviewer Lain',
            'username' => 'reviewer_lain',
            'email' => 'reviewerlain@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput',
            'harga_awal' => 8000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
            'total_bayar' => 8000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        $response = $this->actingAs($otherMhs)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Hack review',
        ]);

        $response->assertStatus(403);
    }

    /** 43. Test Review Update Modifies Existing Review */
    public function test_review_update_modifies_existing_review()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Edit Photo',
            'harga_awal' => 25000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 25000,
            'detail_negosiasi' => 'Deal photo',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 25000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
            'total_bayar' => 25000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        RatingReview::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'rate' => 4,
            'review' => 'Bagus tapi agak lama',
        ]);

        $response = $this->actingAs($this->mahasiswa)->patch(route('review.update', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Revisi ulasan: Hasilnya keren banget!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('rating_review', [
            'id_pesanan' => $pesanan->id_pesanan,
            'rate' => 5,
            'review' => 'Revisi ulasan: Hasilnya keren banget!',
        ]);
    }

    /** 44. Test Provider Dashboard Shows Statistics and Active Order */
    public function test_provider_dashboard_shows_statistics_and_active_order()
    {
        $response = $this->actingAs($this->providerUser)->get(route('provider.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Penyedia Jasa');
    }

    /** 45. Test Provider My Service Index View */
    public function test_provider_my_service_index_view()
    {
        $response = $this->actingAs($this->providerUser)->get(route('my-service'));
        $response->assertStatus(200);
        $response->assertSee('Antar Jemput Motor');
    }

    /** 46. Test Provider Order Index Shows All Negotiations */
    public function test_provider_order_index_shows_all_negotiations()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Provider Order Index Test',
            'harga_awal' => 8000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Order index nego',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $response = $this->actingAs($this->providerUser)->get(route('order'));
        $response->assertStatus(200);
        $response->assertSee('Provider Order Index Test');
    }

    /** 47. Test Provider Send Message in Order Chat */
    public function test_provider_send_message_in_order_chat()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Print Tugas Akhir',
            'harga_awal' => 20000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 20000,
            'detail_negosiasi' => 'Pesan awal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $response = $this->actingAs($this->providerUser)->post(route('order.chat', $nego->id_negosiasi), [
            'pesan' => 'Halo kak, berkas sudah siap di-print?',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('negosiasi', [
            'id_request' => $req->id_request,
            'detail_negosiasi' => 'Halo kak, berkas sudah siap di-print?',
            'dibuat_oleh' => 'provider',
        ]);
    }

    /** 48. Test Provider Accept and Reject Negotiation */
    public function test_provider_accept_and_reject_negotiation()
    {
        // 1. Accept Flow
        $req1 = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Jemput Ke Stasiun',
            'harga_awal' => 12000,
            'status_request' => 'diproses',
        ]);

        $nego1 = Negosiasi::create([
            'id_request' => $req1->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 12000,
            'detail_negosiasi' => 'Accept nego',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $responseAccept = $this->actingAs($this->providerUser)->post(route('order.accept', $nego1->id_negosiasi));
        $responseAccept->assertRedirect(route('order', ['active' => $nego1->id_negosiasi]));
        $this->assertDatabaseHas('negosiasi', ['id_negosiasi' => $nego1->id_negosiasi, 'status_negosiasi' => 'disepakati']);
        $this->assertDatabaseHas('pesanan', ['id_negosiasi' => $nego1->id_negosiasi]);

        // 2. Reject Flow
        $req2 = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Titip Makan',
            'detail_kebutuhan' => 'Titip Makan Terlalu Jauh',
            'harga_awal' => 5000,
            'status_request' => 'diproses',
        ]);

        $nego2 = Negosiasi::create([
            'id_request' => $req2->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 5000,
            'detail_negosiasi' => 'Reject nego',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $responseReject = $this->actingAs($this->providerUser)->post(route('order.reject', $nego2->id_negosiasi));
        $responseReject->assertSessionHas('success');
        $this->assertDatabaseHas('negosiasi', ['id_negosiasi' => $nego2->id_negosiasi, 'status_negosiasi' => 'ditolak']);
    }

    /** 49. Test User Can Delete Own Account */
    public function test_user_can_delete_own_account()
    {
        $tempUser = User::create([
            'name' => 'Delete Me',
            'username' => 'delete_me',
            'email' => 'deleteme@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($tempUser)->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('user', ['id_user' => $tempUser->id_user]);
    }

    /** 50. Test Direct Order Rejects Invalid Layanan ID */
    public function test_direct_order_rejects_invalid_layanan_id()
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('catalog.direct-order'), [
            'id_layanan' => 999999,
            'catatan' => 'Invalid ID test',
        ]);

        $response->assertSessionHasErrors(['id_layanan']);
    }

    /** 51. Test Full Lifecycle: Request -> Negosiasi -> Pesanan -> Detail Pekerjaan -> Pembayaran -> Delivery -> Review */
    public function test_full_lifecycle_request_to_review()
    {
        // Step 1: Mahasiswa creates custom RequestLayanan
        $responseReq = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.request.store'), [
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Bimbingan Tugas Akhir RPL Diagram UML',
            'kriteria_output' => 'Diagram Use Case, Class, Sequence in Draw.io',
            'harga_awal' => 75000,
            'deadline' => now()->addDays(7)->format('Y-m-d'),
        ]);
        $req = RequestLayanan::where('detail_kebutuhan', 'Bimbingan Tugas Akhir RPL Diagram UML')->first();
        $this->assertNotNull($req);

        // Step 2: Mahasiswa initiates negotiation with provider
        $responseNegoInit = $this->actingAs($this->mahasiswa)->post(route('negosiasi.store', [$req->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => 75000,
            'detail_negosiasi' => 'Halo mas Rizky, bisa bantu bimbingan UML?',
        ]);
        $nego = Negosiasi::where('id_request', $req->id_request)->first();
        $this->assertNotNull($nego);

        // Step 3: Provider counters offer
        $this->actingAs($this->providerUser)->post(route('order.counter', $nego->id_negosiasi), [
            'harga_tawaran' => 80000,
            'pesan' => 'Siap mas, harga 80rb termasuk revisi 2x ya.',
        ]);
        $negoTerbaru = Negosiasi::where('id_request', $req->id_request)->latest('created_at')->first();
        $this->assertEquals(80000, $negoTerbaru->harga_tawaran);

        // Step 4: Mahasiswa accepts provider counter offer
        $this->actingAs($this->mahasiswa)->post(route('negosiasi.accept', $negoTerbaru->id_negosiasi));
        $pesanan = Pesanan::where('id_negosiasi', $negoTerbaru->id_negosiasi)->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals(80000, $pesanan->harga_final);

        // Step 5: Mahasiswa submits Detail Pekerjaan
        $this->actingAs($this->mahasiswa)->post(route('detailPekerjaan.store', $pesanan->id_pesanan), [
            'instruksi_pengerjaan' => 'Topik sistem informasi perpustakaan berbasis web.',
            'format_hasil' => 'File .drawio dan .pdf',
        ]);
        $this->assertDatabaseHas('detail_pekerjaan', ['id_pesanan' => $pesanan->id_pesanan]);

        // Step 6: Mahasiswa pays via QRIS
        $this->actingAs($this->mahasiswa)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
        ]);
        $this->assertDatabaseHas('pembayaran', ['id_pesanan' => $pesanan->id_pesanan, 'status_bayar' => 'dikonfirmasi']);

        // Step 7: Provider completes order & uploads deliverable
        $this->actingAs($this->providerUser)->post(route('order.progress', $negoTerbaru->id_negosiasi), [
            'status_pesanan' => 'selesai',
            'pesan_progress' => 'Bimbingan selesai, berkas diagram terlampir.',
            'dokumen' => 'https://drive.google.com/file/d/uml-diagram-unlocked/view',
        ]);
        $pesanan->refresh();
        $this->assertEquals('selesai', $pesanan->status_pesanan);

        // Step 8: Mahasiswa submits review and 5-star rating
        $this->actingAs($this->mahasiswa)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Sangat membantu dan penjelasannya mudah dipahami!',
        ]);

        $this->assertDatabaseHas('rating_review', [
            'id_pesanan' => $pesanan->id_pesanan,
            'rate' => 5,
        ]);

        // Step 9: Verify Struk Payment is generated
        $responseStruk = $this->actingAs($this->mahasiswa)->get(route('pesanan.struk', $pesanan->id_pesanan));
        $responseStruk->assertStatus(200);
        $responseStruk->assertSee('80.000');
    }

    /** 52. Test Micro UI Status Badges & Currency Formatting Render */
    public function test_micro_ui_status_badges_and_currency_formatting()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Format Currency & Badge Test',
            'harga_awal' => 12500,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 12500,
            'detail_negosiasi' => 'Currency test',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 12500,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'menunggu_pengerjaan',
        ]);

        $response = $this->actingAs($this->mahasiswa)->get(route('pesanan.show', $pesanan->id_pesanan));
        $response->assertStatus(200);
        $response->assertSee('12.500');
    }

    /** 53. Test Micro Request Deadline Rejects Past Date */
    public function test_micro_request_deadline_rejects_past_date()
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.request.store'), [
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Request Past Deadline',
            'harga_awal' => 20000,
            'deadline' => now()->subDays(2)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['deadline']);
    }

    /** 54. Test Micro Catalog Empty Search Returns All Providers */
    public function test_micro_catalog_empty_search_returns_all_providers()
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('catalog.index', ['search' => '']));
        $response->assertStatus(200);
        $response->assertSee('rizky_antar');
    }

    /** 55. Test Micro Provider Dashboard Statistics Calculation Accuracy */
    public function test_micro_provider_dashboard_statistics_calculation_accuracy()
    {
        // Add completed pesanan for provider
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Income Calc Test',
            'harga_awal' => 45000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 45000,
            'detail_negosiasi' => 'Income deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 45000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        $response = $this->actingAs($this->providerUser)->get(route('provider.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('45.000');
    }

    /** 56. Test Micro Review Rating Average Calculation Precision */
    public function test_micro_review_rating_average_calculation_precision()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Rating Precision Test',
            'harga_awal' => 10000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 10000,
            'detail_negosiasi' => 'Rating deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 10000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay / Transfer Bank',
            'total_bayar' => 10000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        // Submit 4-star review
        $this->actingAs($this->mahasiswa)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 4,
            'review' => 'Bagus sekali 4 bintang',
        ]);

        $this->provider->refresh();
        $this->assertEquals(4.0, (float) $this->provider->rating);
    }

    /** 57. Test E2E Mahasiswa Pesanan Index Review Submission Flow */
    public function test_e2e_mahasiswa_pesanan_index_review_submission_flow()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Print Skripsi Halaman Full',
            'harga_awal' => 35000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 35000,
            'detail_negosiasi' => 'Deal print',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 35000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay',
            'total_bayar' => 35000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        $responseIndex = $this->actingAs($this->mahasiswa)->get(route('pesanan.index', ['pesanan' => $pesanan->id_pesanan]));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Beri penilaian');

        $responseSubmit = $this->actingAs($this->mahasiswa)->post(route('review.store', $pesanan->id_pesanan), [
            'rate' => 5,
            'review' => 'Hasil print rapi dan cepat dari pesanan index!',
        ]);

        $responseSubmit->assertSessionHas('success');
        $this->assertDatabaseHas('rating_review', [
            'id_pesanan' => $pesanan->id_pesanan,
            'rate' => 5,
            'review' => 'Hasil print rapi dan cepat dari pesanan index!',
        ]);
    }

    /** 58. Test E2E Mahasiswa Pesanan Show Review Submission Flow */
    public function test_e2e_mahasiswa_pesanan_show_review_submission_flow()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Titip Makan',
            'detail_kebutuhan' => 'Titip Makan Kantin Unikom',
            'harga_awal' => 18000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 18000,
            'detail_negosiasi' => 'Deal titip makan',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 18000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode_pembayaran' => 'QRIS Unikom Pay',
            'total_bayar' => 18000,
            'status_bayar' => 'dikonfirmasi',
        ]);

        $responseShow = $this->actingAs($this->mahasiswa)->get(route('pesanan.show', $pesanan->id_pesanan));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Berikan Bintang');
    }

    /** 59. Test E2E Provider Order Chat Multiple Messages Thread */
    public function test_e2e_provider_order_chat_multiple_messages_thread()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Bimbingan Python Data Science',
            'harga_awal' => 60000,
            'status_request' => 'diproses',
        ]);

        $nego1 = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 60000,
            'detail_negosiasi' => 'Halo mas, apakah bisa tgl 10?',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'pending',
        ]);

        $this->actingAs($this->providerUser)->post(route('order.chat', $nego1->id_negosiasi), [
            'pesan' => 'Bisa mas, jam 2 siang di perpustakaan.',
        ]);

        $this->assertDatabaseHas('negosiasi', [
            'id_request' => $req->id_request,
            'detail_negosiasi' => 'Bisa mas, jam 2 siang di perpustakaan.',
            'dibuat_oleh' => 'provider',
        ]);
    }

    /** 60. Test E2E Tracking Pesanan Multiple Progress Updates */
    public function test_e2e_tracking_pesanan_multiple_progress_updates()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Desain & Editing',
            'detail_kebutuhan' => 'Desain Feeds Instagram',
            'harga_awal' => 40000,
            'status_request' => 'diproses',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 40000,
            'detail_negosiasi' => 'Deal feeds',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 40000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'dikerjakan',
        ]);

        // First update: 50%
        $this->actingAs($this->providerUser)->post(route('order.progress', $nego->id_negosiasi), [
            'status_pesanan' => 'dikerjakan',
            'pesan_progress' => 'Draft awal feeds 3 slide sudah jadi.',
            'dokumen' => 'https://drive.google.com/file/d/draft1/view',
        ]);

        // Second update: 100% Selesai
        $this->actingAs($this->providerUser)->post(route('order.progress', $nego->id_negosiasi), [
            'status_pesanan' => 'selesai',
            'pesan_progress' => 'Final 6 slide feeds selesai.',
            'dokumen' => 'https://drive.google.com/file/d/final/view',
        ]);

        $this->assertEquals(2, TrackingPesanan::where('id_pesanan', $pesanan->id_pesanan)->count());
    }

    /** 61. Test E2E Search Filters With No Matching Results */
    public function test_e2e_search_filters_with_no_matching_results()
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('catalog.index', ['search' => 'NonExistentServiceXYZ999']));
        $response->assertStatus(200);
        $response->assertSee('Belum Ada Mitra Tersedia');
    }

    /** 62. Test E2E Category Filters for All Six Categories */
    public function test_e2e_category_filters_for_all_six_categories()
    {
        $kategoriList = ['Antar Jemput', 'Print & Fotokopi', 'Bimbingan', 'Desain & Editing', 'Titip Makan', 'Titip Beli'];

        foreach ($kategoriList as $kat) {
            $response = $this->actingAs($this->mahasiswa)->get(route('catalog.index', ['kategori' => $kat]));
            $response->assertStatus(200);
            $response->assertSee($kat);
        }
    }

    /** 63. Test E2E Registration Validation Duplicate Email and Username */
    public function test_e2e_registration_validation_duplicate_email_and_username()
    {
        $response = $this->post('/register', [
            'name' => 'Duplicate User Attempt',
            'username' => 'raka_mhs', // duplicate username
            'email' => 'raka.pratama@student.ac.id', // duplicate email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
    }

    /** 64. Test E2E Profile Update Validation Duplicate Email */
    public function test_e2e_profile_update_validation_duplicate_email()
    {
        User::create([
            'name' => 'Existing User',
            'username' => 'existing_usr',
            'email' => 'existing@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($this->mahasiswa)->patch(route('profile.update'), [
            'name' => 'Raka Pratama',
            'username' => 'raka_mhs',
            'email' => 'existing@student.ac.id', // duplicate
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** 65. Test E2E Request Layanan Kriteria Output Optional Field */
    public function test_e2e_request_layanan_kriteria_output_optional_field()
    {
        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.request.store'), [
            'kategori' => 'Titip Beli',
            'detail_kebutuhan' => 'Beli Buku Tulis Sidu 1 Pack',
            'harga_awal' => 25000,
            'deadline' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $req = RequestLayanan::where('detail_kebutuhan', 'Beli Buku Tulis Sidu 1 Pack')->first();
        $this->assertNotNull($req);
        $this->assertNull($req->kriteria_output);
    }

    /** 66. Test E2E Negosiasi Price Validation Negative Number */
    public function test_e2e_negosiasi_price_validation_negative_number()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Antar Negative Price Test',
            'harga_awal' => 8000,
            'status_request' => 'open',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('negosiasi.store', [$req->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => -5000,
            'detail_negosiasi' => 'Nego harga negatif',
        ]);

        $response->assertSessionHasErrors(['harga_tawaran']);
    }

    /** 67. Test E2E Provider Service Store Validation Missing Fields */
    public function test_e2e_provider_service_store_validation_missing_fields()
    {
        $response = $this->actingAs($this->providerUser)->post(route('provider.services.store'), [
            'nama_layanan' => '',
            'kategori' => '',
            'harga' => 'bukan_angka',
        ]);

        $response->assertSessionHasErrors(['nama_layanan', 'kategori', 'harga']);
    }

    /** 68. Test E2E Review Update Blocked If User Not Owner */
    public function test_e2e_review_update_blocked_if_user_not_owner()
    {
        $otherMhs = User::create([
            'name' => 'Reviewer Bajakan',
            'username' => 'reviewer_bajakan',
            'email' => 'bajakan@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Review Ownership Test',
            'harga_awal' => 8000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        $response = $this->actingAs($otherMhs)->patch(route('review.update', $pesanan->id_pesanan), [
            'rate' => 1,
            'review' => 'Hack review update',
        ]);

        $response->assertStatus(403);
    }

    /** 69. Test E2E Struk Access Blocked For Unpaid Or Non Owner */
    public function test_e2e_struk_access_blocked_for_unpaid_or_non_owner()
    {
        $otherMhs = User::create([
            'name' => 'Struk Access Hacker',
            'username' => 'struk_hacker',
            'email' => 'strukhacker@student.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Struk 403 Test',
            'harga_awal' => 8000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 8000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 8000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        $response = $this->actingAs($otherMhs)->get(route('pesanan.struk', $pesanan->id_pesanan));
        $response->assertStatus(403);
    }

    /** 70. Test E2E Provider Dashboard Revenue Period Calculation */
    public function test_e2e_provider_dashboard_revenue_period_calculation()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Bimbingan',
            'detail_kebutuhan' => 'Bimbingan Revenue Calc',
            'harga_awal' => 100000,
            'status_request' => 'selesai',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 100000,
            'detail_negosiasi' => 'Deal bimbingan',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 100000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'selesai',
        ]);

        $response = $this->actingAs($this->providerUser)->get(route('provider.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('100.000');
    }

    /** 71. Test E2E Complete Multi Order Parallel Negotiations */
    public function test_e2e_complete_multi_order_parallel_negotiations()
    {
        $req1 = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Parallel Nego 1',
            'harga_awal' => 10000,
            'status_request' => 'open',
        ]);

        $req2 = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Print & Fotokopi',
            'detail_kebutuhan' => 'Parallel Nego 2',
            'harga_awal' => 15000,
            'status_request' => 'open',
        ]);

        $this->actingAs($this->mahasiswa)->post(route('negosiasi.store', [$req1->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => 10000,
            'detail_negosiasi' => 'Nego 1',
        ]);

        $this->actingAs($this->mahasiswa)->post(route('negosiasi.store', [$req2->id_request, $this->provider->id_provider]), [
            'harga_tawaran' => 15000,
            'detail_negosiasi' => 'Nego 2',
        ]);

        $this->assertDatabaseHas('negosiasi', ['id_request' => $req1->id_request]);
        $this->assertDatabaseHas('negosiasi', ['id_request' => $req2->id_request]);
    }

    /** 72. Test E2E Guest Access Attempt To All Controller Endpoints */
    public function test_e2e_guest_access_attempt_to_all_controller_endpoints()
    {
        $this->get(route('pesanan.index'))->assertRedirect('/login');
        $this->get(route('catalog.index'))->assertRedirect('/login');
        $this->get(route('mahasiswa.request.create'))->assertRedirect('/login');
        $this->get(route('provider.dashboard'))->assertRedirect('/login');
        $this->get(route('my-service'))->assertRedirect('/login');
        $this->get(route('order'))->assertRedirect('/login');
        $this->get(route('provider.review'))->assertRedirect('/login');
        $this->get(route('review.index'))->assertRedirect('/login');
        $this->get(route('profile.edit'))->assertRedirect('/login');
    }

    /** 73. Test E2E Provider Earnings Withdrawal */
    public function test_e2e_provider_earnings_withdrawal()
    {
        $response = $this->actingAs($this->providerUser)->post(route('provider.withdraw'), [
            'metode' => 'GoPay',
            'no_rekening' => '081234567890',
            'jumlah' => 50000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /** 74. Test E2E Provider Emergency Cancellation */
    public function test_e2e_provider_emergency_cancellation()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Antar Jemput',
            'detail_kebutuhan' => 'Jemput Stasiun Emergency Test',
            'harga_awal' => 15000,
            'status_request' => 'open',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 15000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 15000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'dikerjakan',
        ]);

        $response = $this->actingAs($this->providerUser)->post(route('order.cancel', $nego->id_negosiasi));
        $response->assertRedirect();
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pesanan' => 'dibatalkan',
        ]);
    }

    /** 75. Test E2E QRIS Visual Modal Payment Store */
    public function test_e2e_qris_visual_modal_payment_store()
    {
        $req = RequestLayanan::create([
            'id_user' => $this->mahasiswa->id_user,
            'kategori' => 'Titip Makan',
            'detail_kebutuhan' => 'Titip Makan Nasi Goreng QRIS Test',
            'harga_awal' => 20000,
            'status_request' => 'open',
        ]);

        $nego = Negosiasi::create([
            'id_request' => $req->id_request,
            'id_provider' => $this->provider->id_provider,
            'harga_tawaran' => 20000,
            'detail_negosiasi' => 'Deal',
            'dibuat_oleh' => 'mahasiswa',
            'status_negosiasi' => 'disepakati',
        ]);

        $pesanan = Pesanan::create([
            'id_negosiasi' => $nego->id_negosiasi,
            'harga_final' => 20000,
            'tanggal_pesanan' => now(),
            'status_pesanan' => 'dikerjakan',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('pembayaran.store', $pesanan->id_pesanan), [
            'metode_pembayaran' => 'QRIS Unikom Pay',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status_bayar' => 'dikonfirmasi',
        ]);
    }
}

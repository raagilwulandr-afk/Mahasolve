<x-guest-layout>
    <div class="space-y-2 text-center mb-6">
        <h1 class="text-xl font-bold font-display text-slate-900">Selamat Datang Kembali</h1>
        <p class="text-xs text-slate-500">Masuk dengan akun mahasiswa atau penyedia jasa kamu.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-xs" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Mahasiswa / Provider</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                   placeholder="nama@student.unikom.ac.id"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] font-semibold text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs text-slate-600">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/20 transition transform active:scale-95">
            Masuk Sekarang →
        </button>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Belum memiliki akun Mahasolve?
            <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline ml-1">Daftar Akun Baru</a>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <div class="space-y-2 text-center mb-6">
        <h1 class="text-xl font-bold font-display text-slate-900">Lupa Password?</h1>
        <p class="text-xs text-slate-500 leading-relaxed">
            Masukkan alamat email kamu di bawah. Kami akan mengirimkan tautan untuk mengatur ulang password kamu.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-xs" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Terdaftar</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                   placeholder="nama@student.unikom.ac.id"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/20 transition transform active:scale-95">
            Kirim Tautan Reset Password ✉️
        </button>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Ingat password kamu?
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline ml-1">Kembali ke Login</a>
        </div>
    </form>
</x-guest-layout>

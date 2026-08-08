<x-guest-layout>
    <div class="space-y-2 text-center mb-6">
        <h1 class="text-xl font-bold font-display text-slate-900">Buat Akun Mahasolve</h1>
        <p class="text-xs text-slate-500">Bergabunglah dengan ekosistem mobilitas & akademik Unikom.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ selectedRole: 'mahasiswa' }" class="space-y-4">
        @csrf

        <!-- ROLE SELECTOR PILL -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-2">Pilih Peran Akun Kamu</label>
            <input type="hidden" name="role" x-model="selectedRole">
            <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl border border-slate-200/80">
                <button type="button" @click="selectedRole = 'mahasiswa'"
                        :class="selectedRole === 'mahasiswa' ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800'"
                        class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                    Mahasiswa
                </button>
                <button type="button" @click="selectedRole = 'provider'"
                        :class="selectedRole === 'provider' ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-800'"
                        class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                    Penyedia Jasa
                </button>
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5 text-center" x-text="selectedRole === 'mahasiswa' ? 'Daftar untuk memesan layanan antar jemput, bimbingan, atau print.' : 'Daftar sebagai mitra provider untuk menawarkan jasa kamu ke mahasiswa Unikom.'"></p>
        </div>

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                   placeholder="Contoh: Raka Pratama"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Username (Optional / Default) -->
        <div>
            <label for="username" class="block text-xs font-semibold text-slate-700 mb-1">Username Unik</label>
            <input id="username" type="text" name="username" :value="old('username')"
                   placeholder="Contoh: raka_mhs"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Resmi Mahasiswa Unikom</label>
            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                   placeholder="nama@mahasiswa.unikom.ac.id"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <p class="text-[10px] text-indigo-600 mt-1 font-medium">Harus menggunakan email domain @mahasiswa.unikom.ac.id</p>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="Minimal 8 karakter"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="Ketik ulang password"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-rose-500" />
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/20 transition transform active:scale-95">
            Daftar Sekarang
        </button>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline ml-1">Masuk Sekarang</a>
        </div>
    </form>
</x-guest-layout>

<section>
    <header class="mb-4">
        <h3 class="text-base font-bold text-slate-900">
            Pembaruan Password Keamanan
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">
            Pastikan akun kamu menggunakan kombinasi password yang kuat dan aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 mb-1">Password Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-xs text-rose-500" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-semibold text-slate-700 mb-1">Password Baru</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-xs text-rose-500" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-xs text-rose-500" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition">
                Perbarui Password
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Password berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>

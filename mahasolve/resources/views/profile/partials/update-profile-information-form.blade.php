<section>
    <header class="mb-4">
        <h3 class="text-base font-bold text-slate-900">
            Informasi Profil Pengguna
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">
            Perbarui informasi nama, username, nomor WhatsApp/telepon, dan alamat email kamu.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error class="mt-1 text-xs text-rose-500" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="username" class="block text-xs font-semibold text-slate-700 mb-1">Username Unik</label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error class="mt-1 text-xs text-rose-500" :messages="$errors->get('username')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email (Unikom / Active)</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error class="mt-1 text-xs text-rose-500" :messages="$errors->get('email')" />
        </div>

        <div>
            <label for="no_hp" class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
            <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $user->no_hp) }}" placeholder="081234567890"
                   class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
            <x-input-error class="mt-1 text-xs text-rose-500" :messages="$errors->get('no_hp')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Profil berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>

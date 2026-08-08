<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['nullable', 'string', 'in:mahasiswa,provider'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = $request->role ?? 'mahasiswa';
        $username = $request->username ?: (explode('@', $request->email)[0] ?? strtolower(str_replace(' ', '', $request->name)));

        $user = User::create([
            'username' => $username,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        // Jika mendaftar sebagai provider, otomatis buat entri tabel provider
        if ($role === 'provider') {
            Provider::create([
                'id_user' => $user->id_user,
                'rating' => 0.0,
                'detail_provider' => 'Penyedia Jasa Mahasolve Baru',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

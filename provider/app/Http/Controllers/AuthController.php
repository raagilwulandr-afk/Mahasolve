<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user()->role);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('user', 'email'),
            ],
            'password' => [
                'required',
                'min:8',
                'confirmed',
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],
            'role' => [
                'required',
                Rule::in(['mahasiswa', 'provider']),
            ],
            'nama_provider' => [
                'nullable',
                'required_if:role,provider',
                'string',
                'max:100',
            ],
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'no_hp' => $validated['no_hp'] ?? null,
                'role' => $validated['role'],
            ]);

            if ($validated['role'] === 'provider') {

                Provider::create([
                    'id_user' => $user->id_user,
                    'rating' => 0,
                    'detail_provider' => '',
                ]);
            }

            DB::commit();

            Auth::login($user);

            $request->session()->regenerate();

            return $this->redirectByRole($user->role)
                ->with('success', 'Registrasi berhasil.');
        } catch (Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Registrasi gagal.',
                ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'provider' => redirect()->route('provider.dashboard'),
            'mahasiswa' => redirect()->route('dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}

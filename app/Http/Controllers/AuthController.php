<?php

namespace App\Http\Controllers;

use App\Helpers\LogAktivitasHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.daftar');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
       $validated = $request->validated();

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Model User sudah cast 'hashed'
            'role' => User::ROLE_MASYARAKAT,
        ]);

        // Catat log aktivitas registrasi
        LogAktivitasHelper::catat($user->id_user, 'Registrasi akun baru sebagai masyarakat');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        // Catat log aktivitas login berhasil
        $user = Auth::user();
        LogAktivitasHelper::catat($user->id_user, 'Login ke dalam sistem');

        return redirect()->intended(route('dashboard'));
    }

    public function dashboard(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin');
        }

        if ($user->isPemantau()) {
            return redirect()->route('peta');
        }

        return redirect()->route('lapor');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            // Catat log aktivitas logout
            LogAktivitasHelper::catat($user->id_user, 'Logout dari sistem');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function ensureIsNotRateLimited(LoginRequest $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        back()->withErrors([
            'email' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam ' . $seconds . ' detik.',
        ])->throwResponse();
    }

    protected function throttleKey(LoginRequest $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}
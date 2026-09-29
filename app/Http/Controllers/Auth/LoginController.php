<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek login via email atau username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authData = [
            $loginField => $request->login,
            'password'  => $request->password,
        ];

        // Jalankan login
        if (Auth::attempt($authData, $request->filled('remember'))) {
            $user = Auth::user();

            // Cek keaktifan akun
            if (!$user->isActive()) {
                Auth::logout();
                return back()
                    ->withInput($request->only('login', 'remember'))
                    ->withErrors(['login' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.']);
            }

            // Update waktu login terakhir
            $user->update([
                'last_login_at' => now()
            ]);

            $request->session()->regenerate();

            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
            }

            if ($user->hasRole('procurement')) {
                return redirect()->route('procurement.dashboard')
                    ->with('success', 'Selamat datang di Procurement Command Hub, ' . $user->name . '!');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors(['login' => 'Username/Email atau Password salah.']);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}

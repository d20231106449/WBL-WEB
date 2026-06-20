<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AdminAuthController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (session()->has('profile.id')) {
            return redirect()->route(session('profile.role') === 'admin' ? 'admin.dashboard' : 'user.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request, SupabaseService $supabase): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $auth = $supabase->signIn($credentials['email'], $credentials['password']);
            $profile = $supabase->profile($auth['access_token'], $auth['user']['id']);

            if (! $profile) {
                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'Akaun Auth dijumpai tetapi profil pengguna belum tersedia.',
                ]);
            }

            $request->session()->regenerate();
            session([
                'supabase_token' => $auth['access_token'],
                'supabase_refresh_token' => $auth['refresh_token'] ?? null,
                'profile' => $profile,
                'admin' => ($profile['role'] ?? 'user') === 'admin' ? $profile : null,
            ]);

            $destination = ($profile['role'] ?? 'user') === 'admin' ? 'admin.dashboard' : 'user.dashboard';
            return redirect()->route($destination)->with('success', 'Selamat kembali, '.($profile['full_name'] ?? 'Pengguna').'!');
        } catch (Throwable $e) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Emel atau kata laluan tidak betul, atau sambungan Supabase gagal.',
            ]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah log keluar.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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
            'account_type' => ['required', 'in:user,admin'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $auth = $supabase->signIn($credentials['email'], $credentials['password']);
            $profile = $supabase->profile($auth['access_token'], $auth['user']['id']);

            if (! $profile) {
                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'Akaun pengesahan ditemukan, tetapi profil pengguna belum tersedia.',
                ]);
            }

            if (($profile['role'] ?? 'user') !== $credentials['account_type']) {
                $message = $credentials['account_type'] === 'admin'
                    ? 'Akaun ini bukan akaun pentadbir.'
                    : 'Akaun pentadbir perlu log masuk melalui pilihan Pentadbir.';

                return back()->withInput($request->only('email', 'account_type'))->withErrors(['account_type' => $message]);
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
                'email' => $this->loginErrorMessage($e),
            ]);
        }
    }

    public function clientSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'account_type' => ['required', 'in:user,admin'],
            'access_token' => ['required', 'string'],
            'refresh_token' => ['nullable', 'string'],
            'user.id' => ['required', 'string'],
            'profile.id' => ['required', 'string'],
            'profile.role' => ['required', 'in:user,admin'],
            'profile.email' => ['nullable', 'email'],
            'profile.full_name' => ['nullable', 'string'],
            'profile.phone_number' => ['nullable', 'string'],
            'profile.matric_no' => ['nullable', 'string'],
            'profile.matric_number' => ['nullable', 'string'],
        ]);

        if (($data['profile']['id'] ?? null) !== ($data['user']['id'] ?? null)) {
            return response()->json(['message' => 'Profil pengguna tidak sepadan dengan akaun Supabase.'], 422);
        }

        if (($data['profile']['role'] ?? 'user') !== $data['account_type']) {
            $message = $data['account_type'] === 'admin'
                ? 'Akaun ini bukan akaun pentadbir.'
                : 'Akaun pentadbir perlu log masuk melalui pilihan Pentadbir.';

            return response()->json(['message' => $message], 422);
        }

        $request->session()->regenerate();
        session([
            'supabase_token' => $data['access_token'],
            'supabase_refresh_token' => $data['refresh_token'] ?? null,
            'profile' => $data['profile'],
            'admin' => $data['profile']['role'] === 'admin' ? $data['profile'] : null,
        ]);

        return response()->json([
            'redirect' => route($data['profile']['role'] === 'admin' ? 'admin.dashboard' : 'user.dashboard', absolute: false),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah log keluar.');
    }

    public function registerForm(): View|RedirectResponse
    {
        if (session()->has('profile.id')) {
            return redirect()->route(session('profile.role') === 'admin' ? 'admin.dashboard' : 'user.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request, SupabaseService $supabase): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone_number' => ['required', 'string', 'max:30'],
            'matric_no' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        try {
            $supabase->signUp($data['full_name'], $data['email'], $data['password'], $data['phone_number'], $data['matric_no']);

            return redirect()->route('login')->with(
                'success',
                'Pendaftaran berjaya. Sila sahkan e-mel anda jika menerima e-mel pengesahan, kemudian log masuk sebagai pelajar.',
            );
        } catch (Throwable $e) {
            return back()->withInput($request->only('full_name', 'phone_number', 'matric_no', 'email'))->withErrors([
                'email' => $this->registrationErrorMessage($e),
            ]);
        }
    }

    public function forgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordReset(Request $request, SupabaseService $supabase): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        try {
            $supabase->sendPasswordReset($data['email'], route('password.reset'));

            return back()->with('success', 'Pautan pemulihan kata laluan telah dihantar ke e-mel anda.');
        } catch (Throwable $e) {
            return back()->withInput()->withErrors(['email' => $e->getMessage()]);
        }
    }

    public function resetPasswordForm(): View
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request, SupabaseService $supabase): RedirectResponse
    {
        $data = $request->validate([
            'access_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], ['access_token.required' => 'Pautan pemulihan tidak sah atau telah tamat tempoh.']);

        try {
            $supabase->updatePassword($data['access_token'], $data['password']);

            return redirect()->route('login')->with('password_reset_success', 'Kata laluan berjaya ditukar. Sila log masuk menggunakan kata laluan baharu.');
        } catch (Throwable $e) {
            return back()->withErrors(['password' => 'Pautan pemulihan tidak sah atau telah tamat tempoh. Sila minta pautan baharu.']);
        }
    }

    private function loginErrorMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();
        $normalized = strtolower($message);

        if (str_contains($normalized, 'invalid login credentials')) {
            return 'E-mel atau kata laluan tidak betul.';
        }

        if (str_contains($normalized, 'email not confirmed')) {
            return 'Alamat e-mel belum disahkan. Semak peti masuk anda sebelum log masuk.';
        }

        if (str_contains($normalized, 'supabase') || str_contains($normalized, 'supabase_url')) {
            return $message;
        }

        return 'Tidak dapat menyambung ke Supabase. Sila cuba lagi dan semak log aplikasi.';
    }

    private function registrationErrorMessage(Throwable $exception): string
    {
        $normalized = strtolower($exception->getMessage());

        if (str_contains($normalized, 'already registered') || str_contains($normalized, 'already exists')) {
            return 'Alamat e-mel ini telah didaftarkan.';
        }

        if (str_contains($normalized, 'password')) {
            return 'Kata laluan tidak memenuhi syarat keselamatan Supabase.';
        }

        return 'Pendaftaran tidak dapat diselesaikan. Sila cuba lagi.';
    }
}

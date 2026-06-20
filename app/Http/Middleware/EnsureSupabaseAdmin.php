<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupabaseAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('supabase_token') || session('profile.role') !== 'admin') {
            if (session()->has('supabase_token')) {
                return redirect()->route('user.dashboard')->withErrors(['action' => 'Halaman ini hanya untuk pentadbir.']);
            }

            session()->forget(['supabase_token', 'supabase_refresh_token', 'profile', 'admin']);

            return redirect()->route('login')->withErrors(['email' => 'Sila log masuk sebagai pentadbir.']);
        }

        return $next($request);
    }
}

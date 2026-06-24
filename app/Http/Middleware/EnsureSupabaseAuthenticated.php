<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupabaseAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('supabase_token') || ! session()->has('profile.id')) {
            session()->forget(['supabase_token', 'supabase_refresh_token', 'profile', 'admin']);

            return redirect()->route('login')->withErrors(['email' => 'Sila log masuk untuk meneruskan.']);
        }

        return $next($request);
    }
}

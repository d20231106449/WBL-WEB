<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRedirect($request)) {
            return redirect()->to('https://'.$request->getHost().$request->getRequestUri(), 301);
        }

        return $next($request);
    }

    private function shouldRedirect(Request $request): bool
    {
        if ($request->secure()) {
            return false;
        }

        return app()->environment('production')
            || str_ends_with($request->getHost(), '.onrender.com');
    }
}

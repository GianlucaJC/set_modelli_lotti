<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaticUserIsLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('set_modelli_user')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
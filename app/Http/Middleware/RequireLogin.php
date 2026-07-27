<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('user_id')) {
            if ($request->is('licitastore') || $request->is('licitastore/*')) {
                return redirect()->route('ecommerce.login');
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}


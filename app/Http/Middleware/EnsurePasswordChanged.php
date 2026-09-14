<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            if ($request->routeIs('password.change', 'password.change.post', 'logout')) {
                return $next($request);
            }

            return redirect()->route('password.change')
                ->with('warning', 'Anda wajib mengganti password sebelum melanjutkan.');
        }

        return $next($request);
    }
}

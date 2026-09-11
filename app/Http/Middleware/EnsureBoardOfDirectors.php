<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureBoardOfDirectors
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->user()?->hasRole('BOARD_OF_DIRECTORS')) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

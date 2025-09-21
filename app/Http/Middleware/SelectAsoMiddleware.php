<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SelectAsoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (
            ! auth()->check()
            || $request->is('select-aso')
            || session()->has('rol_activo')
        ) {
            return $next($request);
        }

        return redirect('/select-aso');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SelectAsoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Si no está autenticado, o ya estamos en la página de selección,
        // o ya existe rol_activo en sesión, dejamos pasar.
        if (
            ! auth()->check()
            || $request->is('select-aso')
            || session()->has('rol_activo')
        ) {
            return $next($request);
        }

        // Redirigimos por URL en lugar de por nombre de ruta.
        return redirect('/select-aso');
    }
}

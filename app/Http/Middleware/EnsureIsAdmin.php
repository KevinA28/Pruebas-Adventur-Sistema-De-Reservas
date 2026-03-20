<?php
// =====================================================================
// ARCHIVO: EnsureIsAdmin.php
// UBICACIÓN: app/Http/Middleware/EnsureIsAdmin.php
// =====================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
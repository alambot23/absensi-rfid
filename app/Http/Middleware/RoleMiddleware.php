<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user belum login, atau role-nya tidak sama dengan yang diminta di rute
        if (!$request->user() || $request->user()->role !== $role) {
            // Tendang dengan error 403 (Forbidden)
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
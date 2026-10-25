<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika user belum login, arahkan ke halaman login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Cek apakah role user termasuk ke dalam daftar role yang diizinkan
        // Menggunakan method helper dari model User (isAdmin, isPemantau, isMasyarakat) atau properti role
        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }
            if ($role === 'pemantau' && $user->isPemantau()) {
                return $next($request);
            }
            if ($role === 'masyarakat' && $user->isMasyarakat()) {
                return $next($request);
            }
            // Fallback jika mencocokkan string role langsung
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Jika tidak punya hak akses, abort dengan error 403 (Forbidden)
        abort(403, 'Unauthorized action. Anda tidak memiliki hak akses ke halaman ini.');
    }
}
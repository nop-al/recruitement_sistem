<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();
        $userRole = $user->role?->name;

        // Administrator has unrestricted access across all dashboards
        if ($userRole === 'admin') {
            return $next($request);
        }

        if (! in_array($userRole, $roles, true)) {
            $targetRoute = match ($userRole) {
                'hr' => 'hr.dashboard',
                'interviewer' => 'interview.dashboard',
                'kandidat' => 'kadidat.dashboard',
                default => 'login',
            };

            return redirect()->route($targetRoute)
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman tersebut.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        $user = $request->user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'This account has been deactivated.',
            ]);
        }

        if ($user->tenant && !$user->tenant->is_active && !$user->isSuperAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'This shop is currently inactive.',
            ]);
        }

        if (!$user->role) {
            abort(403, 'User has no assigned role.');
        }

        foreach ($roles as $role) {
            if ($user->role->slug === $role) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}

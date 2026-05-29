<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('login');
        }

        if (!$request->user()->role) {
            abort(403, 'User has no assigned role.');
        }

        foreach ($roles as $role) {
            if ($request->user()->role->slug === $role) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}

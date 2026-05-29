<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeature
{
    public function handle(Request $request, Closure $next, ...$features): Response
    {
        $tenant = $request->user()?->tenant;

        if (!$tenant) {
            abort(403, 'No shop context is available for this feature.');
        }

        foreach ($features as $feature) {
            if (!$tenant->hasFeature($feature)) {
                abort(403, 'This feature is not enabled for this shop.');
            }
        }

        return $next($request);
    }
}

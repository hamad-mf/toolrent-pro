<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantScope
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        // 1. Try to find tenant by subdomain
        if ($subdomain !== 'localhost' && $subdomain !== '127' && $subdomain !== 'www' && $subdomain !== '192') {
            $tenant = Tenant::where('slug', $subdomain)->first();
            
            if ($tenant) {
                $this->setTenantSession($tenant);
                return $next($request);
            }
        }

        // 2. Fallback: If no subdomain (or main site), but user is logged in and has a tenant_id
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenant = Auth::user()->tenant;
            if ($tenant) {
                $this->setTenantSession($tenant);
                return $next($request);
            }
        }

        // 3. No tenant context (Super Admin or Landing Page)
        // Only clear if we are NOT on a tenant subdomain
        session()->forget(['tenant_id', 'tenant_name', 'tenant_primary_color']);

        return $next($request);
    }

    protected function setTenantSession(Tenant $tenant): void
    {
        session()->put('tenant_id', $tenant->id);
        session()->put('tenant_name', $tenant->name);
        session()->put('tenant_primary_color', $tenant->primary_color);
    }
}

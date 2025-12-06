<?php

namespace App\Http\Middleware;

use App\Subdomain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class RedirectKeeperDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Subdomain::organization() !== null) {
            return redirect(Config::string('app.url').'/dashboard');
        }

        return $next($request);
    }
}

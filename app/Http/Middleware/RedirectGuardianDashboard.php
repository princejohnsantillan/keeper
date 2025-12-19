<?php

namespace App\Http\Middleware;

use App\Subdomain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class RedirectGuardianDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $organization = Subdomain::organization();

        if (Subdomain::defined() && $organization === null) {
            abort(404);
        }

        if ($organization !== null) {
            return redirect(Config::string('app.url').'/guardian');
        }

        return $next($request);
    }
}

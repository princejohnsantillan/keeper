<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\OrganizationRouting;
use App\Facades\Subdomain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guardians always use the root domain: requests to the guardian panel on an organization
 * subdomain are redirected there, regardless of the configured organization routing.
 */
final class RedirectGuardianDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Subdomain::onRootDomain($request)) {
            return $next($request);
        }

        if (Subdomain::routing() === OrganizationRouting::Subdomain && Subdomain::organization() === null) {
            abort(404);
        }

        return redirect(Config::string('app.url').'/'.$request->path());
    }
}

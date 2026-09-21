<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\OrganizationRouting;
use App\Facades\Subdomain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the organization from the request (subdomain or path segment, depending on
 * config "organization.routing") and aborts with 404 when none matches.
 *
 * The organization is resolved from the request passed to the middleware rather than the
 * global request so that Livewire's persistent middleware (which replays panel middleware
 * against a copy of the original page request) can identify the organization on updates.
 */
final class RequireOrganizationSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $previous = Subdomain::organization();
        $organization = Subdomain::resolve($request);

        if ($organization === null) {
            abort(404);
        }

        // A Livewire batch may replay this middleware for several page paths, and Livewire only
        // replays once per distinct path. Refuse to serve components belonging to different
        // organizations in one request, so no component can run under another org's context.
        if ($previous !== null && ! $previous->is($organization)) {
            abort(404);
        }

        // Drop the slug from the route parameters so controllers and Livewire pages receive
        // exactly the same arguments as in subdomain mode.
        if (Subdomain::routing() === OrganizationRouting::Path) {
            $request->route()?->forgetParameter('organization');
        }

        return $next($request);
    }
}

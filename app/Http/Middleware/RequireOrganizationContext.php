<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class RequireOrganizationContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = Str::beforeLast($request->host(), '.'.Config::string('app.domain'));

        $organization = Organization::query()->where('slug', $slug)->first();

        if ($organization === null) {
            abort(404);
        }

        Context::addHidden('organization', $organization);

        return $next($request);
    }
}

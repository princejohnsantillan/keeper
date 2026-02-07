<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\GetCurrentKeeperAction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureKeeperAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('filament.keeper.auth.login');
        }

        app(GetCurrentKeeperAction::class)->__invoke();

        return $next($request);
    }
}

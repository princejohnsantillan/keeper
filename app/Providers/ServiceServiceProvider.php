<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Binds service interfaces to their implementations.
 *
 * @see .ai/guidelines/actions-and-services.md
 */
final class ServiceServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // Example:
        // \App\Services\Contracts\ChildServiceInterface::class => \App\Services\ChildService::class,
    ];
}

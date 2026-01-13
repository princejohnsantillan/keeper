# Actions and Services

## Purpose

Actions and services provide a framework-agnostic business logic layer that:

- Centralizes testable business logic outside of controllers, Livewire components, and Filament actions
- Enables swapping UI implementations (e.g., Filament → Inertia, Livewire → API) without rewriting business logic
- Maintains a single source of truth for how domain operations are performed

## Actions

### Definition

An action is a single-responsibility, invokable class that performs one specific operation.

### Rules

- Must be an invokable class with a single public `__invoke()` method
- Must not contain UI logic (no Filament, Livewire, or HTTP-specific code)
- Each action must have a corresponding feature test

### Location & Naming

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Action | `{Verb}{Noun}Action` | `CreateChildAction` | `app/Actions/` |
| Action Test | `{Verb}{Noun}ActionTest` | `CreateChildActionTest` | `tests/Feature/Actions/` |

### Structure

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;

final class CreateChildAction
{
    public function __invoke(array $data): Child
    {
        return Child::query()->create($data);
    }
}
```

### Actions with Dependencies

Actions can have constructor dependencies injected by Laravel's container:

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Services\Contracts\NotificationServiceInterface;

final class CreateChildAction
{
    public function __construct(
        private NotificationServiceInterface $notificationService,
    ) {}

    public function __invoke(array $data): Child
    {
        $child = Child::query()->create($data);

        $this->notificationService->notifyGuardian($child);

        return $child;
    }
}
```

## Services

### Definition

A service is a library of related, co-located methods that operate on a specific domain. Services group multiple related operations that don't warrant individual action classes.

### Rules

- Must have a corresponding interface in `app/Services/Contracts/`
- Interface must be bound to implementation in `ServiceServiceProvider`
- Each public method must have a corresponding feature test
- Must not contain UI logic

### Location & Naming

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Service | `{Domain}Service` | `ChildService` | `app/Services/` |
| Interface | `{Domain}ServiceInterface` | `ChildServiceInterface` | `app/Services/Contracts/` |
| Service Test | `{Domain}ServiceTest` | `ChildServiceTest` | `tests/Feature/Services/` |

### Structure

**Interface:**

```php
<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Child;
use App\Models\Guardian;

interface ChildServiceInterface
{
    public function create(array $data): Child;

    public function attachGuardian(Child $child, Guardian $guardian, string $relationship): void;

    public function detachGuardian(Child $child, Guardian $guardian): void;
}
```

**Implementation:**

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use App\Services\Contracts\ChildServiceInterface;

final class ChildService implements ChildServiceInterface
{
    public function create(array $data): Child
    {
        return Child::query()->create($data);
    }

    public function attachGuardian(Child $child, Guardian $guardian, string $relationship): void
    {
        Relationship::query()->create([
            'child_id' => $child->id,
            'guardian_id' => $guardian->id,
            'relationship' => $relationship,
        ]);
    }

    public function detachGuardian(Child $child, Guardian $guardian): void
    {
        Relationship::query()
            ->where('child_id', $child->id)
            ->where('guardian_id', $guardian->id)
            ->delete();
    }
}
```

### Binding Services

Register interface-to-implementation bindings in `app/Providers/ServiceServiceProvider.php`:

```php
<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ChildService;
use App\Services\Contracts\ChildServiceInterface;
use Illuminate\Support\ServiceProvider;

final class ServiceServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ChildServiceInterface::class => ChildService::class,
    ];
}
```

## Testing Requirements

| Type | Requirement | Location |
|------|-------------|----------|
| Action | Each action must have a test | `tests/Feature/Actions/` |
| Service | Each public method must have a test | `tests/Feature/Services/` |

### Testing Actions

```php
<?php

declare(strict_types=1);

use App\Actions\CreateChildAction;
use App\Models\Child;

it('creates a child', function () {
    $action = app(CreateChildAction::class);

    $child = $action([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    expect($child)
        ->toBeInstanceOf(Child::class)
        ->first_name->toBe('John')
        ->last_name->toBe('Doe');

    $this->assertDatabaseHas(Child::class, [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});
```

### Testing Services

```php
<?php

declare(strict_types=1);

use App\Models\Child;
use App\Models\Guardian;
use App\Services\Contracts\ChildServiceInterface;

it('attaches a guardian to a child', function () {
    $service = app(ChildServiceInterface::class);
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $service->attachGuardian($child, $guardian, 'parent');

    $this->assertDatabaseHas('relationships', [
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'relationship' => 'parent',
    ]);
});
```

### Mocking Services

Because services have interfaces, they can be easily mocked in tests:

```php
<?php

declare(strict_types=1);

use App\Services\Contracts\NotificationServiceInterface;

it('sends notification when child is created', function () {
    $mock = $this->mock(NotificationServiceInterface::class);
    $mock->shouldReceive('notifyGuardian')->once();

    // Test code that triggers notification...
});
```

## Filament Integration

Filament UI actions in `app/Filament/Actions/` must delegate business logic to action classes or services. No direct model manipulation should occur in Filament actions.

### Why?

- Filament actions are UI components, not business logic
- Business logic in Filament actions cannot be reused in APIs or other UI frameworks
- Testing Filament actions requires browser/Livewire testing; action classes can be unit tested

### Dependency Injection in Closures

Laravel's container automatically resolves action classes passed as closure parameters:

```php
->using(fn (array $data, CreateChildAction $action): Child => $action($data))
```

### Before (avoid)

```php
<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\CreateAction;

final class CreateChildAction
{
    public static function make(?string $name = null, string $label = 'Add child'): CreateAction
    {
        return CreateAction::make($name)
            ->label($label)
            ->using(function (array $data): Child {
                // ❌ Business logic directly in Filament action
                $relationship = $data['relationship'];
                unset($data['relationship']);

                $guardian = AuthUser::guardian();
                $child = Child::query()->create($data);

                Relationship::create([
                    'guardian_id' => $guardian->id,
                    'child_id' => $child->id,
                    'relationship' => $relationship,
                    'is_primary' => true,
                ]);

                return $child;
            });
    }
}
```

### After (preferred)

```php
<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\CreateChildAction as CreateChildBusinessAction;
use App\Models\Child;
use Filament\Actions\CreateAction;

final class CreateChildAction
{
    public static function make(?string $name = null, string $label = 'Add child'): CreateAction
    {
        return CreateAction::make($name)
            ->label($label)
            // ✅ Delegate to business logic action
            ->using(fn (array $data, CreateChildBusinessAction $action): Child => $action($data));
    }
}
```

## When to Use Actions vs Services

| Use Case | Recommendation |
|----------|----------------|
| Single, focused operation | Action |
| Multiple related operations on a domain | Service |
| Operation used in one place | Action |
| Operations shared across many features | Service |
| Simple CRUD with no side effects | Either (prefer Action for single ops) |
| Complex orchestration of multiple steps | Action (can inject services) |

## Migration Strategy

- **New features:** Use actions/services from the start
- **Existing code:** Refactor incrementally when touching related code
- **Priority:** Focus on code with business logic in Filament actions, controllers, or Livewire components

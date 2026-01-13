# Actions and Services

## Purpose
Framework-agnostic business logic layer that:
- Centralizes testable logic outside controllers, Livewire, and Filament
- Enables UI swapping (Filament → Inertia, Livewire → API) without rewriting logic
- Maintains single source of truth for domain operations

---

## Actions

**Definition:** Single-responsibility invokable class performing one specific operation.

### Rules
- Must have single public `__invoke()` method
- Must NOT contain UI logic (no Filament/Livewire/HTTP code)
- Each action requires a corresponding feature test

### Location

| Type | Pattern | Location |
|------|---------|----------|
| Action | `{Verb}{Noun}Action` | `app/Actions/` |
| Test | `{Verb}{Noun}ActionTest` | `tests/Feature/Actions/` |

### Basic Structure
```php
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

### With Dependencies
```php
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

---

## Services

**Definition:** Library of related methods operating on a specific domain, grouping operations that don't warrant individual action classes.

### Rules
- Must have interface in `app/Services/Contracts/`
- Interface bound to implementation in `ServiceServiceProvider`
- Each public method requires a feature test
- Must NOT contain UI logic

### Location

| Type | Pattern | Location |
|------|---------|----------|
| Service | `{Domain}Service` | `app/Services/` |
| Interface | `{Domain}ServiceInterface` | `app/Services/Contracts/` |
| Test | `{Domain}ServiceTest` | `tests/Feature/Services/` |

### Interface
```php
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

### Implementation
```php
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

### Binding in ServiceServiceProvider
```php
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

---

## Testing

### Action Test
```php
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

### Service Test
```php
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
```php
use App\Services\Contracts\NotificationServiceInterface;

it('sends notification when child is created', function () {
    $mock = $this->mock(NotificationServiceInterface::class);
    $mock->shouldReceive('notifyGuardian')->once();
    // Test code that triggers notification...
});
```

---

## Filament Integration

Filament UI actions (`app/Filament/Actions/`) must delegate to business logic actions/services. No direct model manipulation in Filament actions.

**Why:**
- Filament actions are UI components, not business logic
- Logic in Filament actions cannot be reused in APIs/other frameworks
- Action classes enable unit testing; Filament requires browser/Livewire testing

### Dependency Injection in Closures
Laravel auto-resolves actions in closure parameters:
```php
->using(fn (array $data, CreateChildAction $action): Child => $action($data))
```

### Avoid (business logic in Filament)
```php
// app/Filament/Actions/CreateChildAction.php
->using(function (array $data): Child {
    $relationship = $data['relationship'];
    unset($data['relationship']);
    $guardian = AuthUser::guardian();
    $child = Child::query()->create($data);
    Relationship::create([...]);
    return $child;
});
```

### Preferred (delegate to action)
```php
// app/Filament/Actions/CreateChildAction.php
->using(fn (array $data, CreateChildBusinessAction $action): Child => $action($data));
```

---

## Action vs Service Decision

| Scenario | Use |
|----------|-----|
| Single focused operation | Action |
| Multiple related domain operations | Service |
| Used in one place | Action |
| Shared across many features | Service |
| Simple CRUD, no side effects | Action |
| Complex multi-step orchestration | Action (inject services) |

---

## Migration Strategy
- **New features:** Use actions/services from start
- **Existing code:** Refactor incrementally when touching related code
- **Priority:** Extract logic from Filament actions, controllers, Livewire components

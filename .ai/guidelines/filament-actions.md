# Filament Actions

## Purpose
Standardize Filament action usage to:
- Centralize all action components in a single location
- Avoid reserved parameter name conflicts in Filament callbacks
- Enable reuse of action configurations across panels and resources

---

## Location

All Filament action classes must be placed in `app/Filament/Actions/`.

This applies to all action types:
- Table actions (row actions, bulk actions, header actions)
- Page actions (header actions)
- Form actions
- Infolist actions
- Any other Filament action components

---

## Structure

```php
declare(strict_types=1);

namespace App\Filament\Actions;

use Filament\Actions\Action;

final class DoSomethingAction
{
    public static function make(?string $name = null, string $label = 'Do Something'): Action
    {
        return Action::make($name)->label($label)
            // configuration...
    }
}
```

---

## Variable Naming for Actions

### Reserved Parameter Names
In Filament closures and callbacks, `$action` is a reserved parameter name that Filament automatically injects. To avoid conflicts and confusion, do NOT use `$action` as a variable name when instantiating action classes.

### Convention
When assigning a Filament action to a variable, name the variable using the full class name in camelCase:

| Class | Variable Name |
|-------|---------------|
| `CreateChildAction` | `$createChildAction` |
| `AttendActivityAction` | `$attendActivityAction` |
| `DeleteRecordAction` | `$deleteRecordAction` |

### Correct
```php
$createChildAction = CreateChildAction::make();
$attendActivityAction = AttendActivityAction::make();
```

### Incorrect
```php
// DON'T DO THIS - 'action' is reserved in Filament callbacks
$action = CreateChildAction::make();
```

### Why This Matters
Filament automatically injects certain parameters into closures:
```php
->action(function ($action, $record, $data) {
    // $action is auto-injected by Filament
})
```

Using `$action` for your own variables can cause:
- Naming conflicts in closures
- Confusion about which action is being referenced
- Unexpected behavior when Filament tries to inject its own `$action`

---

## Inline Usage

When actions are used inline (not assigned to a variable), call `make()` directly:

```php
return $table
    ->headerActions([
        CreateChildAction::make(),
    ])
    ->recordActions([
        AttendActivityAction::make(),
        EditAction::make(),
    ]);
```

---

## Business Logic Delegation

Filament actions must delegate business logic to action classes in `app/Actions/` or services. See the [Actions and Services guidelines](./actions-and-services.md) for details.

### Example
```php
// app/Filament/Actions/CreateChildAction.php
->using(fn (array $data, CreateChildBusinessAction $action): Child => $action($data))
```

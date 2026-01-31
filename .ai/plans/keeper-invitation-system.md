# Keeper Invitation System Implementation Plan

## Overview

Add the ability for organization admins to invite Keepers via email. Invited users must accept the invitation (set password) before being associated with the organization.

**Security Model:** Users are only associated with the organization (Keeper record created) **after** they accept the invitation. Pending invitations are tracked separately.

**Roles:**
- `Admin` - Full access within the organization
- `Gatekeeper` - Limited access (placeholder for future permissions)

---

## 1. Database Changes

### 1.1 Create `KeeperRole` Enum
**File:** `app/Enums/KeeperRole.php`

```php
enum KeeperRole: string implements HasColor, HasIcon, HasLabel
{
    case Admin = 'admin';
    case Gatekeeper = 'gatekeeper';
}
```

### 1.2 Migration: Add `role` to `keepers` table
```php
$table->string('role')->default('gatekeeper')->after('user_id');
```

### 1.3 Migration: Make `password` nullable on `users` table
```php
$table->string('password')->nullable()->change();
```

### 1.4 Migration: Create `keeper_invitations` table
```php
Schema::create('keeper_invitations', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
    $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
    $table->foreignUlid('invited_by')->constrained('users')->cascadeOnDelete();
    $table->string('role');
    $table->string('token')->unique();
    $table->timestamp('accepted_at')->nullable();
    $table->timestamp('expires_at');
    $table->timestamps();

    $table->unique(['organization_id', 'user_id']);
});
```

### 1.5 Create `KeeperInvitation` Model
**File:** `app/Models/KeeperInvitation.php`
- Relationships: `organization()`, `user()`, `invitedBy()`
- Scopes: `pending()`, `expired()`, `valid()`
- Methods: `isExpired()`, `isAccepted()`, `isPending()`, `accept()`

---

## 2. Model Changes

### 2.1 `app/Models/Keeper.php`
- Add `role` cast to `KeeperRole::class`
- Add `isAdmin(): bool` helper method
- Add `isGatekeeper(): bool` helper method

### 2.2 `app/Models/User.php`
- Add `needsPasswordSetup(): bool` method (returns `$this->password === null`)
- Update `canAccessPanel()` to also check `!$this->needsPasswordSetup()`

### 2.3 `database/factories/KeeperFactory.php`
- Add `role` to definition (default: `KeeperRole::Gatekeeper`)
- Add `admin()` state
- Add `gatekeeper()` state

### 2.4 `database/factories/UserFactory.php`
- Add `invited()` state (password: null, email_verified_at: null)

---

## 3. Business Logic

### 3.1 `app/Services/Contracts/KeeperInvitationServiceInterface.php`
```php
interface KeeperInvitationServiceInterface
{
    public function findOrCreateInvitedUser(string $email, string $name): User;
    public function hasPendingInvitation(User $user, Organization $organization): bool;
    public function isKeeperForOrganization(User $user, Organization $organization): bool;
    public function createInvitation(User $user, Organization $organization, User $invitedBy, KeeperRole $role): KeeperInvitation;
    public function sendInvitationEmail(KeeperInvitation $invitation): void;
    public function findValidInvitation(string $token): ?KeeperInvitation;
    public function acceptInvitation(KeeperInvitation $invitation): Keeper;
}
```

### 3.2 `app/Services/KeeperInvitationService.php`
- `findOrCreateInvitedUser()`: Creates user with null password if not exists
- `createInvitation()`: Creates KeeperInvitation with unique token, sets expiry (e.g., 7 days)
- `sendInvitationEmail()`: Queues `KeeperInvitationMail` mailable
- `findValidInvitation()`: Finds invitation by token, checks not expired/accepted
- `acceptInvitation()`: Creates Keeper record, marks invitation as accepted

### 3.3 `app/Actions/InviteKeeperAction.php`
- Orchestrates: find/create user → check not already keeper → check no pending invite → create invitation → send email
- Throws `KeeperAlreadyExistsException` if user is already a keeper
- Throws `InvitationAlreadyExistsException` if pending invitation exists

### 3.4 `app/Actions/AcceptKeeperInvitationAction.php`
- Validates token → checks not expired → creates Keeper record → marks accepted
- Called after user sets their password
- Throws `InvalidInvitationException` if token invalid/expired

### 3.5 Exceptions
- `app/Exceptions/KeeperAlreadyExistsException.php`
- `app/Exceptions/InvitationAlreadyExistsException.php`
- `app/Exceptions/InvalidInvitationException.php`

### 3.6 `app/Providers/ServiceServiceProvider.php`
- Add binding: `KeeperInvitationServiceInterface::class => KeeperInvitationService::class`

---

## 4. Email

### 4.1 `app/Mail/KeeperInvitationMail.php`
- Queued mailable following `GatepassCreated` pattern
- Contains: organization name, inviter name, role, acceptance URL with token
- URL format: `https://{org-slug}.domain.test/admin/invitation/accept?token={token}`

### 4.2 `resources/views/mail/keeper-invitation.blade.php`
- Markdown mail template with "Accept Invitation" button
- Shows organization name, role being granted, who invited them
- Mentions they'll need to set a password if new user

---

## 5. Invitation Acceptance Flow

### 5.1 `app/Filament/Panels/Keeper/Pages/AcceptInvitation.php`
New Filament page for accepting invitations:
- Route: `/admin/invitation/accept?token={token}`
- Validates token on load → shows error if invalid/expired
- Shows: organization name, role, inviter name
- Form: password, password_confirmation (only if user has no password)
- On submit:
  1. Set password (if needed)
  2. Mark email as verified
  3. Call `AcceptKeeperInvitationAction` → creates Keeper record
  4. Log user in
  5. Redirect to dashboard

### 5.2 `app/Filament/Panels/Keeper/Pages/Login.php`
- Add helpful message for users who try to log in before accepting invitation
- Check if user has pending invitation and show appropriate message

---

## 6. Filament UI

### 6.1 `app/Filament/Actions/InviteKeeperAction.php`
- Modal form with: name, email, role (select)
- Delegates to `InviteKeeperAction` business action
- Shows success/error notifications

### 6.2 New Resource: `app/Filament/Panels/Keeper/Resources/Keepers/`
```
Keepers/
├── KeeperResource.php
├── Pages/
│   └── ListKeepers.php
├── Schemas/
│   └── KeeperForm.php
└── Tables/
    └── KeepersTable.php
```
- Lists keepers for current organization
- Header action: "Invite Keeper" using the Filament action (Admin only)
- Columns: user name, email, role, created date
- Allow editing role of existing keepers (Admin only)
- Authorization: Use `GetCurrentKeeperAction` to check `isAdmin()` for sensitive actions

### 6.3 New Resource: `app/Filament/Panels/Keeper/Resources/KeeperInvitations/`
```
KeeperInvitations/
├── KeeperInvitationResource.php
├── Pages/
│   └── ListKeeperInvitations.php
└── Tables/
    └── KeeperInvitationsTable.php
```
- Lists pending invitations for current organization (Admin only)
- Columns: invitee email/name, role, invited by, expires at, status
- Actions: Resend invitation, Cancel/Delete invitation

### 6.4 `app/Filament/Notifications/AppNotification.php`
- Add `keeperInvited(string $email)` method
- Add `keeperAlreadyExists(string $email)` method
- Add `invitationAlreadyPending(string $email)` method
- Add `invitationAccepted()` method

---

## 7. Critical Files

| File | Purpose |
|------|---------|
| `app/Enums/KeeperRole.php` | Role enum with labels/colors/icons |
| `app/Models/Keeper.php` | Add role cast and helpers |
| `app/Models/KeeperInvitation.php` | Track pending invitations |
| `app/Models/User.php` | Add `needsPasswordSetup()`, update `canAccessPanel()` |
| `app/Services/KeeperInvitationService.php` | Core invitation logic |
| `app/Actions/InviteKeeperAction.php` | Send invitation action |
| `app/Actions/AcceptKeeperInvitationAction.php` | Accept invitation action |
| `app/Mail/KeeperInvitationMail.php` | Invitation email |
| `app/Filament/Panels/Keeper/Pages/AcceptInvitation.php` | Invitation acceptance page |
| `app/Filament/Panels/Keeper/Pages/Login.php` | Handle pending invitation message |
| `app/Filament/Panels/Keeper/Resources/Keepers/KeeperResource.php` | Keeper management UI |
| `app/Filament/Panels/Keeper/Resources/KeeperInvitations/KeeperInvitationResource.php` | Pending invitations UI |
| Organization creation action (find existing) | Auto-create owner as Admin keeper |

---

## 8. Additional Requirements

### 8.1 Authorization
- Only **Admin** keepers can invite new keepers
- Gatekeepers cannot access the invite functionality

### 8.2 Existing Users
- When inviting someone who already has a user account, send the invite email anyway
- Email will contain login link (not password setup) if they already have a password

### 8.3 Organization Owner
- When an organization is created, the owner is automatically added as an **Admin** keeper
- This requires updating the organization creation flow (likely in `CreateOrganizationAction` or similar)

---

## 9. Verification

### Run Tests
```bash
php artisan test --filter=KeeperInvitationService
php artisan test --filter=InviteKeeperAction
php artisan test --filter=AcceptKeeperInvitationAction
php artisan test --filter=KeeperInvitationMail
php artisan test --filter=AcceptInvitation
```

### Manual Testing
1. Create a new organization (verify owner becomes Admin keeper automatically)
2. Log in as the Admin keeper
3. Navigate to Keepers resource → click "Invite Keeper" → fill form
4. Check email is received (use Mailpit/Mailtrap)
5. **Before accepting:** Verify user is NOT in Keepers list (only in pending invitations)
6. Click "Accept Invitation" link in email
7. Set password on acceptance page
8. Verify redirect to dashboard and user is now logged in
9. Verify user now appears in Keepers list with correct role
10. As Gatekeeper, verify "Invite Keeper" action is not visible

### Edge Cases to Test
- Invite existing user with password → acceptance page skips password form
- Invite user already a keeper for this org → error message
- Invite user who is keeper for different org → should succeed
- Try to log in before accepting invitation → helpful message shown
- Use expired invitation token → error page
- Use already-accepted invitation token → error page
- Resend invitation → generates new token, old token invalid
- Cancel pending invitation → token invalidated

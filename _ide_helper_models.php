<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $location
 * @property string $starts_at
 * @property string $ends_at
 * @property string|null $notes
 * @property int $organization_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\Attendance|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Child> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \App\Models\Organization $organization
 * @method static \Database\Factories\ActivityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperActivity {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $service_id
 * @property int $child_id
 * @property int|null $checkin_keeper_id
 * @property int|null $checkin_gatepass_id
 * @property string|null $checked_in_at
 * @property int|null $checkout_keeper_id
 * @property int|null $checkout_gatepass_id
 * @property string|null $checked_out_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity|null $activity
 * @property-read \App\Models\Gatepass|null $checkinGatepass
 * @property-read \App\Models\Keeper|null $checkinKeeper
 * @property-read \App\Models\Gatepass|null $checkoutGatepass
 * @property-read \App\Models\Keeper|null $checkoutKeeper
 * @property-read \App\Models\Child $child
 * @method static \Database\Factories\AttendanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckedOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckinGatepassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckinKeeperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckoutGatepassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCheckoutKeeperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperAttendance {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $nickname
 * @property string $birth_date
 * @property bool $gender
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Relationship|\App\Models\Attendance|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $guardians
 * @property-read int|null $guardians_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Relationship> $relationships
 * @property-read int|null $relationships_count
 * @method static \Database\Factories\ChildFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperChild {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $guardian_id
 * @property int $child_id
 * @property int $activity_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity $activity
 * @property-read \App\Models\Child $child
 * @property-read \App\Models\Guardian $guardian
 * @method static \Database\Factories\GatepassFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperGatepass {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $birth_date
 * @property \App\Enums\Gender $gender
 * @property string $email
 * @property string|null $phone
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Relationship|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Child> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Relationship> $relationships
 * @property-read int|null $relationships_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\GuardianFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperGuardian {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property string|null $permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $checkinAttendance
 * @property-read int|null $checkin_attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $checkoutAttendance
 * @property-read int|null $checkout_attendance_count
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\KeeperFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperKeeper {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Keeper> $keepers
 * @property-read int|null $keepers_count
 * @property-read \App\Models\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperOrganization {}
}

namespace App\Models{
/**
 * @property \App\Enums\Relationship $relationship
 * @property-read \App\Models\Child|null $child
 * @property-read \App\Models\Guardian|null $guardian
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperRelationship {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $createdActivities
 * @property-read int|null $created_activities_count
 * @property-read \App\Models\Guardian|null $guardian
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Keeper> $keepers
 * @property-read int|null $keepers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $ownedOrganizations
 * @property-read int|null $owned_organizations_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}


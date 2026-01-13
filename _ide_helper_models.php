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
 * @property string|null $location_map_link
 * @property \Carbon\CarbonImmutable $starts_at
 * @property \Carbon\CarbonImmutable $ends_at
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property string|null $notes
 * @property int $organization_id
 * @property int|null $term_id
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
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\Term|null $term
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLocationMapLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereTermId($value)
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
 * @property string $attendee_code
 * @property int $activity_id
 * @property int $child_id
 * @property int|null $checkin_keeper_id
 * @property int|null $checkin_gatepass_id
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 * @property int|null $checkout_keeper_id
 * @property int|null $checkout_gatepass_id
 * @property \Illuminate\Support\Carbon|null $checked_out_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity $activity
 * @property-read \App\Models\Gatepass|null $checkinGatepass
 * @property-read \App\Models\Keeper|null $checkinKeeper
 * @property-read \App\Models\Gatepass|null $checkoutGatepass
 * @property-read \App\Models\Keeper|null $checkoutKeeper
 * @property-read \App\Models\Child $child
 * @method static \Database\Factories\AttendanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAttendeeCode($value)
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
 * @property \Carbon\CarbonImmutable $birth_date
 * @property \App\Enums\Gender $gender
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Relationship|\App\Models\Attendance|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $guardians
 * @property-read int|null $guardians_count
 * @property-read string $known_as
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Relationship> $relationships
 * @property-read int|null $relationships_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Tags\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\ChildFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAnyTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAnyTagsOfType(array|string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withoutTrashed()
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
 * @property int|null $term_acceptance_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Activity $activity
 * @property-read \App\Models\Child $child
 * @property-read \App\Models\Guardian $guardian
 * @property-read \App\Models\TermAcceptance|null $termAcceptance
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gatepass whereTermAcceptanceId($value)
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
 * @property \Carbon\CarbonImmutable $birth_date
 * @property \App\Enums\Gender $gender
 * @property string $email
 * @property string|null $phone
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Term> $acceptedTerms
 * @property-read int|null $accepted_terms_count
 * @property-read \App\Models\Relationship|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Child> $children
 * @property-read int|null $children_count
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Relationship> $relationships
 * @property-read int|null $relationships_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Tags\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TermAcceptance> $termAcceptances
 * @property-read int|null $term_acceptances_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\GuardianFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAnyTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAnyTagsOfType(array|string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withoutTrashed()
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $checkinAttendance
 * @property-read int|null $checkin_attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $checkoutAttendance
 * @property-read int|null $checkout_attendance_count
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\KeeperFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Keeper withoutTrashed()
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $guardians
 * @property-read int|null $guardians_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Keeper> $keepers
 * @property-read int|null $keepers_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Term> $terms
 * @property-read int|null $terms_count
 * @method static \Database\Factories\OrganizationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperOrganization {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $guardian_id
 * @property int $child_id
 * @property \App\Enums\Relationship $relationship
 * @property bool $is_primary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Child $child
 * @property-read \App\Models\Guardian $guardian
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereChildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperRelationship {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $version
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property int $organization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TermAcceptance> $acceptances
 * @property-read int|null $acceptances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $acceptedByGuardians
 * @property-read int|null $accepted_by_guardians_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Organization $organization
 * @method static \Database\Factories\TermFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereVersion($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperTerm {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $term_id
 * @property int $guardian_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \App\Models\Guardian $guardian
 * @property-read \App\Models\Term $term
 * @method static \Database\Factories\TermAcceptanceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TermAcceptance whereUserAgent($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperTermAcceptance {}
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
 * @property-read \App\Models\Keeper|null $keeper
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
	final class IdeHelperUser {}
}


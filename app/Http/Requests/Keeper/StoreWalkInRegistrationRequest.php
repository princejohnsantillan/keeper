<?php

declare(strict_types=1);

namespace App\Http\Requests\Keeper;

use App\Enums\Gender;
use App\Enums\Relationship;
use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreWalkInRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $activity = $this->route('activity');
        $requiresTermsAcceptance = $activity instanceof Activity && $activity->term !== null;
        $today = now()->toDateString();
        $maxGuardianBirthDate = now()->subYears(18)->toDateString();

        return [
            'guardian_first_name' => ['required', 'string', 'max:80'],
            'guardian_middle_name' => ['nullable', 'string', 'max:80'],
            'guardian_last_name' => ['required', 'string', 'max:80'],
            'guardian_birth_date' => ['required', 'date', "before_or_equal:{$maxGuardianBirthDate}"],
            'guardian_gender' => ['required', Rule::enum(Gender::class)],
            'guardian_email' => ['required', 'email'],
            'guardian_phone' => ['nullable', 'string', 'max:16'],

            'child_first_name' => ['required', 'string', 'max:80'],
            'child_middle_name' => ['nullable', 'string', 'max:80'],
            'child_last_name' => ['required', 'string', 'max:80'],
            'child_nickname' => ['nullable', 'string', 'max:40'],
            'child_birth_date' => ['required', 'date', "before_or_equal:{$today}"],
            'child_gender' => ['required', Rule::enum(Gender::class)],
            'child_notes' => ['nullable', 'string'],

            'relationship' => ['required', Rule::enum(Relationship::class)],
            'agree_to_terms' => $requiresTermsAcceptance ? ['accepted'] : ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'agree_to_terms.accepted' => 'You must accept the activity terms before registering this walk-in.',
        ];
    }
}

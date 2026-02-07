<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walk-in Registration - {{ $activity->title }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-zinc-100 antialiased">
    @php
        use App\Enums\Gender;
        use Illuminate\Support\Str;

        $requiresTermsAcceptance = $activity->term !== null;
        $termsAccepted = (bool) old('agree_to_terms', false);
        $today = now()->toDateString();
        $maxGuardianBirthDate = now()->subYears(18)->toDateString();
        $maleValue = (string) Gender::Male->value;
        $femaleValue = (string) Gender::Female->value;
        $inputClasses = 'mt-2 block w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-base text-zinc-900 shadow-sm outline-none transition focus:border-lime-500 focus:ring-4 focus:ring-lime-500/20';
        $inputErrorClasses = 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20';
    @endphp

    <div class="min-h-screen px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl rounded-3xl border border-zinc-200 bg-zinc-50 px-5 py-8 shadow-sm sm:px-8 lg:px-12 lg:py-12">
            <header class="mb-8 text-center">
                <p class="text-4xl font-semibold tracking-tight text-zinc-900">Keeper</p>
                <h1 class="mt-3 text-5xl font-black tracking-tight text-zinc-950">Walk-in Registration</h1>
                <p class="mt-4 text-lg text-zinc-600">
                    for <span class="font-semibold text-zinc-900">{{ $activity->title }}</span>
                    <span class="mx-2 text-zinc-400">•</span>
                    <a href="{{ route('filament.keeper.resources.activities.index') }}" class="font-semibold text-lime-700 underline decoration-transparent underline-offset-4 transition hover:decoration-lime-700">
                        back to activities
                    </a>
                </p>
            </header>

            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-lime-300 bg-lime-50 px-5 py-4 text-sm font-medium text-lime-900">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-50 px-5 py-4">
                    <p class="text-sm font-semibold text-rose-900">Please review the highlighted fields.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('keeper.walk-ins.store', ['activity' => $activity]) }}" class="space-y-8">
                @csrf

                @if ($activity->term !== null)
                    <fieldset class="rounded-2xl border border-zinc-300 bg-white px-6 py-6">
                        <legend class="px-2 text-2xl font-semibold tracking-tight text-zinc-900">Activity Terms</legend>

                        <div class="mt-3 max-h-64 overflow-y-auto rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-4 prose prose-zinc max-w-none">
                            {!! Str::markdown($activity->term->content ?? '') !!}
                        </div>

                        <label for="agree_to_terms" class="mt-5 flex cursor-pointer items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-4 text-sm text-zinc-800 transition hover:border-zinc-300">
                            <input
                                id="agree_to_terms"
                                type="checkbox"
                                name="agree_to_terms"
                                value="1"
                                @checked($termsAccepted)
                                class="mt-1 h-5 w-5 rounded border-zinc-300 text-lime-600 focus:ring-lime-500"
                            >
                            <span>
                                I have read and agree to the terms and conditions for this activity.
                                <span class="font-semibold text-rose-600">*</span>
                            </span>
                        </label>

                        @error('agree_to_terms')
                            <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                        @enderror
                    </fieldset>
                @endif

                <fieldset class="rounded-2xl border border-zinc-300 bg-white px-6 py-6">
                    <legend class="px-2 text-2xl font-semibold tracking-tight text-zinc-900">Guardian Details</legend>

                    <div class="mt-4 grid grid-cols-1 gap-5 lg:grid-cols-3">
                        <div>
                            <label for="guardian_first_name" class="text-sm font-semibold text-zinc-900">
                                First name <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="guardian_first_name"
                                type="text"
                                name="guardian_first_name"
                                value="{{ old('guardian_first_name') }}"
                                required
                                maxlength="80"
                                autofocus
                                class="{{ $inputClasses }} @error('guardian_first_name') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('guardian_first_name')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_middle_name" class="text-sm font-semibold text-zinc-900">Middle name</label>
                            <input
                                id="guardian_middle_name"
                                type="text"
                                name="guardian_middle_name"
                                value="{{ old('guardian_middle_name') }}"
                                maxlength="80"
                                class="{{ $inputClasses }} @error('guardian_middle_name') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('guardian_middle_name')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_last_name" class="text-sm font-semibold text-zinc-900">
                                Last name <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="guardian_last_name"
                                type="text"
                                name="guardian_last_name"
                                value="{{ old('guardian_last_name') }}"
                                required
                                maxlength="80"
                                class="{{ $inputClasses }} @error('guardian_last_name') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('guardian_last_name')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-1">
                            <p class="text-sm font-semibold text-zinc-900">
                                Gender <span class="text-rose-600">*</span>
                            </p>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="guardian_gender"
                                        value="{{ $maleValue }}"
                                        class="peer sr-only"
                                        @checked((string) old('guardian_gender') === $maleValue)
                                        required
                                    >
                                    <span class="flex items-center justify-center rounded-xl border border-zinc-300 bg-white px-4 py-3 text-base font-semibold text-zinc-700 transition peer-checked:border-lime-500 peer-checked:bg-lime-50 peer-checked:text-lime-900">
                                        Male
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="guardian_gender"
                                        value="{{ $femaleValue }}"
                                        class="peer sr-only"
                                        @checked((string) old('guardian_gender') === $femaleValue)
                                        required
                                    >
                                    <span class="flex items-center justify-center rounded-xl border border-zinc-300 bg-white px-4 py-3 text-base font-semibold text-zinc-700 transition peer-checked:border-lime-500 peer-checked:bg-lime-50 peer-checked:text-lime-900">
                                        Female
                                    </span>
                                </label>
                            </div>
                            @error('guardian_gender')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_birth_date" class="text-sm font-semibold text-zinc-900">
                                Birth date <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="guardian_birth_date"
                                type="date"
                                name="guardian_birth_date"
                                value="{{ old('guardian_birth_date') }}"
                                max="{{ $maxGuardianBirthDate }}"
                                required
                                class="{{ $inputClasses }} @error('guardian_birth_date') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('guardian_birth_date')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_phone" class="text-sm font-semibold text-zinc-900">Phone</label>
                            <input
                                id="guardian_phone"
                                type="tel"
                                name="guardian_phone"
                                value="{{ old('guardian_phone') }}"
                                maxlength="16"
                                class="{{ $inputClasses }} @error('guardian_phone') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('guardian_phone')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-3">
                            <label for="guardian_email" class="text-sm font-semibold text-zinc-900">
                                Email address <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="guardian_email"
                                type="email"
                                name="guardian_email"
                                value="{{ old('guardian_email') }}"
                                required
                                class="{{ $inputClasses }} @error('guardian_email') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('guardian_email')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset class="rounded-2xl border border-zinc-300 bg-white px-6 py-6">
                    <legend class="px-2 text-2xl font-semibold tracking-tight text-zinc-900">Child Details</legend>

                    <div class="mt-4 grid grid-cols-1 gap-5 lg:grid-cols-3">
                        <div>
                            <label for="child_first_name" class="text-sm font-semibold text-zinc-900">
                                First name <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="child_first_name"
                                type="text"
                                name="child_first_name"
                                value="{{ old('child_first_name') }}"
                                required
                                maxlength="80"
                                class="{{ $inputClasses }} @error('child_first_name') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('child_first_name')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="child_middle_name" class="text-sm font-semibold text-zinc-900">Middle name</label>
                            <input
                                id="child_middle_name"
                                type="text"
                                name="child_middle_name"
                                value="{{ old('child_middle_name') }}"
                                maxlength="80"
                                class="{{ $inputClasses }} @error('child_middle_name') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('child_middle_name')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="child_last_name" class="text-sm font-semibold text-zinc-900">
                                Last name <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="child_last_name"
                                type="text"
                                name="child_last_name"
                                value="{{ old('child_last_name') }}"
                                required
                                maxlength="80"
                                class="{{ $inputClasses }} @error('child_last_name') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('child_last_name')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="child_nickname" class="text-sm font-semibold text-zinc-900">Nickname</label>
                            <input
                                id="child_nickname"
                                type="text"
                                name="child_nickname"
                                value="{{ old('child_nickname') }}"
                                maxlength="40"
                                class="{{ $inputClasses }} @error('child_nickname') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('child_nickname')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-zinc-900">
                                Gender <span class="text-rose-600">*</span>
                            </p>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="child_gender"
                                        value="{{ $maleValue }}"
                                        class="peer sr-only"
                                        @checked((string) old('child_gender') === $maleValue)
                                        required
                                    >
                                    <span class="flex items-center justify-center rounded-xl border border-zinc-300 bg-white px-4 py-3 text-base font-semibold text-zinc-700 transition peer-checked:border-lime-500 peer-checked:bg-lime-50 peer-checked:text-lime-900">
                                        Male
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="child_gender"
                                        value="{{ $femaleValue }}"
                                        class="peer sr-only"
                                        @checked((string) old('child_gender') === $femaleValue)
                                        required
                                    >
                                    <span class="flex items-center justify-center rounded-xl border border-zinc-300 bg-white px-4 py-3 text-base font-semibold text-zinc-700 transition peer-checked:border-lime-500 peer-checked:bg-lime-50 peer-checked:text-lime-900">
                                        Female
                                    </span>
                                </label>
                            </div>
                            @error('child_gender')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="child_birth_date" class="text-sm font-semibold text-zinc-900">
                                Birth date <span class="text-rose-600">*</span>
                            </label>
                            <input
                                id="child_birth_date"
                                type="date"
                                name="child_birth_date"
                                value="{{ old('child_birth_date') }}"
                                max="{{ $today }}"
                                required
                                class="{{ $inputClasses }} @error('child_birth_date') {{ $inputErrorClasses }} @enderror"
                            >
                            @error('child_birth_date')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-3">
                            <label for="child_notes" class="text-sm font-semibold text-zinc-900">Notes</label>
                            <textarea
                                id="child_notes"
                                name="child_notes"
                                rows="3"
                                class="{{ $inputClasses }} @error('child_notes') {{ $inputErrorClasses }} @enderror"
                            >{{ old('child_notes') }}</textarea>
                            @error('child_notes')
                                <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset class="rounded-2xl border border-zinc-300 bg-white px-6 py-6">
                    <legend class="px-2 text-2xl font-semibold tracking-tight text-zinc-900">Relationship</legend>

                    <div class="mt-4">
                        <label for="relationship" class="text-sm font-semibold text-zinc-900">
                            Relationship to Child <span class="text-rose-600">*</span>
                        </label>
                        <select
                            id="relationship"
                            name="relationship"
                            required
                            class="{{ $inputClasses }} @error('relationship') {{ $inputErrorClasses }} @enderror"
                        >
                            <option value="">Select relationship</option>
                            @foreach ($relationshipOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('relationship') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('relationship')
                            <p class="mt-2 text-sm font-medium text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <div class="space-y-2">
                    <button
                        id="walkin-submit"
                        type="submit"
                        @disabled($requiresTermsAcceptance && ! $termsAccepted)
                        class="inline-flex w-full items-center justify-center rounded-xl bg-lime-500 px-6 py-4 text-xl font-semibold text-zinc-900 shadow-sm transition hover:bg-lime-400 focus:outline-none focus:ring-4 focus:ring-lime-500/40 disabled:cursor-not-allowed disabled:bg-zinc-300 disabled:text-zinc-500"
                    >
                        Register Walk-in
                    </button>

                    @if ($requiresTermsAcceptance)
                        <p class="text-sm text-zinc-600">The button stays disabled until activity terms are accepted.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($requiresTermsAcceptance)
        <script>
            const termsCheckbox = document.getElementById('agree_to_terms');
            const submitButton = document.getElementById('walkin-submit');

            if (termsCheckbox !== null && submitButton !== null) {
                const syncState = () => {
                    submitButton.disabled = !termsCheckbox.checked;
                };

                termsCheckbox.addEventListener('change', syncState);
                syncState();
            }
        </script>
    @endif
</body>
</html>

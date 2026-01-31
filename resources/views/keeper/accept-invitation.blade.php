<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation</title>
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Accept Invitation
                </h2>
                @if($invitation)
                    <p class="mt-2 text-center text-sm text-gray-600">
                        You have been invited to join <span class="font-semibold">{{ $invitation->organization->name }}</span> as <span class="font-semibold">{{ $invitation->role->getLabel() }}</span> by {{ $invitation->invitedBy->name }}.
                    </p>
                @endif
            </div>

            @if(session('error') || $error)
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ session('error') ?? $error }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/admin/login" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Go to Login
                    </a>
                </div>
            @endif

            @if($showForm)
                <form class="mt-8 space-y-6" method="POST" action="{{ route('filament.keeper.invitation.accept') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="rounded-md shadow-sm space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                minlength="8"
                                class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Password (min 8 characters)"
                            >
                            @error('password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Confirm Password"
                            >
                            @error('password_confirmation') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Accept Invitation & Set Password
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</body>
</html>

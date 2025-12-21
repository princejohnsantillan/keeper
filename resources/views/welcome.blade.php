<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Keeper') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-900 dark:bg-stone-950 dark:text-stone-100">
        <!-- Header -->
        <header class="border-b border-stone-200 dark:border-stone-800">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a href="/" class="text-xl font-bold text-lime-600 dark:text-lime-400">
                    {{ config('app.name', 'Keeper') }}
                </a>

                <nav class="flex items-center gap-4">
                    <a
                        href="{{ route('filament.guardian.auth.login') }}"
                        class="text-sm font-medium text-stone-600 transition hover:text-lime-600 dark:text-stone-400 dark:hover:text-lime-400"
                    >
                        Log in
                    </a>
                    <a
                        href="{{ route('filament.guardian.auth.register') }}"
                        class="rounded-lg bg-lime-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-lime-600 dark:bg-lime-600 dark:hover:bg-lime-500"
                    >
                        Register
                    </a>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="px-6 py-24 text-center">
            <div class="mx-auto max-w-3xl">
                <h1 class="mb-6 text-4xl font-bold tracking-tight text-stone-900 dark:text-white sm:text-5xl">
                    Welcome to <span class="text-lime-600 dark:text-lime-400">{{ config('app.name', 'Keeper') }}</span>
                </h1>
                <p class="mb-10 text-lg text-stone-600 dark:text-stone-400">
                    Your trusted companion for managing and keeping track of what matters most.
                    Simple, secure, and always by your side.
                </p>
                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a
                        href="{{ route('filament.guardian.auth.register') }}"
                        class="inline-flex items-center rounded-lg bg-lime-500 px-6 py-3 text-base font-medium text-white transition hover:bg-lime-600 dark:bg-lime-600 dark:hover:bg-lime-500"
                    >
                        Get Started
                    </a>
                    <a
                        href="#faq"
                        class="inline-flex items-center rounded-lg border border-stone-300 bg-white px-6 py-3 text-base font-medium text-stone-700 transition hover:border-lime-500 hover:text-lime-600 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-300 dark:hover:border-lime-500 dark:hover:text-lime-400"
                    >
                        Learn More
                    </a>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="border-t border-stone-200 bg-white px-6 py-20 dark:border-stone-800 dark:bg-stone-900">
            <div class="mx-auto max-w-3xl">
                <h2 class="mb-12 text-center text-3xl font-bold text-stone-900 dark:text-white">
                    Frequently Asked Questions
                </h2>

                <div class="space-y-6">
                    <!-- Question 1 -->
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800">
                        <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-white">
                            What is Keeper?
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400">
                            Keeper is a comprehensive management platform designed to help you organize and track
                            important information efficiently. Whether for personal or professional use, Keeper
                            provides the tools you need to stay on top of things.
                        </p>
                    </div>

                    <!-- Question 2 -->
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800">
                        <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-white">
                            How do I create an account?
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400">
                            Creating an account is simple! Click the "Register" button at the top of the page,
                            fill in your details, and you'll be ready to go in just a few moments. No credit card
                            required to get started.
                        </p>
                    </div>

                    <!-- Question 3 -->
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800">
                        <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-white">
                            Is my data secure?
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400">
                            Absolutely! We take security seriously. All data is encrypted both in transit and at rest.
                            We follow industry best practices to ensure your information remains safe and private
                            at all times.
                        </p>
                    </div>

                    <!-- Question 4 -->
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800">
                        <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-white">
                            Can I access Keeper on my mobile device?
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400">
                            Yes! Keeper is fully responsive and works seamlessly on all devices including smartphones,
                            tablets, and desktop computers. Access your information anytime, anywhere.
                        </p>
                    </div>

                    <!-- Question 5 -->
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800">
                        <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-white">
                            How can I get support if I need help?
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400">
                            We're here to help! You can reach out to our support team through the contact form
                            in your dashboard, or email us directly. We typically respond within 24 hours and
                            are happy to assist with any questions.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-stone-200 bg-stone-50 px-6 py-8 dark:border-stone-800 dark:bg-stone-950">
            <div class="mx-auto max-w-5xl text-center">
                <p class="text-sm text-stone-500 dark:text-stone-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Keeper') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </body>
</html>

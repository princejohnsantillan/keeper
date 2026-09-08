<x-filament-widgets::widget>
    <x-filament::section
        heading="Tag Distribution"
        description="How many registered children carry each tag, and how many of them checked in."
        icon="heroicon-o-tag"
        collapsible
    >
        @if (count($tagCounts) === 0)
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No tags recorded for the children registered to this activity.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th scope="col" class="py-2 pe-4 text-start">Tag</th>
                            <th scope="col" class="px-4 py-2 text-end">Registered</th>
                            <th scope="col" class="ps-4 py-2 text-end">Attended</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($tagCounts as $tag => $counts)
                            <tr>
                                <td class="py-2 pe-4">
                                    <x-filament::badge color="gray">
                                        {{ ucfirst($tag) }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-4 py-2 text-end font-medium text-gray-900 dark:text-white">
                                    {{ $counts['registered'] }}
                                </td>
                                <td class="ps-4 py-2 text-end font-semibold text-success-600 dark:text-success-400">
                                    {{ $counts['attended'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

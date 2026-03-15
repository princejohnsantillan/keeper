<x-filament::page>
    @php
        $gatepass = $this->getGatepass();
        $displayTimezone = config('app.display_timezone');
        $attendanceState = $this->attendanceState ?? [
            'status' => null,
            'can_check_in' => false,
            'can_check_out' => false,
            'reason' => null,
        ];
    @endphp

    <div class="space-y-6">
        {{-- Code Input Form --}}
        <form wire:submit="lookup" class="max-w-md">
            {{ $this->form }}
            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                    Look Up
                </x-filament::button>
                <x-filament::button
                    type="button"
                    color="gray"
                    icon="heroicon-o-camera"
                    x-data=""
                    x-on:click="$dispatch('toggle-camera')"
                >
                    <span x-text="$store.qrScanner?.isScanning ? 'Stop Camera' : 'Scan QR Code'">Scan QR Code</span>
                </x-filament::button>
            </div>
        </form>

        {{-- Camera Scanner --}}
        <div
            x-data="qrScanner"
            x-on:toggle-camera.window="toggle()"
            class="max-w-md"
        >
            <div
                x-show="isScanning"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="rounded-lg overflow-hidden bg-gray-900"
            >
                <div id="qr-reader" class="w-full"></div>
                <div class="p-3 text-center text-sm text-gray-400">
                    Point your camera at a QR code to scan
                </div>
            </div>
        </div>

        {{-- Gatepass Details --}}
        @if ($gatepass)
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <span>Gatepass Details</span>
                        <x-filament::badge
                            :color="match ($attendanceState['status']) {
                                'not_checked_in' => 'gray',
                                'checked_in' => 'success',
                                'checked_out' => 'info',
                                default => 'gray',
                            }"
                        >
                            {{ match ($attendanceState['status']) {
                                'not_checked_in' => 'Not Checked In',
                                'checked_in' => 'Checked In',
                                'checked_out' => 'Checked Out',
                                default => 'Unknown',
                            } }}
                        </x-filament::badge>
                    </div>
                </x-slot>

                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Child Info --}}
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Child</h3>
                        <div class="flex items-center gap-3">
                            <img
                                src="{{ $gatepass->child->getFirstMediaUrl('avatar') ?: \App\Avatar::generateUrl($gatepass->child->full_name) }}"
                                alt="{{ $gatepass->child->full_name }}"
                                class="h-24 w-24 rounded-full object-cover"
                            />
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $gatepass->child->full_name }}
                                </p>
                                <p class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    <x-filament::icon icon="heroicon-o-cake" class="h-4 w-4" />
                                    <span>
                                        {{ $gatepass->child->birth_date->age }}
                                        {{ $gatepass->child->birth_date->age === 1 ? 'year' : 'years' }} old
                                    </span>
                                </p>
                                <p class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    <x-filament::icon :icon="$gatepass->child->gender->getIcon()" class="h-4 w-4" />
                                    <span>{{ $gatepass->child->gender->getLabel() }}</span>
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @forelse ($gatepass->child->organizationTags as $tag)
                                        <x-filament::badge color="gray">
                                            {{ $tag->name }}
                                        </x-filament::badge>
                                    @empty
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No tags</p>
                                    @endforelse
                                    <x-filament::icon-button
                                        wire:click="mountAction('editChildTags')"
                                        icon="heroicon-o-tag"
                                        color="gray"
                                        size="sm"
                                        label="Edit child tags"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Guardian Info --}}
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Guardian</h3>
                        <div class="flex items-center gap-3">
                            <img
                                src="{{ $gatepass->guardian->getFirstMediaUrl('avatar') ?: \App\Avatar::generateUrl($gatepass->guardian->full_name) }}"
                                alt="{{ $gatepass->guardian->full_name }}"
                                class="h-24 w-24 rounded-full object-cover"
                            />
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $gatepass->guardian->full_name }}
                                </p>
                                <p class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    <x-filament::icon :icon="$gatepass->guardian->gender->getIcon()" class="h-4 w-4" />
                                    <span>{{ $gatepass->guardian->gender->getLabel() }}</span>
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    @forelse ($gatepass->guardian->organizationTags as $tag)
                                        <x-filament::badge color="gray">
                                            {{ $tag->name }}
                                        </x-filament::badge>
                                    @empty
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No tags</p>
                                    @endforelse
                                    <x-filament::icon-button
                                        wire:click="mountAction('editGuardianTags')"
                                        icon="heroicon-o-tag"
                                        color="gray"
                                        size="sm"
                                        label="Edit guardian tags"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Code Display --}}
                <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                    <div class="grid gap-6 md:grid-cols-2 md:items-center">
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gatepass Code</p>
                            <p class="font-mono text-2xl font-bold tracking-wider text-gray-900 dark:text-white">
                                {{ $gatepass->code }}
                            </p>
                        </div>

                        <div class="space-y-1 md:border-l md:border-gray-200 md:pl-6 dark:md:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Activity</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $gatepass->activity->title }}
                            </p>
                            @if ($gatepass->activity->starts_at)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Starts: {{ $gatepass->activity->starts_at->setTimezone($displayTimezone)->format('M d, Y h:i A') }}
                                </p>
                            @endif
                            @if ($gatepass->activity->ends_at)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Ends: {{ $gatepass->activity->ends_at->setTimezone($displayTimezone)->format('M d, Y h:i A') }}
                                </p>
                            @endif
                            @if ($attendanceState['reason'] === 'not_published')
                                <p class="text-sm text-danger-600 dark:text-danger-400">
                                    Attendance actions are unavailable until this activity is published.
                                </p>
                            @elseif ($attendanceState['reason'] === 'event_ended')
                                <p class="text-sm text-warning-600 dark:text-warning-400">
                                    Check-in is closed because this activity has already ended.
                                </p>
                            @elseif ($attendanceState['reason'] === 'checkin_closed')
                                <p class="text-sm text-warning-600 dark:text-warning-400">
                                    Check-in has been closed for this activity.
                                </p>
                            @elseif ($attendanceState['reason'] === 'checkin_not_open')
                                <p class="text-sm text-danger-600 dark:text-danger-400">
                                    Check-in is not open for this activity yet.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($attendanceState['can_check_in'])
                        <x-filament::button
                            wire:click="checkInAndPrint"
                            icon="heroicon-o-printer"
                        >
                            Check In & Print
                        </x-filament::button>

                        <x-filament::button
                            wire:click="checkIn"
                            color="success"
                            icon="heroicon-o-arrow-right-end-on-rectangle"
                        >
                            Check In
                        </x-filament::button>
                    @endif

                    @if ($attendanceState['can_check_out'])
                        <x-filament::button
                            wire:click="checkOut"
                            color="warning"
                            icon="heroicon-o-arrow-left-start-on-rectangle"
                        >
                            Check Out
                        </x-filament::button>
                    @endif

                    @if (in_array($attendanceState['status'], ['checked_in', 'checked_out'], true))
                        <x-filament::button
                            wire:click="print"
                            color="gray"
                            icon="heroicon-o-printer"
                        >
                            Print
                        </x-filament::button>
                    @endif

                    <x-filament::button
                        wire:click="clearGatepass"
                        color="gray"
                        icon="heroicon-o-x-mark"
                    >
                        Clear
                    </x-filament::button>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="py-12 text-center">
                    <x-filament::icon
                        icon="heroicon-o-qr-code"
                        class="mx-auto h-12 w-12 text-gray-400"
                    />
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                        Ready to Scan
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Enter or scan a gatepass code to view details and manage attendance.
                    </p>
                </div>
            </x-filament::section>
        @endif
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('qrScanner', () => ({
                    isScanning: false,
                    scanner: null,

                    init() {
                        Alpine.store('qrScanner', this);
                    },

                    async toggle() {
                        if (this.isScanning) {
                            await this.stop();
                        } else {
                            await this.start();
                        }
                    },

                    async start() {
                        this.isScanning = true;
                        await this.$nextTick();

                        this.scanner = new Html5Qrcode("qr-reader");

                        try {
                            await this.scanner.start(
                                { facingMode: "environment" },
                                {
                                    fps: 10,
                                    qrbox: { width: 250, height: 250 },
                                    aspectRatio: 1.0,
                                },
                                (decodedText) => {
                                    this.onScanSuccess(decodedText);
                                },
                                (errorMessage) => {
                                    // Ignore scan errors (no QR found in frame)
                                }
                            );
                        } catch (err) {
                            console.error('Camera error:', err);
                            this.isScanning = false;

                            // Show error notification
                            new FilamentNotification()
                                .title('Camera Error')
                                .body('Could not access camera. Please check permissions.')
                                .danger()
                                .send();
                        }
                    },

                    async stop() {
                        if (this.scanner) {
                            try {
                                await this.scanner.stop();
                            } catch (err) {
                                console.error('Error stopping scanner:', err);
                            }
                            this.scanner = null;
                        }
                        this.isScanning = false;
                    },

                    async onScanSuccess(decodedText) {
                        // Stop scanning
                        await this.stop();

                        // Set the code in the form and trigger lookup
                        @this.set('data.code', decodedText);
                        @this.call('lookup');
                    },

                    destroy() {
                        this.stop();
                    }
                }));
            });
        </script>
    @endpush
</x-filament::page>

<x-filament-panels::page>

    {{-- Filter bar --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach (['all' => 'Tout', 'manifestations' => 'Manifestations', 'offers' => 'Offres', 'projects' => 'Projets'] as $value => $label)
            <button
                wire:click="$set('filter', '{{ $value }}')"
                class="px-3 py-1 rounded-full text-sm font-medium transition
                    {{ $filter === $value
                        ? 'bg-primary-600 text-white'
                        : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Calendar container --}}
    <div
        wire:ignore
        x-data="berdCalendar(@js($calendarEvents))"
        x-init="init()"
        class="bg-white dark:bg-gray-900 rounded-xl shadow p-4"
        @calendar-events-updated.window="updateEvents($event.detail.events)"
    >
        <div x-ref="calendarEl" style="min-height:600px;"></div>
    </div>

    {{-- FullCalendar CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.js"></script>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('berdCalendar', (initialEvents) => ({
            calendar: null,
            init() {
                this.calendar = new FullCalendar.Calendar(this.$refs.calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    height: 'auto',
                    events: initialEvents,
                    headerToolbar: {
                        left:   'prev,next today',
                        center: 'title',
                        right:  'dayGridMonth,timeGridWeek,listMonth',
                    },
                    buttonText: {
                        today:     'Aujourd\'hui',
                        month:     'Mois',
                        week:      'Semaine',
                        list:      'Liste',
                    },
                    eventClick(info) {
                        const url = info.event.extendedProps?.url;
                        if (url) {
                            window.location.href = url;
                        }
                    },
                    eventMouseEnter(info) {
                        info.el.style.cursor = 'pointer';
                    },
                });
                this.calendar.render();
            },
            updateEvents(events) {
                if (this.calendar) {
                    this.calendar.removeAllEvents();
                    this.calendar.addEventSource(events);
                }
            },
        }));
    });
    </script>

</x-filament-panels::page>

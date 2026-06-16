<?php

namespace App\Filament\Pages;

use App\Models\Manifestation;
use App\Models\Offer;
use App\Models\Project;
use Filament\Pages\Page;

class BerdCalendarPage extends Page
{
    protected string $view = 'filament.pages.berd-calendar';

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Calendrier';
    protected static \UnitEnum|string|null $navigationGroup = 'Pilotage d\'Activités';
    protected static ?int $navigationSort = 10;

    public string $filter = 'all';

    public function updatedFilter(): void
    {
        $this->dispatch('calendar-events-updated', events: $this->buildEvents());
    }

    private function buildEvents(): array
    {
        $events = [];

        if ($this->filter === 'all' || $this->filter === 'manifestations') {
            Manifestation::whereNotNull('deadline')
                ->with('avisManifestation:id,title')
                ->select(['id', 'deadline', 'status', 'avis_manifestation_id'])
                ->get()
                ->each(function ($m) use (&$events) {
                    $events[] = [
                        'id'    => 'manif-' . $m->id,
                        'title' => '📋 ' . ($m->avisManifestation?->title ?? "Manifestation #{$m->id}"),
                        'start' => $m->deadline?->toDateString(),
                        'color' => match ($m->status) {
                            'won'       => '#22c55e',
                            'lost'      => '#ef4444',
                            'abandoned' => '#f59e0b',
                            default     => '#3b82f6',
                        },
                        'extendedProps' => [
                            'url' => url('/admin/manifestations/' . $m->id),
                        ],
                    ];
                });
        }

        if ($this->filter === 'all' || $this->filter === 'offers') {
            Offer::whereHas('technicalOffer', fn($q) => $q->whereNotNull('deadline'))
                ->with(['technicalOffer:id,offer_id,deadline'])
                ->select(['id', 'title', 'result'])
                ->get()
                ->each(function ($o) use (&$events) {
                    $deadline = $o->technicalOffer?->deadline;
                    if (! $deadline) {
                        return;
                    }
                    $events[] = [
                        'id'    => 'offer-' . $o->id,
                        'title' => '📄 ' . ($o->title ?? "Offre #{$o->id}"),
                        'start' => $deadline->toDateString(),
                        'color' => match ($o->result) {
                            'won'       => '#22c55e',
                            'lost'      => '#ef4444',
                            'abandoned' => '#f59e0b',
                            default     => '#8b5cf6',
                        },
                        'extendedProps' => [
                            'url' => url('/admin/offers/' . $o->id),
                        ],
                    ];
                });
        }

        if ($this->filter === 'all' || $this->filter === 'projects') {
            Project::whereNotNull('planned_end_date')
                ->select(['id', 'title', 'code', 'status', 'planned_start_date', 'planned_end_date'])
                ->get()
                ->each(function ($p) use (&$events) {
                    $events[] = [
                        'id'    => 'proj-' . $p->id,
                        'title' => '🏗 ' . ($p->code ? "[{$p->code}] " : '') . $p->title,
                        'start' => $p->planned_start_date?->toDateString() ?? $p->planned_end_date->toDateString(),
                        'end'   => $p->planned_end_date->addDay()->toDateString(), // FullCalendar end is exclusive
                        'color' => match ($p->status) {
                            'completed'   => '#22c55e',
                            'cancelled'   => '#ef4444',
                            'suspended'   => '#f59e0b',
                            'ongoing'     => '#3b82f6',
                            default       => '#9ca3af',
                        },
                        'extendedProps' => [
                            'url' => url('/admin/projects/' . $p->id),
                        ],
                    ];
                });
        }

        return $events;
    }

    protected function getViewData(): array
    {
        return [
            'calendarEvents' => $this->buildEvents(),
        ];
    }
}

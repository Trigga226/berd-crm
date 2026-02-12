<?php

namespace App\Filament\Widgets;

use App\Models\Offer;
use App\Services\OfferAlertService;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class OfferAlertsWidget extends TableWidget
{
    protected static ?string $heading = 'Alertes Offres';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        /** @var OfferAlertService $service */
        $service = app(OfferAlertService::class);

        return $table
            ->query(
                $service->applyAlertFilters(Offer::query())
            )
            ->heading('🚨 Alertes Offres')
            ->columns([
                TextColumn::make('title')
                    ->label('Offre')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->url(fn(Offer $record): string => \App\Filament\Resources\Offers\OfferResource::getUrl('view', ['record' => $record])),

                TextColumn::make('alerts_summary')
                    ->label('Type d\'alerte')
                    ->state(function (Offer $record) use ($service) {
                        $alerts = [];

                        // Technical Check
                        if ($record->technicalOffer && $service->shouldAlert($record->technicalOffer)) {
                            $alerts[] = 'Technique';
                        }

                        // Financial Check
                        if ($record->financialOffer && $service->shouldAlert($record->financialOffer)) {
                            $alerts[] = 'Financière';
                        }

                        return implode(', ', $alerts);
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Technique' => 'info',
                        'Financière' => 'success',
                        'Technique, Financière' => 'warning', // Both
                        default => 'gray',
                    }),

                TextColumn::make('deadlines')
                    ->label('Dates (Limite / Ctrl. Interne)')
                    ->html()
                    ->state(function (Offer $record) {
                        $lines = [];

                        $formatDate = function ($date, $daysThreshold) {
                            if (!$date) return '-';
                            $isImminent = $date <= now()->addDays($daysThreshold);
                            $formatted = $date->format('d/m/Y');
                            return $isImminent ? "<span style='color: red; font-weight: bold;'>{$formatted}</span>" : $formatted;
                        };

                        if ($record->technicalOffer) {
                            $dead = $formatDate($record->technicalOffer->deadline, 4);
                            $ctrl = $formatDate($record->technicalOffer->internal_control_date, 2);
                            if ($record->technicalOffer->deadline || $record->technicalOffer->internal_control_date) {
                                $lines[] = "<div class='text-xs'><span class='text-gray-500 font-semibold'>Tech:</span> {$dead} <span class='text-gray-400'>|</span> {$ctrl}</div>";
                            }
                        }

                        if ($record->financialOffer) {
                            $dead = $formatDate($record->financialOffer->deadline, 4);
                            $ctrl = $formatDate($record->financialOffer->internal_control_date, 2);
                            if ($record->financialOffer->deadline || $record->financialOffer->internal_control_date) {
                                $lines[] = "<div class='text-xs'><span class='text-gray-500 font-semibold'>Fin:</span> {$dead} <span class='text-gray-400'>|</span> {$ctrl}</div>";
                            }
                        }

                        return implode('', $lines);
                    }),
            ])
            ->paginated([5]);
    }
}

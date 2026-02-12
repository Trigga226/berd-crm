<?php

namespace App\Filament\Resources\Manifestations\Pages;

use App\Filament\Resources\Manifestations\ManifestationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListManifestations extends ListRecords
{
    use \Filament\Pages\Concerns\ExposesTableToWidgets;

    protected static string $resource = ManifestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print_stats')
                ->label('Imprimer Stats')
                ->icon('heroicon-m-printer')
                ->color('gray')
                ->url(fn() => route('manifestations.print-stats', ['filters' => $this->tableFilters ?? []]))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Manifestations\Widgets\ManifestationsChartTable::class,
        ];
    }
}

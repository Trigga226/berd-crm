<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffers extends ListRecords
{
    use \Filament\Pages\Concerns\ExposesTableToWidgets;

    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print_stats')
                ->label('Imprimer Stats')
                ->icon('heroicon-m-printer')
                ->color('gray')
                ->url(fn() => route('offers.print-stats', ['filters' => $this->tableFilters ?? []]))
                ->openUrlInNewTab(),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Offers\Widgets\OffersChartTable::class,
        ];
    }
}

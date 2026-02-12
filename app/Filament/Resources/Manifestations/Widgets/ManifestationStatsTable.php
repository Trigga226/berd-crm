<?php

namespace App\Filament\Resources\Manifestations\Widgets;

use App\Filament\Widgets\ManifestationStatsOverview;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Illuminate\Database\Eloquent\Builder;

class ManifestationStatsTable extends ManifestationStatsOverview
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return \App\Filament\Resources\Manifestations\Pages\ListManifestations::class;
    }

    protected function getBaseQuery(): Builder
    {
        return $this->getPageTableQuery() ?? parent::getBaseQuery();
    }
}

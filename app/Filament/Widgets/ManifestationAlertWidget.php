<?php

namespace App\Filament\Widgets;

use App\Models\Manifestation;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ManifestationAlertWidget extends TableWidget
{
    protected static ?string $heading = 'Alertes Manifestations';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => Manifestation::query()
                    ->where(function (Builder $query) {
                        $query->whereDate('deadline', '<=', now()->addDays(4))
                            ->orWhereDate('internal_control_date', '<=', now()->addDays(2));
                    })
                    ->whereNotIn('status', ['won', 'lost', 'abandoned', 'submitted'])
            )->heading('🚨 Alertes Manifestations')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('avisManifestation.title')
                    ->label('Manifestation')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('deadline')
                    ->label('Date Limite')
                    ->dateTime()
                    ->color('danger')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('internal_control_date')
                    ->label('Contrôle Interne')
                    ->dateTime()
                    ->color('warning')
                    ->sortable(),
            ])
            ->paginated([5]);
    }
}

<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Utils\Pays;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->label('Titre'),
                TextColumn::make('client.name')->searchable()->sortable()->label('Client'),
                TextColumn::make('result')->label('Résultat'),
                IconColumn::make('is_consortium')->boolean()->label('Groupement'),
                TextColumn::make('created_at')->dateTime()->label('Créé le'),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->label('Résultat')
                    ->options([
                        'won' => 'Gagné',
                        'lost' => 'Perdu',
                        'abandoned' => 'Abandonné',
                        'pending' => 'En Cours',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'pending') {
                            return $query->where(function ($q) {
                                $q->whereNull('result')
                                    ->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
                            });
                        }
                        return $query->where('result', $data['value']);
                    }),
                SelectFilter::make('country')
                    ->label('Pays')
                    ->options(Pays::$LISTEPAYS)
                    ->searchable(),
                SelectFilter::make('is_consortium')
                    ->label('Groupement')
                    ->options([
                        '1' => 'Oui',
                        '0' => 'Non',
                    ])
                    ->query(
                        fn(Builder $query, array $data) =>
                        $query->where('is_consortium', $data['value'] === '1')
                    ),
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Créé à partir du'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Créé jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

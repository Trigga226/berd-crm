<?php

namespace App\Filament\Resources\Partners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Nom / Raison Sociale')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                \Filament\Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'physique' => 'Physique',
                        'morale' => 'Morale',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'physique' => 'info',
                        'morale' => 'warning',
                    }),

                \Filament\Tables\Columns\TextColumn::make('domains')
                    ->label('Spécialités')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('country')
                    ->label('Pays')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Docs')
                    ->badge(),

                \Filament\Tables\Columns\TextColumn::make('references_count')
                    ->counts('references')
                    ->label('Refs')
                    ->badge(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'physique' => 'Personne Physique',
                        'morale' => 'Personne Morale',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('country')
                    ->label('Pays')
                    ->options(\App\utils\Pays::$LISTEPAYS)
                    ->searchable(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

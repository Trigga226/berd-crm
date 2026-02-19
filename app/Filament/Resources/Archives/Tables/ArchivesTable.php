<?php

namespace App\Filament\Resources\Archives\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ArchivesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Avis de manifestation' => 'warning',
                        'Manifestation' => 'info',
                        'Offres' => 'success',
                        'Livrable' => 'primary',
                        'Rapport' => 'gray',
                        'Document administratif' => 'danger',
                        default => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('date_archive')
                    ->label('Date d\'archivage')
                    ->date()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('archive_par')
                    ->label('Archivé par')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('resultat')
                    ->label('Résultat')
                    ->placeholder('N/A')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make()->visible(Auth::user()->email==="franck.b@berd-ing.com"),
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'Avis de manifestation' => 'Avis de manifestation',
                        'Manifestation' => 'Manifestation',
                        'Offres' => 'Offres',
                        'Livrable' => 'Livrable',
                        'Rapport' => 'Rapport',
                        'Document administratif' => 'Document administratif',
                        'Autre' => 'Autre',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(Auth::user()->email==="franck.b@berd-ing.com"),
                    RestoreBulkAction::make()->visible(Auth::user()->email==="franck.b@berd-ing.com"),
                ]),
            ]);
    }
}

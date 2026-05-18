<?php

namespace App\Filament\Resources\Archives\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ArchivesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Avis de manifestation' => 'warning',
                        'Manifestation'          => 'info',
                        'Offres'                 => 'success',
                        'Livrable'               => 'primary',
                        'Rapport'                => 'gray',
                        'Document administratif' => 'danger',
                        default                  => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date_archive')
                    ->label('Date d\'archivage')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('archive_par')
                    ->label('Archivé par')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('resultat')
                    ->label('Résultat')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('media_files_count')
                    ->label('Documents')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make()->visible(Auth::user()->isSuperAdmin()),
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'Avis de manifestation' => 'Avis de manifestation',
                        'Manifestation'          => 'Manifestation',
                        'Offres'                 => 'Offres',
                        'Livrable'               => 'Livrable',
                        'Rapport'                => 'Rapport',
                        'Document administratif' => 'Document administratif',
                        'Autre'                  => 'Autre',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(Auth::user()->isSuperAdmin()),
                    RestoreBulkAction::make()->visible(Auth::user()->isSuperAdmin()),
                ]),
            ])
            ->defaultSort('date_archive', 'desc');
    }
}

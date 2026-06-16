<?php

namespace App\Filament\Resources\Experts\RelationManagers;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectContracts';

    protected static ?string $title = 'Contrats Projets';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role')
            ->columns([
                TextColumn::make('project.code')
                    ->label('Projet')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->url(fn($record) => $record->project ? ProjectResource::getUrl('view', ['record' => $record->project]) : null),

                TextColumn::make('project.title')
                    ->label('Titre du Projet')
                    ->limit(35)
                    ->placeholder('—'),

                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'active'    => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('daily_rate')
                    ->label('Taux Jour')
                    ->numeric(thousandsSeparator: ' ')
                    ->suffix(' XOF')
                    ->sortable(),

                TextColumn::make('planned_days')
                    ->label('J. Prévus')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('actual_days')
                    ->label('J. Réels')
                    ->numeric()
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label('Fin Contrat')
                    ->date('d/m/Y')
                    ->color(fn($record) => $record->end_date && $record->end_date <= now()->addDays(30) ? 'warning' : 'success')
                    ->sortable(),
            ])
            ->defaultSort('end_date', 'desc')
            ->paginated([5, 10]);
    }
}

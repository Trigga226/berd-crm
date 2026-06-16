<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projets';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->limit(40)
                    ->searchable()
                    ->url(fn($record) => ProjectResource::getUrl('view', ['record' => $record])),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'preparation' => 'gray',
                        'ongoing'     => 'info',
                        'suspended'   => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'preparation' => 'Préparation',
                        'ongoing'     => 'En Cours',
                        'suspended'   => 'Suspendu',
                        'completed'   => 'Terminé',
                        'cancelled'   => 'Annulé',
                        default       => $state,
                    }),

                TextColumn::make('execution_percentage')
                    ->label('Avancement')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('total_budget')
                    ->label('Budget Total')
                    ->numeric(thousandsSeparator: ' ')
                    ->suffix(' XOF')
                    ->sortable(),

                TextColumn::make('planned_end_date')
                    ->label('Fin Prévue')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('planned_end_date', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => ProjectResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

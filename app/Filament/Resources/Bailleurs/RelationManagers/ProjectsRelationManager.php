<?php

namespace App\Filament\Resources\Bailleurs\RelationManagers;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Projets financés';

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
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Projet')
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

                IconColumn::make('pivot.is_lead')
                    ->label('Chef de file')
                    ->boolean(),

                TextColumn::make('pivot.financing_amount')
                    ->label('Financement')
                    ->money('XOF')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => ProjectResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

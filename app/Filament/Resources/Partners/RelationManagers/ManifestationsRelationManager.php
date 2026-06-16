<?php

namespace App\Filament\Resources\Partners\RelationManagers;

use App\Filament\Resources\Manifestations\ManifestationResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManifestationsRelationManager extends RelationManager
{
    protected static string $relationship = 'manifestations';

    protected static ?string $title = 'Manifestations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('client_name')
            ->columns([
                TextColumn::make('avisManifestation.title')
                    ->label('Titre')
                    ->limit(45)
                    ->searchable()
                    ->url(fn($record) => ManifestationResource::getUrl('view', ['record' => $record])),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'info',
                        'won'       => 'success',
                        'lost'      => 'danger',
                        'abandoned' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'draft'     => 'Brouillon',
                        'submitted' => 'Soumis',
                        'won'       => 'Gagné',
                        'lost'      => 'Perdu',
                        'abandoned' => 'Abandonné',
                        default     => $state,
                    }),

                TextColumn::make('is_groupement')
                    ->label('Chef de File')
                    ->state(fn($record) => $record->pivot?->is_lead ? 'Oui' : 'Non')
                    ->badge()
                    ->color(fn($state) => $state === 'Oui' ? 'success' : 'gray'),

                TextColumn::make('client_name')
                    ->label('Client')
                    ->placeholder('—'),

                TextColumn::make('deadline')
                    ->label('Date Limite')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('deadline', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => ManifestationResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

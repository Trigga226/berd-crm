<?php

namespace App\Filament\Resources\AvisManifestations\RelationManagers;

use App\Filament\Resources\Manifestations\ManifestationResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManifestationsRelationManager extends RelationManager
{
    protected static string $relationship = 'manifestations';

    protected static ?string $title = 'Manifestations soumises';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('client_name')
            ->columns([
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

                TextColumn::make('client_name')
                    ->label('Client')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('country')
                    ->label('Pays')
                    ->placeholder('—'),

                TextColumn::make('score')
                    ->label('Score')
                    ->suffix('/10')
                    ->placeholder('—'),

                IconColumn::make('is_groupement')
                    ->label('Groupement')
                    ->boolean(),

                TextColumn::make('deadline')
                    ->label('Date Limite')
                    ->date('d/m/Y')
                    ->color(fn($record) => $record->deadline && $record->deadline <= now() ? 'danger' : null)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => ManifestationResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

<?php

namespace App\Filament\Resources\Manifestations\RelationManagers;

use App\Filament\Resources\Offers\OfferResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $title = 'Offres liées';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => OfferResource::getUrl('view', ['record' => $record])),

                TextColumn::make('client.name')
                    ->label('Client')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('result')
                    ->label('Résultat')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'won'       => 'success',
                        'lost'      => 'danger',
                        'abandoned' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'won'       => 'Gagné',
                        'lost'      => 'Perdu',
                        'abandoned' => 'Abandonné',
                        default     => 'En cours',
                    }),

                IconColumn::make('is_consortium')
                    ->label('Groupement')
                    ->boolean(),

                TextColumn::make('project.code')
                    ->label('Projet')
                    ->badge()
                    ->color('success')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => OfferResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

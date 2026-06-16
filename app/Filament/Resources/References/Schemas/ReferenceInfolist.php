<?php

namespace App\Filament\Resources\References\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReferenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations Principales')
                ->schema([
                    TextEntry::make('title')
                        ->label('Titre')
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        TextEntry::make('client_name')
                            ->label('Client'),

                        TextEntry::make('country')
                            ->label('Pays'),

                        TextEntry::make('year')
                            ->label('Année')
                            ->badge()
                            ->color('gray'),
                    ]),

                    Grid::make(2)->schema([
                        TextEntry::make('domains')
                            ->label('Domaines')
                            ->badge()
                            ->separator(','),

                        TextEntry::make('contract_value')
                            ->label('Valeur du contrat')
                            ->money('EUR')
                            ->placeholder('Non renseignée'),
                    ]),

                    TextEntry::make('description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->placeholder('Aucune description.'),
                ])->columns(1),

            Section::make('Projet & Statut')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('project.title')
                            ->label('Projet lié')
                            ->placeholder('Aucun projet lié'),

                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'active'   => 'success',
                                'archived' => 'gray',
                                default    => 'secondary',
                            }),
                    ]),

                    TextEntry::make('file_path')
                        ->label('Fichier de référence')
                        ->icon('heroicon-o-document')
                        ->iconColor('primary')
                        ->url(fn($state) => $state ? asset('storage/' . $state) : null, true)
                        ->placeholder('Aucun fichier'),
                ]),
        ]);
    }
}

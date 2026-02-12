<?php

namespace App\Filament\Resources\Postes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PosteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails du poste')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Intitulé')
                            ->weight('bold')
                            ->icon('heroicon-m-briefcase'),
                        TextEntry::make('department.name')
                            ->label('Département')
                            ->badge()
                            ->color('gray')
                            ->icon('heroicon-m-building-office'),
                        TextEntry::make('users_count')
                            ->state(fn($record) => $record->users()->count())
                            ->badge()
                            ->label('Utilisateurs assignés')
                            ->color('success'),
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('Aucune description disponible')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}

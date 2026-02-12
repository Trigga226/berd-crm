<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nom')
                            ->weight('bold')
                            ->icon('heroicon-m-building-office'),
                        TextEntry::make('postes_count')
                            ->state(fn($record) => $record->postes()->count())
                            ->badge()
                            ->label('Nombre de postes'),
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

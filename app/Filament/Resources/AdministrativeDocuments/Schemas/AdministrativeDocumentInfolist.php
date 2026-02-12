<?php

namespace App\Filament\Resources\AdministrativeDocuments\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class AdministrativeDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informations générales')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Titre')
                            ->icon('heroicon-m-document-text')
                            ->weight('bold'),
                        TextEntry::make('category')
                            ->label('Catégorie')
                            ->badge(),
                        TextEntry::make('expiration_date')
                            ->label('Expiration')
                            ->date('d/m/Y')
                            ->badge()
                            ->color(fn($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success'),
                        TextEntry::make('file_path')
                            ->label('Fichier')
                            ->formatStateUsing(fn() => 'Ouvrir le PDF')
                            ->url(fn($record) => asset('storage/' . $record->file_path))
                            ->openUrlInNewTab()
                            ->icon('heroicon-m-arrow-top-right-on-square')
                            ->color('primary'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('Aucune description')
                            ->columnSpanFull(),
                    ])->columns(2),

                ComponentsSection::make('Détails de la référence')
                    ->schema([
                        TextEntry::make('additional_info.date_obtention')
                            ->label("Date d'obtention")
                            ->date('d/m/Y')
                            ->icon('heroicon-m-calendar-days'),
                        TextEntry::make('additional_info.domaines')
                            ->label('Domaines')
                            ->badge()
                            ->separator(','),
                    ])
                    ->visible(fn($record) => $record->category === 'References')
                    ->columns(2),
            ]);
    }
}

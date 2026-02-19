<?php

namespace App\Filament\Resources\Archives\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArchiveInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de l\'Archive')
                    ->schema([
                        TextEntry::make('titre')
                            ->label('Titre'),
                        TextEntry::make('type')
                            ->label('Type d\'archive')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Avis de manifestation' => 'warning',
                                'Manifestation' => 'info',
                                'Offres' => 'success',
                                'Livrable' => 'primary',
                                'Rapport' => 'gray',
                                'Document administratif' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('date_archive')
                            ->label('Date d\'archivage')
                            ->date(),
                        TextEntry::make('archive_par')
                            ->label('Archivé par'),
                        TextEntry::make('resultat')
                            ->label('Résultat')
                            ->visible(fn($record) => in_array($record->type, ['Manifestation', 'Offres']))
                            ->placeholder('N/A'),
                        TextEntry::make('observation')
                            ->label('Observation')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Documents Archive')
                    ->schema([
                        TextEntry::make('fichier')
                            ->label('Fichiers joints')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->formatStateUsing(fn($state) => basename($state))
                            ->icon('heroicon-o-document')
                            ->iconColor('primary')
                            ->color('primary')
                            ->url(fn($state) => asset('storage/' . $state), true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

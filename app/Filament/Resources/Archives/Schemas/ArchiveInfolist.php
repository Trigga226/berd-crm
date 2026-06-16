<?php

namespace App\Filament\Resources\Archives\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Slimani\MediaManager\Infolists\Components\MediaFileEntry;

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
                                'Manifestation'          => 'info',
                                'Offres'                 => 'success',
                                'Livrable'               => 'primary',
                                'Rapport'                => 'gray',
                                'Document administratif' => 'danger',
                                default                  => 'secondary',
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
                        MediaFileEntry::make('documents')
                            ->label('Fichiers joints')
                            ->state(fn($record) => $record->mediaFiles)
                            ->placeholder('Aucun document attaché.')
                            ->width('130px')
                            ->height('130px')
                            ->columnSpanFull(),

                        // Compatibilité ascendante : anciens fichiers stockés en colonne JSON
                        TextEntry::make('fichier')
                            ->label('Anciens fichiers (héritage)')
                            ->visible(fn($record) => !empty($record->fichier))
                            ->state(fn($record) => collect($record->fichier ?? [])
                                ->map(fn($f) => is_array($f) ? ($f['chemin'] ?? null) : $f)
                                ->filter()
                                ->values()
                                ->toArray()
                            )
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->formatStateUsing(fn($state) => is_string($state) ? basename($state) : '—')
                            ->icon('heroicon-o-document')
                            ->iconColor('gray')
                            ->color('gray')
                            ->url(fn($state) => is_string($state) ? asset('storage/' . $state) : null, true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

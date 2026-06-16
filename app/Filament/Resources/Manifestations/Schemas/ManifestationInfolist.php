<?php

namespace App\Filament\Resources\Manifestations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Slimani\MediaManager\Infolists\Components\MediaFileEntry;

class ManifestationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informations Générales')
                    ->schema([
                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Statut')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'draft'     => 'gray',
                                        'submitted' => 'info',
                                        'won'       => 'success',
                                        'lost'      => 'danger',
                                        'abandoned' => 'warning',
                                        default     => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'draft'     => 'Brouillon',
                                        'submitted' => 'Soumis',
                                        'won'       => 'Gagné',
                                        'lost'      => 'Perdu',
                                        'abandoned' => 'Abandonné',
                                        default     => $state,
                                    }),

                                TextEntry::make('score')
                                    ->label('Score IA')
                                    ->suffix('/10')
                                    ->color(fn($state) => $state === null ? 'gray' : ($state >= 7 ? 'success' : ($state >= 4 ? 'warning' : 'danger')))
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('—'),

                                TextEntry::make('country')
                                    ->label('Pays')
                                    ->placeholder('—'),
                            ]),

                        TextEntry::make('avisManifestation.title')
                            ->label('Titre (Avis de Manifestation)')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull()
                            ->placeholder('—'),

                        ComponentsGrid::make(2)->columnSpanFull()
                            ->schema([
                                TextEntry::make('client_name')
                                    ->label('Client')
                                    ->placeholder('—'),

                                TextEntry::make('avisManifestation.reference_number')
                                    ->label('N° Référence')
                                    ->copyable()
                                    ->placeholder('—'),
                            ]),
                    ]),

                ComponentsSection::make('Dates & Soumission')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('deadline')
                            ->label('Date Limite')
                            ->dateTime('d/m/Y H:i')
                            ->color(fn($record) => $record->deadline && $record->deadline <= now() ? 'danger' : 'success')
                            ->placeholder('—'),

                        TextEntry::make('internal_control_date')
                            ->label('Contrôle Interne')
                            ->dateTime('d/m/Y H:i')
                            ->color('warning')
                            ->placeholder('—'),

                        TextEntry::make('submission_mode')
                            ->label('Mode de Soumission')
                            ->badge()
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'online'   => 'En ligne',
                                'physical' => 'Physique',
                                'email'    => 'Email',
                                default    => $state ?? '—',
                            })
                            ->placeholder('—'),

                        TextEntry::make('result')
                            ->label('Résultat')
                            ->badge()
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'won'       => 'Gagné',
                                'lost'      => 'Perdu',
                                'abandoned' => 'Abandonné',
                                'submitted' => 'Soumis',
                                default     => $state ?? '—',
                            })
                            ->color(fn($state) => match ($state) {
                                'won'       => 'success',
                                'lost'      => 'danger',
                                'abandoned' => 'warning',
                                'submitted' => 'info',
                                default     => 'gray',
                            })
                            ->placeholder('—'),

                        TextEntry::make('is_groupement')
                            ->label('Groupement / Consortium')
                            ->formatStateUsing(fn($state) => $state ? 'Oui' : 'Non')
                            ->badge()
                            ->color(fn($state) => $state ? 'info' : 'gray'),

                        TextEntry::make('leadPartner.name')
                            ->label('Partenaire Chef de File')
                            ->placeholder('—'),
                    ]),

                ComponentsSection::make('Domaines')
                    ->schema([
                        TextEntry::make('domains')
                            ->label('')
                            ->badge()
                            ->separator(',')
                            ->color('primary')
                            ->columnSpanFull()
                            ->placeholder('Aucun domaine renseigné'),
                    ]),

                ComponentsSection::make('Experts Mobiles')
                    ->schema([
                        TextEntry::make('experts')
                            ->label('')
                            ->state(fn($record) => $record->experts->map(fn($e) => $e->first_name . ' ' . $e->last_name)->join(', '))
                            ->columnSpanFull()
                            ->placeholder('Aucun expert assigné'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Partenaires')
                    ->schema([
                        TextEntry::make('partners')
                            ->label('')
                            ->state(fn($record) => $record->partners->map(fn($p) => $p->name)->join(', '))
                            ->columnSpanFull()
                            ->placeholder('Aucun partenaire assigné'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Notes & Observations')
                    ->schema([
                        TextEntry::make('note')
                            ->label('Note interne')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make('observation')
                            ->label('Observation')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Médiathèque')
                    ->schema([
                        MediaFileEntry::make('documents')
                            ->label('Fichiers joints')
                            ->state(fn($record) => $record->mediaFiles)
                            ->placeholder('Aucun document attaché.')
                            ->width('130px')
                            ->height('130px')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}

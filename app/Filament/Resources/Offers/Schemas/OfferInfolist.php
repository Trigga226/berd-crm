<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Slimani\MediaManager\Infolists\Components\MediaFileEntry;

class OfferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informations Générales')
                    ->schema([
                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('result')
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

                                TextEntry::make('is_consortium')
                                    ->label('Groupement')
                                    ->formatStateUsing(fn($state) => $state ? 'Oui' : 'Non')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'info' : 'gray'),

                                TextEntry::make('general_note')
                                    ->label('Note Générale')
                                    ->suffix('/10')
                                    ->color(fn($state) => $state === null ? 'gray' : ($state >= 7 ? 'success' : ($state >= 4 ? 'warning' : 'danger')))
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('—'),
                            ]),

                        TextEntry::make('title')
                            ->label('Titre')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('client.name')
                                    ->label('Client')
                                    ->placeholder('—'),

                                TextEntry::make('country')
                                    ->label('Pays')
                                    ->placeholder('—'),

                                TextEntry::make('submission_mode')
                                    ->label('Mode de Soumission')
                                    ->badge()
                                    ->placeholder('—'),
                            ]),
                    ]),

                ComponentsSection::make('Manifestation liée')
                    ->schema([
                        TextEntry::make('manifestation.avisManifestation.title')
                            ->label('Titre de la Manifestation')
                            ->placeholder('Aucune manifestation liée'),

                        TextEntry::make('manifestation.status')
                            ->label('Statut Manifestation')
                            ->badge()
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'draft'     => 'Brouillon',
                                'submitted' => 'Soumis',
                                'won'       => 'Gagné',
                                'lost'      => 'Perdu',
                                'abandoned' => 'Abandonné',
                                default     => $state ?? '—',
                            })
                            ->color(fn($state) => match ($state) {
                                'won'       => 'success',
                                'lost'      => 'danger',
                                'submitted' => 'info',
                                'abandoned' => 'warning',
                                default     => 'gray',
                            })
                            ->placeholder('—'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                ComponentsSection::make('Offre Technique')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('technicalOffer.result')
                            ->label('Résultat')
                            ->badge()
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'won'       => 'Gagné',
                                'lost'      => 'Perdu',
                                'abandoned' => 'Abandonné',
                                default     => 'En cours',
                            })
                            ->color(fn(?string $state): string => match ($state) {
                                'won'       => 'success',
                                'lost'      => 'danger',
                                'abandoned' => 'warning',
                                default     => 'gray',
                            })
                            ->placeholder('—'),

                        TextEntry::make('technicalOffer.submission_mode')
                            ->label('Mode Soumission')
                            ->badge()
                            ->placeholder('—'),

                        TextEntry::make('technicalOffer.deadline')
                            ->label('Date Limite')
                            ->dateTime('d/m/Y H:i')
                            ->color(fn($record) => $record->technicalOffer?->deadline && $record->technicalOffer->deadline <= now() ? 'danger' : 'success')
                            ->placeholder('—'),

                        TextEntry::make('technicalOffer.internal_control_date')
                            ->label('Contrôle Interne')
                            ->dateTime('d/m/Y H:i')
                            ->color('warning')
                            ->placeholder('—'),

                        TextEntry::make('technicalOffer.submission_date')
                            ->label('Date de Soumission')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),

                        TextEntry::make('technicalOffer.note')
                            ->label('Note interne')
                            ->placeholder('—'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Offre Financière')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('financialOffer.result')
                            ->label('Résultat')
                            ->badge()
                            ->formatStateUsing(fn(?string $state): string => match ($state) {
                                'won'       => 'Gagné',
                                'lost'      => 'Perdu',
                                'abandoned' => 'Abandonné',
                                default     => 'En cours',
                            })
                            ->color(fn(?string $state): string => match ($state) {
                                'won'       => 'success',
                                'lost'      => 'danger',
                                'abandoned' => 'warning',
                                default     => 'gray',
                            })
                            ->placeholder('—'),

                        TextEntry::make('financialOffer.submission_mode')
                            ->label('Mode Soumission')
                            ->badge()
                            ->placeholder('—'),

                        TextEntry::make('financialOffer.deadline')
                            ->label('Date Limite')
                            ->dateTime('d/m/Y H:i')
                            ->color(fn($record) => $record->financialOffer?->deadline && $record->financialOffer->deadline <= now() ? 'danger' : 'success')
                            ->placeholder('—'),

                        TextEntry::make('financialOffer.internal_control_date')
                            ->label('Contrôle Interne')
                            ->dateTime('d/m/Y H:i')
                            ->color('warning')
                            ->placeholder('—'),

                        TextEntry::make('financialOffer.submission_date')
                            ->label('Date de Soumission')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),

                        TextEntry::make('financialOffer.note')
                            ->label('Note interne')
                            ->placeholder('—'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Projet lié')
                    ->schema([
                        TextEntry::make('project.code')
                            ->label('Code Projet')
                            ->copyable()
                            ->placeholder('Aucun projet créé'),

                        TextEntry::make('project.title')
                            ->label('Titre du Projet')
                            ->placeholder('—'),

                        TextEntry::make('project.status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'preparation' => 'gray',
                                'ongoing'     => 'info',
                                'suspended'   => 'warning',
                                'completed'   => 'success',
                                'cancelled'   => 'danger',
                                default       => 'gray',
                            })
                            ->placeholder('—'),

                        TextEntry::make('project.execution_percentage')
                            ->label('Avancement')
                            ->suffix('%')
                            ->placeholder('—'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->visible(fn($record) => $record->project !== null),

                ComponentsSection::make('Partenaires')
                    ->schema([
                        TextEntry::make('partners')
                            ->label('')
                            ->state(fn($record) => $record->partners->map(fn($p) => $p->name)->join(', '))
                            ->columnSpanFull()
                            ->placeholder('Aucun partenaire assigné'),
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

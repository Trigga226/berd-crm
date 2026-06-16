<?php

namespace App\Filament\Resources\Experts\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section as ComponentsSection;

class ExpertInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informations Personnelles')
                    ->schema([
                        TextEntry::make('first_name')->label('Prénom'),
                        TextEntry::make('last_name')->label('Nom')->weight('bold'),
                        TextEntry::make('email')->label('Email')->icon('heroicon-m-envelope')->copyable(),
                        TextEntry::make('phone')->label('Téléphone')->icon('heroicon-m-phone'),
                        TextEntry::make('years_of_experience')->label("Années d'expérience")->badge()->color('success'),
                        TextEntry::make('rating')
                            ->label('Note interne')
                            ->formatStateUsing(fn(?int $state): string => $state ? str_repeat('⭐', $state) . " ({$state}/5)" : '—')
                            ->placeholder('Non noté'),
                        TextEntry::make('won_manifestations_count')
                            ->label('Manifestations gagnées')
                            ->badge()
                            ->color('success'),
                    ])->columns(3),

                ComponentsSection::make('CV')
                    ->schema([
                        TextEntry::make('cv_path')
                            ->label('CV Principal')
                            ->formatStateUsing(fn() => 'Télécharger le CV')
                            ->url(fn($record) => asset('storage/' . $record->cv_path))
                            ->openUrlInNewTab()
                            ->icon('heroicon-m-document-text')
                            ->color('primary'),

                        \Filament\Infolists\Components\RepeatableEntry::make('cv_paths')
                            ->label('CVs multi-langues')
                            ->schema([
                                TextEntry::make('lang')
                                    ->label('Langue')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'fr' => '🇫🇷 Français',
                                        'en' => '🇬🇧 Anglais',
                                        'pt' => '🇵🇹 Portugais',
                                        'ar' => '🇩🇿 Arabe',
                                        default => $state,
                                    }),
                                TextEntry::make('label')->label('Intitulé')->placeholder('—'),
                                TextEntry::make('path')
                                    ->label('Fichier')
                                    ->formatStateUsing(fn() => 'Télécharger')
                                    ->url(fn($state) => $state ? asset('storage/' . $state) : null)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-m-document-text')
                                    ->color('primary'),
                            ])
                            ->grid(3)
                            ->columnSpanFull()
                            ->visible(fn($record) => !empty($record->cv_paths)),
                    ]),

                ComponentsSection::make('Compétences & Parcours')
                    ->schema([
                        TextEntry::make('skills')
                            ->label('Compétences')
                            ->badge()
                            ->columnSpanFull(),

                        \Filament\Infolists\Components\RepeatableEntry::make('experiences')
                            ->label('Expériences Professionnelles')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('poste')->label('Poste')->weight('bold'),
                                \Filament\Infolists\Components\TextEntry::make('entreprise')->label('Entreprise'),
                                \Filament\Infolists\Components\TextEntry::make('duree')->label('Durée'),
                            ])
                            ->grid(2)
                            ->columnSpanFull(),

                        \Filament\Infolists\Components\RepeatableEntry::make('formations')
                            ->label('Formations')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('diplome')->label('Diplôme')->weight('bold'),
                                \Filament\Infolists\Components\TextEntry::make('ecole')->label('École'),
                                \Filament\Infolists\Components\TextEntry::make('date_obtention')->label("Date"),
                            ])
                            ->grid(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

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
                    ])->columns(3),

                ComponentsSection::make('CV')
                    ->schema([
                        TextEntry::make('cv_path')
                            ->label('Fichier CV')
                            ->formatStateUsing(fn() => 'Télécharger le CV')
                            ->url(fn($record) => asset('storage/' . $record->cv_path))
                            ->openUrlInNewTab()
                            ->icon('heroicon-m-document-text')
                            ->color('primary'),
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

<?php

namespace App\Filament\Resources\Archives\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Slimani\MediaManager\Form\MediaPicker;

class ArchiveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de l\'Archive')
                    ->schema([
                        TextInput::make('titre')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Type d\'archive')
                            ->options([
                                'Avis de manifestation' => 'Avis de manifestation',
                                'Manifestation'          => 'Manifestation',
                                'Offres'                 => 'Offres',
                                'Livrable'               => 'Livrable',
                                'Rapport'                => 'Rapport',
                                'Document administratif' => 'Document administratif',
                                'Autre'                  => 'Autre',
                            ])
                            ->required()
                            ->searchable()
                            ->live(),
                        TextInput::make('annee')
                            ->label('Année')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->default(now()->year)
                            ->required(),
                        Select::make('domaine')
                            ->label('Domaine')
                            ->options(\App\Utils\Domaines::getOptions())
                            ->searchable()
                            ->live(),
                        DatePicker::make('date_archive')
                            ->label('Date d\'archivage')
                            ->required()
                            ->default(now())
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('archive_par')
                            ->label('Archivé par')
                            ->default(fn() => Auth::user()?->email)
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255),
                        Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'archived' => 'Archivé',
                                'draft'    => 'Brouillon',
                                'restaure' => 'Restauré',
                            ])
                            ->default('archived')
                            ->required(),
                        TextInput::make('resultat')
                            ->label('Résultat (Optionnel)')
                            ->visible(fn(Get $get) => in_array($get('type'), ['Manifestation', 'Offres', 'Livrable']))
                            ->maxLength(255),
                        Textarea::make('observation')
                            ->label('Observation')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Documents')
                    ->description('Sélectionnez des fichiers depuis la médiathèque ou déposez-en de nouveaux. Les fichiers sont organisés par type / année / domaine.')
                    ->schema([
                        MediaPicker::make('mediaFiles')
                            ->label('Documents archivés')
                            ->relationship('mediaFiles')
                            ->multiple()
                            ->directory(fn(Get $get) =>
                                'Archives/'
                                . ($get('type') ?? 'Autre') . '/'
                                . ($get('annee') ?? now()->year) . '/'
                                . ($get('domaine') ?? 'General')
                            )
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'application/zip',
                                'application/x-rar-compressed',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

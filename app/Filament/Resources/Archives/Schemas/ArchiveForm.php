<?php

namespace App\Filament\Resources\Archives\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
                                'Manifestation' => 'Manifestation',
                                'Offres' => 'Offres',
                                'Livrable' => 'Livrable',
                                'Rapport' => 'Rapport',
                                'Document administratif' => 'Document administratif',
                                'Autre' => 'Autre',
                            ])
                            ->required()
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
                        TextInput::make('resultat')
                            ->label('Résultat (Optionnel)')
                            ->visible(fn(\Filament\Schemas\Components\Utilities\Get $get) => in_array($get('type'), ['Manifestation', 'Offres']))
                            ->maxLength(255),
                        Textarea::make('observation')
                            ->label('Observation')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Fichiers')
                    ->schema([
                        FileUpload::make('fichier')
                            ->label('Documents')
                            ->multiple()
                            ->directory(function (\Filament\Schemas\Components\Utilities\Get $get) {
                                $type = $get('type') ?? 'Divers';
                                $typeSlug = Str::slug($type);
                                return "archives/{$typeSlug}";
                            })
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

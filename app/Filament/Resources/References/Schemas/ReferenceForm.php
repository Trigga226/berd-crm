<?php

namespace App\Filament\Resources\References\Schemas;

use App\Utils\Domaines;
use App\Utils\Pays;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReferenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations Principales')
                ->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Grid::make(3)->schema([
                        TextInput::make('client_name')
                            ->label('Nom du client')
                            ->required()
                            ->maxLength(255),

                        Select::make('country')
                            ->label('Pays')
                            ->options(Pays::$LISTEPAYS)
                            ->searchable(),

                        TextInput::make('year')
                            ->label('Année')
                            ->numeric()
                            ->required()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->default(now()->year),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('domains')
                            ->label('Domaines')
                            ->options(Domaines::getOptions())
                            ->multiple()
                            ->searchable()
                            ->required(),

                        TextInput::make('contract_value')
                            ->label('Valeur du contrat (€)')
                            ->numeric()
                            ->minValue(0),
                    ]),

                    Select::make('project_id')
                        ->label('Projet lié')
                        ->relationship('project', 'title')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Fichiers & Statut')
                ->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('file_path')
                            ->label('Fichier de référence')
                            ->directory('references')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->downloadable(),

                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'active'   => 'Active',
                                'archived' => 'Archivée',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                ]),
        ]);
    }
}

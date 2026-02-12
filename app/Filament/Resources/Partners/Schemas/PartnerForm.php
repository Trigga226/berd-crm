<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;
use App\utils\Pays;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Partenaire')
                    ->tabs([
                        Tabs\Tab::make('Identité & Coordonnées')
                            ->schema([
                                Section::make('Type de Partenaire')
                                    ->schema([
                                        Radio::make('type')
                                            ->options([
                                                'physique' => 'Personne Physique',
                                                'morale' => 'Personne Morale (Entreprise)',
                                            ])
                                            ->default('physique')
                                            ->required()
                                            ->inline()
                                            ->live(),
                                    ]),

                                Section::make('Identité')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(fn(Get $get) => $get('type') === 'morale' ? 'Raison Sociale' : 'Nom')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('ifu')
                                            ->label('IFU / NIF')
                                            ->maxLength(255)
                                            ->visible(fn(Get $get) => $get('type') === 'morale'),

                                        TextInput::make('website')
                                            ->label('Site Web')
                                            ->url()
                                            ->prefix('https://')
                                            ->maxLength(255)
                                            ->visible(fn(Get $get) => $get('type') === 'morale'),

                                        TagsInput::make('domains')
                                            ->label('Domaines de Spécialité')
                                            ->suggestions([
                                                'BTP',
                                                'Informatique',
                                                'Audit',
                                                'Conseil',
                                                'Environnement',
                                            ])
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Section::make('Coordonnées')
                                    ->schema([
                                        TextInput::make('email')
                                            ->email()
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-envelope'),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-m-phone'),
                                        TextInput::make('address')
                                            ->label('Adresse')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        TextInput::make('city')
                                            ->label('Ville')
                                            ->maxLength(255),
                                        Select::make('country')
                                            ->label('Pays')
                                            ->options(Pays::$LISTEPAYS)
                                            ->searchable(),
                                    ])->columns(2),

                                Section::make('Contact Principal')
                                    ->description('Interlocuteur privilégié.')
                                    ->schema([
                                        TextInput::make('contact_name')
                                            ->label('Nom')
                                            ->maxLength(255),
                                        TextInput::make('contact_email')
                                            ->label('Email')
                                            ->email(),
                                        TextInput::make('contact_phone')
                                            ->label('Téléphone')
                                            ->tel(),
                                    ])
                                    ->visible(fn(Get $get) => $get('type') === 'morale')
                                    ->columns(3),
                            ]),

                        Tabs\Tab::make('Documents Administratifs')
                            ->schema([
                                Repeater::make('documents')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Titre du document')
                                            ->required(),
                                        DatePicker::make('expiration_date')
                                            ->label('Date d\'expiration'),
                                        FileUpload::make('file_path')
                                            ->label('Fichier (PDF/Image)')
                                            ->disk('public')
                                            ->directory('partenaire/tmp') // Sera déplacé
                                            ->openable()
                                            ->downloadable()
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter un document'),
                            ]),

                        Tabs\Tab::make('Références')
                            ->schema([
                                Repeater::make('references')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Nom du Projet')
                                            ->required(),
                                        TextInput::make('client_name')
                                            ->label('Client'),
                                        TextInput::make('year')
                                            ->label('Année')
                                            ->numeric()
                                            ->maxLength(4),
                                        TagsInput::make('domains')
                                            ->label('Domaines concernés')
                                            ->columnSpanFull(),
                                        Textarea::make('description')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        FileUpload::make('file_path')
                                            ->label('Attestation / Preuve')
                                            ->disk('public')
                                            ->directory('partenaire/tmp') // Sera déplacé
                                            ->openable()
                                            ->downloadable()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter une référence'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Bailleurs\Schemas;

use App\Models\Bailleur;
use App\Utils\Pays;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BailleurForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité du Bailleur')
                    ->schema([
                        Select::make('type')
                            ->label('Type de bailleur')
                            ->options(Bailleur::TYPES)
                            ->default('multilateral')
                            ->required()
                            ->native(false),

                        TextInput::make('name')
                            ->label('Dénomination')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('acronym')
                            ->label('Sigle')
                            ->placeholder('BAD, BM, BOAD, UE…')
                            ->maxLength(50),

                        TextInput::make('ifu')
                            ->label('IFU / NIF')
                            ->maxLength(255),

                        TextInput::make('website')
                            ->label('Site Web')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255),

                        TagsInput::make('domains')
                            ->label('Domaines / Secteurs financés')
                            ->suggestions([
                                'Eau & Assainissement',
                                'Infrastructures',
                                'Énergie',
                                'Agriculture',
                                'Environnement',
                                'Éducation',
                                'Santé',
                                'Gouvernance',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

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
                    ])
                    ->columns(2),

                Section::make('Contact Principal')
                    ->description('Interlocuteur privilégié auprès du bailleur.')
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
                    ->columns(3)
                    ->collapsible(),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

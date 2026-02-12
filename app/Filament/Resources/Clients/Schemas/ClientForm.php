<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\utils\Pays;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    public static function getComponents(): array
    {
        return [
            Section::make('Type de Client')
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

                    TextInput::make('first_name')
                        ->label('Prénom')
                        ->maxLength(255)
                        ->visible(fn(Get $get) => $get('type') === 'physique'),

                    TextInput::make('ifu')
                        ->label('IFU (Identifiant Fiscal Unique)')
                        ->maxLength(255)
                        ->visible(fn(Get $get) => $get('type') === 'morale'),

                    TextInput::make('website')
                        ->label('Site Web')
                        ->url()
                        ->prefix('https://')
                        ->maxLength(255)
                        ->visible(fn(Get $get) => $get('type') === 'morale'),
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
                ->description('Interlocuteur privilégié pour cette entreprise.')
                ->schema([
                    TextInput::make('contact_name')
                        ->label('Nom du contact')
                        ->maxLength(255),
                    TextInput::make('contact_email')
                        ->label('Email du contact')
                        ->email(),
                    TextInput::make('contact_phone')
                        ->label('Téléphone du contact')
                        ->tel(),
                ])
                ->visible(fn(Get $get) => $get('type') === 'morale')
                ->columns(3),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->collapsible(),
        ];
    }
}

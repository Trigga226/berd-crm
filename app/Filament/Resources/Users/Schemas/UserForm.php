<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        FileUpload::make('photo')->directory("users")->disk("public")->label("Photo")->avatar()->preserveFilenames()->columnSpanFull(),
                        TextInput::make('name')
                            ->required()->label("Nom complet")
                            ->prefixIcon('heroicon-m-user'),
                        TextInput::make('num_poste')->label("Numéro de poste")
                            ->prefixIcon('heroicon-m-phone'),
                        TextInput::make('num_perso')->label("Numéro personnel")
                            ->prefixIcon('heroicon-m-device-phone-mobile'),
                        Select::make('poste_id')
                            ->relationship('poste', 'title')
                            ->searchable()
                            ->preload()
                            ->label('Poste')
                            ->prefixIcon('heroicon-m-briefcase'),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->prefixIcon('heroicon-m-envelope'),
                        Select::make('roles')
                            ->relationship('roles', 'name', modifyQueryUsing: fn($query) => $query->where('name', '!=', 'super_admin'))
                            ->preload()
                            ->searchable()
                            ->prefixIcon('heroicon-m-shield-check'),
                        TextInput::make('password')->revealable()->visibleOn('create')
                            ->password()->label("Mot de passe")
                            ->required()
                            ->prefixIcon('heroicon-m-key'),
                    ])->columnSpanFull()->columns(3)
            ]);
    }
}

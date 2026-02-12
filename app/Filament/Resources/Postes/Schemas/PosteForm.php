<?php

namespace App\Filament\Resources\Postes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PosteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Intitulé du poste')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-briefcase'),
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Département')
                            ->prefixIcon('heroicon-m-building-office'),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}

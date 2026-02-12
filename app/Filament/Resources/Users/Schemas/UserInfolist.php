<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        ImageEntry::make('photo')
                            ->label('Photo')
                            ->circular()
                            ->columnSpanFull(),

                        TextEntry::make('name')
                            ->label('Nom complet'),

                        TextEntry::make('email')
                            ->label('Adresse E-mail')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),

                        TextEntry::make('roles.name')
                            ->label('Rôles')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'admin' => 'danger',
                                'manager' => 'warning',
                                default => 'success',
                            }),

                        TextEntry::make('num_poste')
                            ->label('Numéro de poste')
                            ->placeholder('-'),

                        TextEntry::make('num_perso')
                            ->label('Numéro personnel')
                            ->placeholder('-'),

                        
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}

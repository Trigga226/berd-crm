<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PartnerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informations Générales')
                    ->schema([
                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Type')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'public'  => 'info',
                                        'private' => 'success',
                                        'ngo'     => 'warning',
                                        'ong'     => 'warning',
                                        default   => 'gray',
                                    })
                                    ->placeholder('—'),

                                TextEntry::make('country')
                                    ->label('Pays')
                                    ->placeholder('—'),

                                TextEntry::make('city')
                                    ->label('Ville')
                                    ->placeholder('—'),
                            ]),

                        TextEntry::make('name')
                            ->label('Nom du Partenaire')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                        TextEntry::make('ifu')
                            ->label('IFU')
                            ->copyable()
                            ->placeholder('—'),
                    ]),

                ComponentsSection::make('Coordonnées')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('phone')
                            ->label('Téléphone')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('address')
                            ->label('Adresse')
                            ->columnSpanFull()
                            ->placeholder('—'),

                        TextEntry::make('website')
                            ->label('Site Web')
                            ->url(fn($state) => $state ? 'https://' . ltrim($state, 'https://') : null)
                            ->openUrlInNewTab()
                            ->placeholder('—'),
                    ]),

                ComponentsSection::make('Domaines de Spécialité')
                    ->schema([
                        TextEntry::make('domains')
                            ->label('')
                            ->badge()
                            ->separator(',')
                            ->color('primary')
                            ->columnSpanFull()
                            ->placeholder('Aucun domaine renseigné'),
                    ]),

                ComponentsSection::make('Contact Principal')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('contact_name')
                            ->label('Nom du Contact')
                            ->placeholder('—'),

                        TextEntry::make('contact_email')
                            ->label('Email')
                            ->copyable()
                            ->placeholder('—'),

                        TextEntry::make('contact_phone')
                            ->label('Téléphone')
                            ->copyable()
                            ->placeholder('—'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Activité BERD')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('manifestations')
                            ->label('Manifestations associées')
                            ->state(fn($record) => $record->manifestations->count() . ' manifestation(s)')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('references')
                            ->label('Références partenaires')
                            ->state(fn($record) => $record->references->count() . ' référence(s)')
                            ->badge()
                            ->color('success'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->columnSpanFull()
                            ->placeholder('Aucune note'),
                    ])
                    ->collapsible(),
            ]);
    }
}

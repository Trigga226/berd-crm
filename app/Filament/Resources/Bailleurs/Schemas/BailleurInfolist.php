<?php

namespace App\Filament\Resources\Bailleurs\Schemas;

use App\Models\Bailleur;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class BailleurInfolist
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
                                    ->formatStateUsing(fn(?string $state): string => Bailleur::TYPES[$state] ?? '—')
                                    ->color(fn(?string $state): string => match ($state) {
                                        'multilateral' => 'info',
                                        'bilateral'    => 'success',
                                        'national'     => 'warning',
                                        'fondation'    => 'primary',
                                        default        => 'gray',
                                    }),

                                TextEntry::make('acronym')
                                    ->label('Sigle')
                                    ->badge()
                                    ->color('primary')
                                    ->placeholder('—'),

                                TextEntry::make('country')
                                    ->label('Pays')
                                    ->placeholder('—'),
                            ]),

                        TextEntry::make('name')
                            ->label('Dénomination')
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

                ComponentsSection::make('Domaines / Secteurs financés')
                    ->schema([
                        TextEntry::make('domains')
                            ->label('')
                            ->badge()
                            ->separator(',')
                            ->color('primary')
                            ->columnSpanFull()
                            ->placeholder('Aucun secteur renseigné'),
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
                    ->schema([
                        TextEntry::make('projects')
                            ->label('Projets financés')
                            ->state(fn($record) => $record->projects->count() . ' projet(s)')
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

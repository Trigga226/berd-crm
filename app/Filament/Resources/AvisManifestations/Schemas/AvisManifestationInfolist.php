<?php

namespace App\Filament\Resources\AvisManifestations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class AvisManifestationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Informations Générales')
                    ->schema([
                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('status')
                                    ->label('Statut')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'open'    => 'success',
                                        'closed'  => 'danger',
                                        'draft'   => 'gray',
                                        default   => 'gray',
                                    })
                                    ->placeholder('—'),

                                TextEntry::make('ai_score')
                                    ->label('Score IA')
                                    ->suffix('/10')
                                    ->color(fn($state) => $state === null ? 'gray' : ($state >= 7 ? 'success' : ($state >= 4 ? 'warning' : 'danger')))
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('—'),

                                TextEntry::make('deadline')
                                    ->label('Date Limite')
                                    ->dateTime('d/m/Y H:i')
                                    ->color(fn($record) => $record->deadline && $record->deadline <= now() ? 'danger' : 'success')
                                    ->placeholder('—'),
                            ]),

                        TextEntry::make('title')
                            ->label('Titre')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),

                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('reference_number')
                                    ->label('N° Référence')
                                    ->copyable()
                                    ->placeholder('—'),

                                TextEntry::make('client.name')
                                    ->label('Client')
                                    ->placeholder(fn($record) => $record->client_name ?? '—'),

                                TextEntry::make('submission_date')
                                    ->label('Date de Soumission')
                                    ->date('d/m/Y')
                                    ->placeholder('—'),
                            ]),
                    ]),

                ComponentsSection::make('Domaines')
                    ->schema([
                        TextEntry::make('domains')
                            ->label('')
                            ->badge()
                            ->separator(',')
                            ->color('primary')
                            ->columnSpanFull()
                            ->placeholder('Aucun domaine renseigné'),
                    ]),

                ComponentsSection::make('Description')
                    ->schema([
                        TextEntry::make('description')
                            ->label('')
                            ->columnSpanFull()
                            ->placeholder('Aucune description'),
                    ])
                    ->collapsible(),

                ComponentsSection::make('Analyse IA')
                    ->schema([
                        TextEntry::make('ai_summary')
                            ->label('Résumé IA')
                            ->columnSpanFull()
                            ->placeholder('Aucune analyse IA disponible. Utilisez le bouton "Analyser avec l\'IA" sur cette page.'),
                    ])
                    ->collapsible(),
            ]);
    }
}

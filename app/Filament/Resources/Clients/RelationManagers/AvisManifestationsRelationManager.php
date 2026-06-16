<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\AvisManifestations\AvisManifestationResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AvisManifestationsRelationManager extends RelationManager
{
    protected static string $relationship = 'avisManifestations';

    protected static ?string $title = 'Avis de Manifestation';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('reference_number')
                    ->label('Référence')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->limit(45)
                    ->searchable()
                    ->url(fn($record) => AvisManifestationResource::getUrl('view', ['record' => $record])),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'open'   => 'success',
                        'closed' => 'danger',
                        'draft'  => 'gray',
                        default  => 'gray',
                    }),

                TextColumn::make('ai_score')
                    ->label('Score IA')
                    ->suffix('/10')
                    ->sortable(),

                TextColumn::make('deadline')
                    ->label('Date Limite')
                    ->date('d/m/Y')
                    ->color(fn($record) => $record->deadline && $record->deadline <= now() ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->defaultSort('deadline', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => AvisManifestationResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

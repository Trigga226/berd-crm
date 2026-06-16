<?php

namespace App\Filament\Resources\Offers\RelationManagers;

use App\Filament\Resources\Partners\PartnerResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';

    protected static ?string $title = 'Partenaires';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->url(fn($record) => PartnerResource::getUrl('view', ['record' => $record])),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'public'  => 'info',
                        'private' => 'success',
                        'ngo'     => 'warning',
                        'ong'     => 'warning',
                        default   => 'gray',
                    }),

                IconColumn::make('is_lead')
                    ->label('Chef de File')
                    ->state(fn($record) => $record->pivot?->is_lead)
                    ->boolean(),

                TextColumn::make('country')
                    ->label('Pays')
                    ->placeholder('—'),

                TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->placeholder('—'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn($record) => PartnerResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}

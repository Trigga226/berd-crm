<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Nom / Raison Sociale')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                \Filament\Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'physique' => 'Physique',
                        'morale' => 'Morale',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'physique' => 'info',
                        'morale' => 'warning',
                    }),

                \Filament\Tables\Columns\TextColumn::make('ifu')
                    ->label('IFU')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('country')
                    ->label('Pays')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')
                    ->visible(fn($record) => $record && $record->type === 'morale') // N'est pas idéal en colonne, peut-être juste toggleable
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'physique' => 'Personne Physique',
                        'morale' => 'Personne Morale',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('country')
                    ->label('Pays')
                    ->options(\App\utils\Pays::$LISTEPAYS)
                    ->searchable(),
                TrashedFilter::make()->visible(Auth::user()->email==="franck.b@berd-ing.com"),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(Auth::user()->email==="franck.b@berd-ing.com"),
                    RestoreBulkAction::make()->visible(Auth::user()->email==="franck.b@berd-ing.com"),
                ]),
            ]);
    }
}

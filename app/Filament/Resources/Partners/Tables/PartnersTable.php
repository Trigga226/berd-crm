<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Filament\Actions\ExportCsvAction;
use App\Utils\Pays;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PartnersTable
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

                \Filament\Tables\Columns\TextColumn::make('domains')
                    ->label('Spécialités')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->toggleable(),

                \Filament\Tables\Columns\TextColumn::make('country')
                    ->label('Pays')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Docs')
                    ->badge(),

                \Filament\Tables\Columns\TextColumn::make('references_count')
                    ->counts('references')
                    ->label('Refs')
                    ->badge(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'physique' => 'Personne Physique',
                        'morale' => 'Personne Morale',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('country')
                    ->label('Pays')
                    ->options(Pays::$LISTEPAYS)
                    ->searchable(),
                TrashedFilter::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('partenaires.csv')
                    ->columns([
                        'Nom'          => fn($r) => $r->name ?? '',
                        'Type'         => fn($r) => $r->type ?? '',
                        'Email'        => fn($r) => $r->email ?? '',
                        'Téléphone'    => fn($r) => $r->phone ?? '',
                        'Pays'         => fn($r) => $r->country ?? '',
                        'Spécialités'  => fn($r) => implode(', ', $r->domains ?? []),
                        'Références'   => fn($r) => $r->references_count ?? 0,
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ]);
    }
}

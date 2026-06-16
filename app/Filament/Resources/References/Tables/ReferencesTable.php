<?php

namespace App\Filament\Resources\References\Tables;

use App\Filament\Actions\ExportCsvAction;
use App\Utils\Domaines;
use App\Utils\Pays;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReferencesTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn() => Auth::user()?->email === 'franck.b@berd-ing.com';

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(60),

                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country')
                    ->label('Pays')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('year')
                    ->label('Année')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('domains')
                    ->label('Domaines')
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->toggleable(),

                TextColumn::make('contract_value')
                    ->label('Valeur')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active'   => 'success',
                        'archived' => 'gray',
                        default    => 'secondary',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active'   => 'Active',
                        'archived' => 'Archivée',
                    ]),

                SelectFilter::make('year')
                    ->label('Année')
                    ->options(
                        fn() => \App\Models\Reference::query()
                            ->selectRaw('year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->toArray()
                    ),

                SelectFilter::make('country')
                    ->label('Pays')
                    ->options(Pays::$LISTEPAYS)
                    ->searchable(),

                TrashedFilter::make()
                    ->visible($isAdmin),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('references.csv')
                    ->columns([
                        'Titre'          => fn($r) => $r->title ?? '',
                        'Client'         => fn($r) => $r->client_name ?? '',
                        'Pays'           => fn($r) => $r->country ?? '',
                        'Année'          => fn($r) => $r->year ?? '',
                        'Domaines'       => fn($r) => implode(', ', $r->domains ?? []),
                        'Valeur Contrat' => fn($r) => $r->contract_value ?? '',
                        'Statut'         => fn($r) => $r->status ?? '',
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()
                        ->visible($isAdmin),
                    RestoreBulkAction::make()
                        ->visible($isAdmin),
                ]),
            ])
            ->defaultSort('year', 'desc');
    }
}

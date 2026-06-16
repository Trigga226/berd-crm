<?php

namespace App\Filament\Resources\Bailleurs\Tables;

use App\Filament\Actions\ExportCsvAction;
use App\Models\Bailleur;
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

class BailleursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('acronym')
                    ->label('Sigle')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('name')
                    ->label('Dénomination')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => Bailleur::TYPES[$state] ?? '—')
                    ->color(fn(?string $state): string => match ($state) {
                        'multilateral' => 'info',
                        'bilateral'    => 'success',
                        'national'     => 'warning',
                        'fondation'    => 'primary',
                        'prive'        => 'gray',
                        default        => 'gray',
                    }),

                TextColumn::make('domains')
                    ->label('Secteurs')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->searchable(),

                TextColumn::make('country')
                    ->label('Pays')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Projets')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(Bailleur::TYPES),
                SelectFilter::make('country')
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
                    ->filename('bailleurs.csv')
                    ->columns([
                        'Sigle'     => fn($r) => $r->acronym ?? '',
                        'Dénomination' => fn($r) => $r->name ?? '',
                        'Type'      => fn($r) => Bailleur::TYPES[$r->type] ?? '',
                        'Email'     => fn($r) => $r->email ?? '',
                        'Téléphone' => fn($r) => $r->phone ?? '',
                        'Pays'      => fn($r) => $r->country ?? '',
                        'Secteurs'  => fn($r) => implode(', ', $r->domains ?? []),
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ]);
    }
}

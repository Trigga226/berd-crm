<?php

namespace App\Filament\Resources\AvisManifestations\Tables;

use App\Filament\Actions\ExportCsvAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AvisManifestationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('Réf.')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->title),

                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deadline')
                    ->label('Date Limite')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn($state) => $state < now()->addDays(7) ? 'danger' : 'success')
                    ->description(fn($record) => $record->deadline->diffForHumans()),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'submitted_to_analysis' => 'En analyse',
                        'validated' => 'Validé',
                        'rejected' => 'Rejeté',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'submitted_to_analysis' => 'info',
                        'validated' => 'success',
                        'rejected' => 'danger',
                    }),

                TextColumn::make('projectManagers.name')
                    ->label('Chargés de Projet')
                    ->badge()
                    ->separator(',')
                    ->limitList(2),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'submitted_to_analysis' => 'En analyse',
                        'validated' => 'Validé',
                        'rejected' => 'Rejeté',
                    ]),
                SelectFilter::make('projectManagers')
                    ->label('Chargé de Projet')
                    ->options(
                        fn() => \App\Models\User::query()
                            ->select('id', 'name')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->query(function ($query, $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('projectManagers', fn($q) => $q->where('users.id', $data['value']));
                        }
                    }),


                \Filament\Tables\Filters\TrashedFilter::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('avis-manifestations.csv')
                    ->columns([
                        'Référence'    => fn($r) => $r->reference_number ?? '',
                        'Titre'        => fn($r) => $r->title ?? '',
                        'Client'       => fn($r) => $r->client?->name ?? '',
                        'Date Limite'  => fn($r) => $r->deadline?->format('d/m/Y') ?? '',
                        'Statut'       => fn($r) => $r->status ?? '',
                        'Score IA'     => fn($r) => $r->ai_score ?? '',
                        'Domaines'     => fn($r) => implode(', ', $r->domains ?? []),
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ])
            ->defaultSort('deadline', 'asc');
    }
}

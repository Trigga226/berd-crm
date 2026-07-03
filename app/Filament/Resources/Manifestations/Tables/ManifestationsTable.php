<?php

namespace App\Filament\Resources\Manifestations\Tables;

use App\Filament\Actions\ExportCsvAction;
use App\Services\ArchiveService;
use App\Utils\Pays;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ManifestationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('avisManifestation.title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                \Filament\Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('country')
                    ->label('Pays')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('deadline')
                    ->label('Date Limite')
                    ->dateTime()
                    ->sortable()
                    ->color(fn($record) => $record->deadline < now() ? 'danger' : 'success'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft'     => 'Brouillon',
                        'submitted' => 'Soumis',
                        'won'       => 'Gagné',
                        'lost'      => 'Perdu',
                        'abandoned' => 'Abandonné',
                        default     => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'info',
                        'won'       => 'success',
                        'lost'      => 'danger',
                        'abandoned' => 'warning',
                        default     => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('chargesEtudes.name')
                    ->label('Chargés')
                    ->listWithLineBreaks()
                    ->limitList(2),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'submitted' => 'Soumis',
                        'won' => 'Gagné',
                        'lost' => 'Perdu',
                        'abandoned' => 'Abandonné',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('country')
                    ->label('Pays')
                    ->options(Pays::$LISTEPAYS)
                    ->searchable(),
                \Filament\Tables\Filters\SelectFilter::make('submission_mode')
                    ->label('Mode de Dépôt')
                    ->options([
                        'online' => 'En ligne',
                        'physical' => 'Physique',
                        'email' => 'Email',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('domains')
                    ->label('Domaines')
                    ->options(\App\Utils\Domaines::getOptions())
                    ->searchable()
                    ->query(
                        fn(Builder $query, array $data) =>
                        !empty($data['value']) ? $query->whereJsonContains('domains', $data['value']) : $query
                    ),
                \Filament\Tables\Filters\Filter::make('period')
                    ->form([
                        \Filament\Forms\Components\Select::make('value')
                            ->label('Période')
                            ->options([
                                '1_month' => '1 Mois',
                                '3_months' => '3 Mois',
                                '6_months' => '6 Mois',
                                '1_year' => '1 An',
                                '2_years' => '2 Ans',
                                'all' => 'Toutes les années',
                            ])
                            ->default('all'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? 'all';
                        $start = match ($value) {
                            '1_month' => now()->subMonth(),
                            '3_months' => now()->subMonths(3),
                            '6_months' => now()->subMonths(6),
                            '1_year' => now()->subYear(),
                            '2_years' => now()->subYears(2),
                            'all' => null,
                            default => null,
                        };
                        if ($start) {
                            $query->where('created_at', '>=', $start);
                        }
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $value = $data['value'] ?? null;
                        if (! $value || $value === 'all') {
                            return null;
                        }
                        return 'Période: ' . $value;
                    }),
                \Filament\Tables\Filters\Filter::make('score_min')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Note Min')
                            ->numeric()
                            ->maxValue(100),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return !empty($data['value']) ? $query->where('score', '>=', $data['value']) : $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! ($data['value'] ?? null)) {
                            return null;
                        }
                        return 'Note Min: ' . $data['value'];
                    }),
                TrashedFilter::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('archiver')
                    ->label('Archiver')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Archiver cette manifestation ?')
                    ->modalDescription('Tous les documents du dossier seront compilés et indexés dans les Archives.')
                    ->action(function ($record) {
                        app(ArchiveService::class)->archiverManifestation($record);
                        Notification::make()
                            ->success()
                            ->title('Manifestation archivée')
                            ->body('Le dossier a été créé dans Archives / Manifestations.')
                            ->send();
                    })
                    ->visible(fn($record) => in_array($record->status, ['won', 'lost', 'abandoned'])),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('manifestations.csv')
                    ->columns([
                        'Titre'         => fn($r) => $r->avisManifestation?->title ?? '',
                        'Client'        => fn($r) => $r->client_name ?? '',
                        'Pays'          => fn($r) => $r->country ?? '',
                        'Statut'        => fn($r) => match ($r->status ?? '') {
                            'draft' => 'Brouillon', 'submitted' => 'Soumis',
                            'won' => 'Gagné', 'lost' => 'Perdu', 'abandoned' => 'Abandonné',
                            default => $r->status ?? '',
                        },
                        'Date Limite'   => fn($r) => $r->deadline?->format('d/m/Y') ?? '',
                        'Note'          => fn($r) => $r->score ?? '',
                        'Domaines'      => fn($r) => implode(', ', $r->domains ?? []),
                        'Créé le'       => fn($r) => $r->created_at?->format('d/m/Y') ?? '',
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ]);
    }
}

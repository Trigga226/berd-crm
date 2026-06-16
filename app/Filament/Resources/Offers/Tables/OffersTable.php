<?php

namespace App\Filament\Resources\Offers\Tables;

use App\Filament\Actions\ExportCsvAction;
use App\Services\ArchiveService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Utils\Pays;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->label('Titre'),
                TextColumn::make('client.name')->searchable()->sortable()->label('Client'),
                TextColumn::make('result')
                    ->label('Résultat')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'won'       => 'success',
                        'lost'      => 'danger',
                        'abandoned' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'won'       => 'Gagné',
                        'lost'      => 'Perdu',
                        'abandoned' => 'Abandonné',
                        default     => 'En cours',
                    }),
                IconColumn::make('is_consortium')->boolean()->label('Groupement'),
                TextColumn::make('created_at')->dateTime()->label('Créé le'),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->label('Résultat')
                    ->options([
                        'won'       => 'Gagné',
                        'lost'      => 'Perdu',
                        'abandoned' => 'Abandonné',
                        'pending'   => 'En Cours',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        if ($data['value'] === 'pending') {
                            return $query->where(function ($q) {
                                $q->whereNull('result')
                                    ->orWhereNotIn('result', ['won', 'lost', 'abandoned']);
                            });
                        }
                        return $query->where('result', $data['value']);
                    }),
                SelectFilter::make('country')
                    ->label('Pays')
                    ->options(Pays::$LISTEPAYS)
                    ->searchable(),
                SelectFilter::make('is_consortium')
                    ->label('Groupement')
                    ->options([
                        '1' => 'Oui',
                        '0' => 'Non',
                    ])
                    ->query(
                        fn(Builder $query, array $data) =>
                        $query->where('is_consortium', $data['value'] === '1')
                    ),
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Créé à partir du'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Créé jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
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
                    ->modalHeading('Archiver cette offre ?')
                    ->modalDescription('L\'offre technique, l\'offre financière et tous les documents seront compilés dans les Archives.')
                    ->action(function ($record) {
                        app(ArchiveService::class)->archiverOffre($record);
                        Notification::make()
                            ->success()
                            ->title('Offre archivée')
                            ->body('Le dossier a été créé dans Archives / Offres.')
                            ->send();
                    })
                    ->visible(fn($record) => in_array($record->result, ['won', 'lost', 'abandoned'])),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('offres.csv')
                    ->columns([
                        'Titre'           => fn($r) => $r->title ?? '',
                        'Client'          => fn($r) => $r->client?->name ?? '',
                        'Pays'            => fn($r) => $r->country ?? '',
                        'Résultat'        => fn($r) => $r->result ?? '',
                        'Mode Soumission' => fn($r) => $r->submission_mode ?? '',
                        'Groupement'      => fn($r) => $r->is_consortium ? 'Oui' : 'Non',
                        'Créé le'         => fn($r) => $r->created_at?->format('d/m/Y') ?? '',
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ]);
    }
}

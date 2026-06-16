<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Filament\Actions\ExportCsvAction;
use App\Services\ArchiveService;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Utils\Pays;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Support\Facades\Auth;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country')
                    ->label('Pays')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'gray' => 'preparation',
                        'primary' => 'ongoing',
                        'warning' => 'suspended',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'preparation' => 'Préparation',
                        'ongoing' => 'En Cours',
                        'suspended' => 'Suspendu',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                        default => $state,
                    }),
                TextColumn::make('execution_percentage')
                    ->label('% Exécution')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('planned_start_date')
                    ->label('Début Prévu')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('planned_end_date')
                    ->label('Fin Prévue')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('total_budget')
                    ->label('Budget')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('projectManagerUser.name')
                    ->label('Chef de Projet')
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'preparation' => 'Préparation',
                        'ongoing' => 'En Cours',
                        'suspended' => 'Suspendu',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                    ]),
                SelectFilter::make('country')
                    ->label('Pays')
                    ->options(Pays::$LISTEPAYS)
                    ->searchable(),
                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('planned_dates')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('planned_from')
                            ->label('Début prévu à partir du'),
                        \Filament\Forms\Components\DatePicker::make('planned_until')
                            ->label('Fin prévue jusqu\'au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['planned_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('planned_start_date', '>=', $date),
                            )
                            ->when(
                                $data['planned_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('planned_end_date', '<=', $date),
                            );
                    }),

                    TrashedFilter::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('archiver')
                    ->label('Archiver')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Archiver ce projet ?')
                    ->modalDescription('Le contrat, les livrables, les rapports et les avenants seront compilés dans les Archives.')
                    ->action(function ($record) {
                        app(ArchiveService::class)->archiverProjet($record);
                        Notification::make()
                            ->success()
                            ->title('Projet archivé')
                            ->body('Le dossier a été créé dans Archives / Projets.')
                            ->send();
                    })
                    ->visible(fn($record) => in_array($record->status, ['completed', 'cancelled'])),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('projets.csv')
                    ->columns([
                        'Code'             => fn($r) => $r->code ?? '',
                        'Titre'            => fn($r) => $r->title ?? '',
                        'Client'           => fn($r) => $r->client?->name ?? '',
                        'Pays'             => fn($r) => $r->country ?? '',
                        'Statut'           => fn($r) => $r->status ?? '',
                        'Avancement (%)'   => fn($r) => $r->execution_percentage ?? '',
                        'Budget Total'     => fn($r) => $r->total_budget ?? '',
                        'Budget Consommé'  => fn($r) => $r->consumed_budget ?? '',
                        'Début Prévu'      => fn($r) => $r->planned_start_date?->format('d/m/Y') ?? '',
                        'Fin Prévue'       => fn($r) => $r->planned_end_date?->format('d/m/Y') ?? '',
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

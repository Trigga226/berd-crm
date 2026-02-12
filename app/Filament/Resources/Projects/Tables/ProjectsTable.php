<?php

namespace App\Filament\Resources\Projects\Tables;

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
use Filament\Actions\ViewAction;

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
                    ->money('EUR')
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
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

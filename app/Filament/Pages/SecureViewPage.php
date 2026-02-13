<?php

namespace App\Filament\Pages;

use App\Models\SecureView;
use BackedEnum;
use Filament\Actions\Action as ActionsAction;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class SecureViewPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.secure-view-page';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Vues Sécurisées';

    protected static ?string $title = 'Vues Sécurisées';

    public function table(Table $table): Table
    {
        return $table
            ->query(SecureView::query())
            ->columns([
                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->sortable()
                    ->colors([
                        'primary' => 'info',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                TextColumn::make('auteur')
                    ->label('Auteur')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    })
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'info' => 'Info',
                        'success' => 'Succès',
                        'warning' => 'Avertissement',
                        'danger' => 'Danger',
                    ])
                    ->placeholder('Tous les types'),

                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Du')
                            ->placeholder('Date de début'),
                        DatePicker::make('date_to')
                            ->label('Au')
                            ->placeholder('Date de fin'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Du: ' . \Carbon\Carbon::parse($data['date_from'])->format('d/m/Y');
                        }

                        if ($data['date_to'] ?? null) {
                            $indicators[] = 'Au: ' . \Carbon\Carbon::parse($data['date_to'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                SelectFilter::make('auteur')
                    ->label('Auteur')
                    ->options(
                        fn(): array => SecureView::query()
                            ->distinct()
                            ->pluck('auteur', 'auteur')
                            ->toArray()
                    )
                    ->placeholder('Tous les auteurs'),
            ])
            ->actions([
                ActionsAction::make('view')
                    ->label('Voir détails')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn(SecureView $record): string => $record->titre)
                    ->modalContent(fn(SecureView $record): \Illuminate\Contracts\View\View => view(
                        'filament.pages.secure-view-details',
                        ['record' => $record],
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->slideOver(),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}

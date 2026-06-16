<?php

namespace App\Filament\Pages;

use App\Models\Archive;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchiveBrowser extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.archive-browser';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Navigateur d\'Archives';

    protected static \UnitEnum|string|null $navigationGroup = 'Gestion Documentaire';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Navigateur d\'Archives';

    public function table(Table $table): Table
    {
        return $table
            ->query(Archive::query()->with('archiveur'))
            ->columns([
                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(50),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Avis de manifestation' => 'warning',
                        'Manifestation'         => 'info',
                        'Offres'                => 'success',
                        'Livrable'              => 'primary',
                        'Rapport'               => 'gray',
                        default                 => 'secondary',
                    })
                    ->sortable(),

                TextColumn::make('annee')
                    ->label('Année')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('domaine')
                    ->label('Domaine')
                    ->sortable()
                    ->limit(30),

                TextColumn::make('resultat')
                    ->label('Résultat')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'won', 'completed' => 'success',
                        'lost'             => 'danger',
                        'abandoned'        => 'warning',
                        'cancelled'        => 'gray',
                        default            => 'secondary',
                    })
                    ->placeholder('—'),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'archived' => 'success',
                        'draft'    => 'warning',
                        'restaure' => 'info',
                        default    => 'gray',
                    }),

                TextColumn::make('date_archive')
                    ->label('Archivé le')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('archiveur.name')
                    ->label('Archivé par')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'Manifestation'         => 'Manifestations',
                        'Offres'                => 'Offres',
                        'Livrable'              => 'Projets',
                        'Rapport'               => 'Rapports',
                        'Avis de manifestation' => 'Avis Manifestation',
                    ]),

                SelectFilter::make('annee')
                    ->label('Année')
                    ->options(
                        fn() => Archive::query()
                            ->selectRaw('annee')
                            ->whereNotNull('annee')
                            ->distinct()
                            ->orderBy('annee', 'desc')
                            ->pluck('annee', 'annee')
                            ->toArray()
                    ),

                SelectFilter::make('domaine')
                    ->label('Domaine')
                    ->searchable()
                    ->options(
                        fn() => Archive::query()
                            ->selectRaw('domaine')
                            ->whereNotNull('domaine')
                            ->distinct()
                            ->orderBy('domaine')
                            ->pluck('domaine', 'domaine')
                            ->toArray()
                    ),

                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'archived' => 'Archivé',
                        'draft'    => 'Brouillon',
                        'restaure' => 'Restauré',
                    ]),

                Filter::make('annee_range')
                    ->label('Plage d\'années')
                    ->form([
                        TextInput::make('annee_from')->numeric()->label('Année de'),
                        TextInput::make('annee_to')->numeric()->label('Année à'),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['annee_from'] ?? null, fn($q) => $q->where('annee', '>=', $data['annee_from']))
                        ->when($data['annee_to'] ?? null, fn($q) => $q->where('annee', '<=', $data['annee_to']))
                    ),
            ])
            ->actions([
                Action::make('ouvrir_dossier')
                    ->label('Fichiers')
                    ->icon('heroicon-o-folder-open')
                    ->visible(fn(Archive $record): bool => filled($record->fichier))
                    ->modalHeading(fn(Archive $record): string => 'Fichiers — ' . $record->titre)
                    ->modalContent(function (Archive $record) {
                        $fichiers = $record->fichier ?? [];

                        if (empty($fichiers)) {
                            return new \Illuminate\Support\HtmlString(
                                '<p class="text-sm text-gray-500 dark:text-gray-400">Aucun fichier enregistré dans cette archive.</p>'
                            );
                        }

                        $html = '<ul class="divide-y divide-gray-200 dark:divide-gray-700">';
                        foreach ($fichiers as $fichier) {
                            $chemin = $fichier['chemin'] ?? '';
                            $nom    = $fichier['nom']    ?? ($fichier['type'] ?? 'Fichier');
                            $type   = $fichier['type']   ?? '';

                            if (!$chemin) continue;

                            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($chemin);

                            $html .= '<li class="flex items-center gap-3 py-2">';
                            $html .= '<svg class="h-5 w-5 shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>';
                            $html .= '<div class="min-w-0 flex-1">';
                            $html .= '<a href="' . e($url) . '" target="_blank" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">' . e($nom) . '</a>';
                            if ($type) {
                                $html .= '<p class="text-xs text-gray-400">' . e($type) . '</p>';
                            }
                            $html .= '</div></li>';
                        }
                        $html .= '</ul>';

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer'),
            ])
            ->defaultSort('date_archive', 'desc')
            ->striped()
            ->paginated([15, 30, 50, 100]);
    }
}

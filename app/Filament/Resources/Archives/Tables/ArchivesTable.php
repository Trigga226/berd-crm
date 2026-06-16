<?php

namespace App\Filament\Resources\Archives\Tables;

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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ArchivesTable
{
    public static function configure(Table $table): Table
    {
        $isAdmin = fn() => Auth::user()?->email === 'franck.b@berd-ing.com';

        return $table
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
                        'Manifestation'          => 'info',
                        'Offres'                 => 'success',
                        'Livrable'               => 'primary',
                        'Rapport'                => 'gray',
                        'Document administratif' => 'danger',
                        default                  => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('annee')
                    ->label('Année')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('domaine')
                    ->label('Domaine')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('resultat')
                    ->label('Résultat')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'won'       => 'Gagné',
                        'completed' => 'Terminé',
                        'lost'      => 'Perdu',
                        'cancelled' => 'Annulé',
                        'abandoned' => 'Abandonné',
                        'submitted' => 'Soumis',
                        default     => $state ?? '—',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'won', 'completed'       => 'success',
                        'lost', 'cancelled'      => 'danger',
                        'abandoned', 'submitted' => 'warning',
                        default                  => 'gray',
                    })
                    ->placeholder('—'),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'archived' => 'Archivé',
                        'draft'    => 'Brouillon',
                        'restaure' => 'Restauré',
                        default    => $state ?? '—',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'archived' => 'success',
                        'draft'    => 'gray',
                        'restaure' => 'warning',
                        default    => 'gray',
                    }),
                TextColumn::make('date_archive')
                    ->label('Archivé le')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('media_files_count')
                    ->label('Fichiers')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'Avis de manifestation' => 'Avis de manifestation',
                        'Manifestation'          => 'Manifestation',
                        'Offres'                 => 'Offres',
                        'Livrable'               => 'Livrable',
                        'Rapport'                => 'Rapport',
                        'Document administratif' => 'Document administratif',
                        'Autre'                  => 'Autre',
                    ]),
                SelectFilter::make('annee')
                    ->label('Année')
                    ->options(fn() => \App\Models\Archive::query()
                        ->distinct()
                        ->orderBy('annee', 'desc')
                        ->pluck('annee', 'annee')
                        ->filter()
                        ->toArray()
                    ),
                SelectFilter::make('domaine')
                    ->label('Domaine')
                    ->options(fn() => \App\Models\Archive::query()
                        ->distinct()
                        ->orderBy('domaine')
                        ->pluck('domaine', 'domaine')
                        ->filter()
                        ->toArray()
                    )
                    ->searchable(),
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
                        \Filament\Forms\Components\TextInput::make('annee_from')
                            ->numeric()
                            ->label('Année de')
                            ->minValue(2000)
                            ->maxValue(2100),
                        \Filament\Forms\Components\TextInput::make('annee_to')
                            ->numeric()
                            ->label('Année à')
                            ->minValue(2000)
                            ->maxValue(2100),
                    ])
                    ->query(fn($query, $data) => $query
                        ->when($data['annee_from'], fn($q) => $q->where('annee', '>=', $data['annee_from']))
                        ->when($data['annee_to'],   fn($q) => $q->where('annee', '<=', $data['annee_to']))
                    ),
                TrashedFilter::make()->visible($isAdmin),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('voir_dossier')
                    ->label('Fichiers')
                    ->icon('heroicon-o-folder-open')
                    ->color('info')
                    ->visible(fn($record) => filled($record->fichier))
                    ->modalHeading(fn($record) => 'Fichiers — ' . $record->titre)
                    ->modalContent(function ($record) {
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
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('archives.csv')
                    ->columns([
                        'Titre'      => fn($r) => $r->titre ?? '',
                        'Type'       => fn($r) => $r->type ?? '',
                        'Année'      => fn($r) => $r->annee ?? '',
                        'Domaine'    => fn($r) => $r->domaine ?? '',
                        'Résultat'   => fn($r) => $r->resultat ?? '',
                        'Statut'     => fn($r) => $r->statut ?? '',
                        'Archivé le' => fn($r) => $r->date_archive?->format('d/m/Y') ?? '',
                        'Dossier'    => fn($r) => $r->folder_path ?? '',
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible($isAdmin),
                    RestoreBulkAction::make()->visible($isAdmin),
                ]),
            ])
            ->defaultSort('date_archive', 'desc');
    }
}

<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Auth;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // En-tête avec informations principales
                ComponentsSection::make('Informations Générales')
                    ->schema([
                        ComponentsGrid::make(3)->columnSpanFull()
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Code Projet')
                                    ->weight(FontWeight::Bold)
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag'),
                                TextEntry::make('status')
                                    ->label('Statut')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'preparation' => 'gray',
                                        'ongoing' => 'info',
                                        'suspended' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'preparation' => 'Préparation',
                                        'ongoing' => 'En Cours',
                                        'suspended' => 'Suspendu',
                                        'completed' => 'Terminé',
                                        'cancelled' => 'Annulé',
                                        default => $state,
                                    }),
                                TextEntry::make('execution_percentage')
                                    ->label('% d\'Exécution')
                                    ->suffix('%')
                                    ->color(fn($state) => $state >= 75 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                                    ->weight(FontWeight::Bold),
                            ]),
                        TextEntry::make('title')
                            ->label('Titre du Projet')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('Aucune description')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Relations
                ComponentsSection::make('Relations')
                    ->schema([
                        TextEntry::make('offer.title')
                            ->label('Offre Associée')
                            ->placeholder('Aucune offre')
                            ->icon('heroicon-o-document-text'),
                        TextEntry::make('client.name')
                            ->label('Client')
                            ->icon('heroicon-o-building-office-2')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('country')
                            ->label('Pays')
                            ->icon('heroicon-o-globe-alt'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Bailleurs de fonds
                ComponentsSection::make('Bailleurs de fonds')
                    ->schema([
                        TextEntry::make('bailleurs')
                            ->label('')
                            ->state(fn($record) => $record->bailleurs->map(function ($b) {
                                $label = $b->acronym ?: $b->name;
                                if ($b->pivot?->is_lead) {
                                    $label .= ' (chef de file)';
                                }
                                return $label;
                            })->all())
                            ->badge()
                            ->color(fn($state) => str_contains((string) $state, 'chef de file') ? 'success' : 'info')
                            ->columnSpanFull()
                            ->placeholder('Aucun bailleur associé'),
                    ])
                    ->collapsible()
                    ->visible(fn($record) => $record->bailleurs->isNotEmpty()),

                // Planning
                ComponentsSection::make('Planning')
                    ->schema([
                        ComponentsGrid::make(2)
                            ->schema([
                                TextEntry::make('planned_start_date')
                                    ->label('Début Prévu')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar'),
                                TextEntry::make('planned_end_date')
                                    ->label('Fin Prévue')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color(fn($record) => $record->isDelayed() ? 'danger' : 'success'),
                            ]),
                        ComponentsGrid::make(2)
                            ->schema([
                                TextEntry::make('actual_start_date')
                                    ->label('Début Réel')
                                    ->date('d/m/Y')
                                    ->placeholder('Non commencé')
                                    ->icon('heroicon-o-calendar-days'),
                                TextEntry::make('actual_end_date')
                                    ->label('Fin Réelle')
                                    ->date('d/m/Y')
                                    ->placeholder('Non terminé')
                                    ->icon('heroicon-o-calendar-days'),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Budget
                ComponentsSection::make('Budget')->visible(fn (): bool => Auth::user()->canViewFinancials())
                    ->schema([
                        TextEntry::make('total_budget')
                            ->label('Coût du Marché')
                            ->money('XOF')
                            ->icon('heroicon-o-banknotes')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('consumed_budget')
                            ->label('Budget Consommé')
                            ->money('XOF')
                            ->icon('heroicon-o-banknotes')
                            ->tooltip('Coût réel des experts + frais remboursables')
                            ->color(fn($record) => $record->consumed_budget > $record->total_budget ? 'danger' : 'success'),
                        TextEntry::make('experts_real_cost')
                            ->label('— dont Coût Experts (réel)')
                            ->state(fn($record) => $record->expertsRealCost())
                            ->money('XOF')
                            ->icon('heroicon-o-user-group')
                            ->color('gray'),
                        TextEntry::make('reimbursable_expenses')
                            ->label('— dont Frais Remboursables')
                            ->state(fn($record) => $record->reimbursableExpensesTotal())
                            ->money('XOF')
                            ->icon('heroicon-o-receipt-percent')
                            ->color('gray'),
                        TextEntry::make('budget_variance')
                            ->label('Variance Budgétaire')
                            ->state(fn($record) => $record->budgetVariance())
                            ->money('XOF')
                            ->color(fn($state) => $state < 0 ? 'danger' : 'success')
                            ->icon(fn($state) => $state < 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Gestion
                ComponentsSection::make('Gestion du Projet')
                    ->schema([
                        TextEntry::make('projectManagerUser.name')
                            ->label('Chef de Projet (Interne)')
                            ->placeholder('Non assigné')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('projectManagerExpert.full_name')
                            ->label('Chef de Projet (Expert)')
                            ->placeholder('Non assigné')
                            ->icon('heroicon-o-user-circle')
                            ->state(fn($record) => $record->projectManagerExpert
                                ? "{$record->projectManagerExpert->first_name} {$record->projectManagerExpert->last_name}"
                                : null),
                        TextEntry::make('contract_path')
                            ->label('Contrat')
                            ->placeholder('Aucun contrat')
                            ->icon('heroicon-o-document')
                            ->url(fn($state) => $state ? route('private.file.show', ['path' => $state]) : null)
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn($state) => $state ? 'Voir le contrat' : 'Aucun contrat'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Métadonnées
                ComponentsSection::make('Métadonnées')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-arrow-path'),
                        TextEntry::make('deleted_at')
                            ->label('Supprimé le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-trash')
                            ->visible(fn(Project $record): bool => $record->trashed())
                            ->color('danger'),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }
}

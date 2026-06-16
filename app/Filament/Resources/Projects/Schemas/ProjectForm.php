<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Utils\Pays;
use Filament\Schemas\Components\Tabs as ComponentsTabs;
use Illuminate\Support\Facades\Auth;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsTabs::make('Tabs')
                    ->tabs([
                        ComponentsTabs\Tab::make('Informations Générales')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('code')
                                    ->label('Code Projet')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('PRJ-2024-001'),
                                Select::make('offer_id')
                                    ->label('Offre')
                                    ->relationship('offer', 'title')
                                    ->searchable()
                                    ->preload(),
                                Select::make('client_id')
                                    ->label('Client')
                                    ->relationship('client', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('country')
                                    ->label('Pays')
                                    ->options(Pays::$LISTEPAYS)
                                    ->required()
                                    ->searchable(),
                                Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'preparation' => 'Préparation',
                                        'ongoing' => 'En Cours',
                                        'suspended' => 'Suspendu',
                                        'completed' => 'Terminé',
                                        'cancelled' => 'Annulé',
                                    ])
                                    ->required()
                                    ->default('preparation'),
                                Toggle::make('use_manual_percentage')
                                    ->label('Saisie manuelle de l\'avancement')
                                    ->helperText('Désactive le calcul basé sur les livrables')
                                    ->live(),
                                TextInput::make('execution_percentage')
                                     ->label('% d\'Exécution')
                                     ->numeric()
                                     ->suffix('%')
                                     ->minValue(0)
                                     ->maxValue(100)
                                     ->default(0)
                                     ->disabled(fn($get) => !$get('use_manual_percentage')),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        ComponentsTabs\Tab::make('Planning')
                            ->schema([
                                DatePicker::make('planned_start_date')
                                    ->label('Date de Début Prévue')
                                    ->required(),
                                DatePicker::make('planned_end_date')
                                    ->label('Date de Fin Prévue')
                                    ->required(),
                                DatePicker::make('actual_start_date')
                                    ->label('Date de Début Réelle'),
                                DatePicker::make('actual_end_date')
                                    ->label('Date de Fin Réelle'),
                            ])
                            ->columns(2),

                        ComponentsTabs\Tab::make('Budget')->visible(fn (): bool => Auth::user()->canViewFinancials())
                            ->schema([
                                TextInput::make('total_budget')
                                    ->label('Coût du Marché')
                                    ->numeric()
                                    ->prefix('XOF')
                                    ->step(0.01),
                                TextInput::make('consumed_budget')
                                    ->label('Budget Consommé')
                                    ->numeric()
                                    ->prefix('XOF')
                                    ->step(0.01)
                                    ->default(0)
                                    ->disabled(),
                            ])
                            ->columns(2),

                        ComponentsTabs\Tab::make('Bailleurs & Financement')
                            ->schema([
                                Repeater::make('projectBailleurs')
                                    ->relationship('projectBailleurs')
                                    ->label('Bailleurs de fonds (co-financement)')
                                    ->schema([
                                        Select::make('bailleur_id')
                                            ->label('Bailleur')
                                            ->relationship('bailleur', 'name')
                                            ->getOptionLabelFromRecordUsing(fn($record) => $record->acronym ? "{$record->acronym} — {$record->name}" : $record->name)
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->columnSpan(2),
                                        TextInput::make('financing_amount')
                                            ->label('Montant financé')
                                            ->numeric()
                                            ->prefix('XOF')
                                            ->step(0.01),
                                        Toggle::make('is_lead')
                                            ->label('Chef de file')
                                            ->inline(false),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter un bailleur'),
                            ]),

                        ComponentsTabs\Tab::make('Chef de Projet & Contrat')
                            ->schema([
                                Select::make('project_manager_user_id')
                                    ->label('Chef de Projet (Interne)')
                                    ->relationship('projectManagerUser', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('project_manager_expert_id')
                                    ->label('Chef de Projet (Expert)')
                                    ->relationship('projectManagerExpert', 'last_name')
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}"),
                                FileUpload::make('contract_path')
                                    ->label('Contrat du Projet')
                                    ->disk('local')
                                    ->directory('projets/contrats')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

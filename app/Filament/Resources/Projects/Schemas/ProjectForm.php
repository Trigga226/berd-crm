<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
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
                                TextInput::make('execution_percentage')
                                    ->label('% d\'Exécution')
                                    ->numeric()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->disabled(),
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

                        ComponentsTabs\Tab::make('Budget')->visible(function():bool{
                        return  Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Gerant') || Auth::user()->hasRole('Comptable');
                })
                            ->schema([
                                TextInput::make('total_budget')
                                    ->label('Budget Total')
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

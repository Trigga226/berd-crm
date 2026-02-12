<?php

namespace App\Filament\Resources\Offers\Schemas;

use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Models\Client; // Add this import for client lookup
use App\Models\Manifestation;
use App\utils\Pays;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Offre')
                    ->tabs([
                        // Onglet Général
                        Tabs\Tab::make('Général')
                            ->schema([
                                ComponentsSection::make('Informations Principales')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Titre')
                                            ->required()
                                            ->maxLength(255)
                                            ->reactive(),
                                        ComponentsGrid::make(3)
                                            ->schema([
                                                Select::make('client_id')
                                                    ->relationship('client', 'name')
                                                    ->label('Client')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        if ($state) {
                                                            $client = Client::find($state);
                                                            if ($client && $client->country) {
                                                                $set('country', $client->country);
                                                            }
                                                        }
                                                    })
                                                    ->createOptionForm(ClientForm::getComponents()),
                                                Select::make('country')
                                                    ->label('Pays')
                                                    ->options(Pays::$LISTEPAYS)
                                                    ->searchable(),
                                                Select::make('manifestation_id')
                                                    ->relationship('manifestation', 'id')
                                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->avisManifestation?->title ?? 'Manifestation #' . $record->id)
                                                    ->label('Manifestation (Optionnel)')
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set, $state) {
                                                        if (! $state) {
                                                            return;
                                                        }
                                                        $manifestation = Manifestation::with(['avisManifestation', 'manifestationPartners'])->find($state);
                                                        if (! $manifestation) {
                                                            return;
                                                        }

                                                        // Auto-fill Client
                                                        if ($manifestation->avisManifestation?->client_id) {
                                                            $set('client_id', $manifestation->avisManifestation->client_id);
                                                            // Also trigger country update from this client
                                                            $client = Client::find($manifestation->avisManifestation->client_id);
                                                            if ($client && $client->country) {
                                                                $set('country', $client->country);
                                                            }
                                                        }

                                                        // Auto-fill Country from Manifestation if available
                                                        if ($manifestation->country) {
                                                            $set('country', $manifestation->country);
                                                        }

                                                        // Auto-fill Consortium
                                                        $set('is_consortium', (bool) $manifestation->is_groupement);

                                                        // Auto-fill Partners
                                                        if ($manifestation->manifestationPartners->isNotEmpty()) {
                                                            $partnersData = $manifestation->manifestationPartners->map(function ($partnerPivot) {
                                                                return [
                                                                    'partner_id' => $partnerPivot->partner_id,
                                                                    'is_lead' => (bool) $partnerPivot->is_lead,
                                                                ];
                                                            })->toArray();
                                                            $set('partners', $partnersData);
                                                        }
                                                    }),
                                            ]),
                                        ComponentsGrid::make(2)
                                            ->schema([
                                                Select::make('result')
                                                    ->label('Résultat Global')
                                                    ->options([
                                                        'En attente' => 'En attente',
                                                        'Gagné' => 'Gagné',
                                                        'Perdu' => 'Perdu',
                                                        'Abandonné' => 'Abandonné',
                                                    ])
                                                    ->default('En attente'),
                                                TextInput::make('general_note')
                                                    ->label('Note Générale')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->suffix('/ 100'),
                                            ]),
                                    ]),

                                ComponentsSection::make('Groupement & Partenaires')
                                    ->schema([
                                        Toggle::make('is_consortium')
                                            ->label('Groupement')
                                            ->reactive(),
                                        Repeater::make('partners')
                                            ->relationship('offerPartners')
                                            ->schema([
                                                Select::make('partner_id')
                                                    ->relationship('partner', 'name')
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->columnSpan(2),
                                                Toggle::make('is_lead')
                                                    ->label('Chef de file')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(3)
                                            ->label('Partenaires')
                                            ->visible(fn(Get $get) => $get('is_consortium')),
                                    ])
                                    ->collapsible(),

                                ComponentsSection::make('Équipe et Documents')
                                    ->schema([
                                        Repeater::make('users')
                                            ->relationship('offerUsers')
                                            ->schema([
                                                Select::make('user_id')
                                                    ->relationship('user', 'name')
                                                    ->label('Utilisateur')
                                                    ->required()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                                Select::make('role')
                                                    ->options([
                                                        'charge_etude' => 'Charger d\'étude',
                                                        'assistant' => 'Assistant',
                                                    ])
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->label('Équipe (Chargés / Assistants)'),
                                        FileUpload::make('dp_path')
                                            ->label('Demande de Proposition (DP)')
                                            ->directory(fn(Get $get) => 'offres/' . (Str::slug($get('title')) ?: 'temp') . '/dp/fichiers')
                                            ->preserveFilenames()
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->downloadable()
                                            ->openable(),
                                    ]),
                            ]),

                        // Onglet Offre Technique
                        Tabs\Tab::make('Offre Technique')
                            ->schema([
                                ComponentsSection::make('Détails de l\'Offre Technique')
                                    ->schema([
                                        TextInput::make('technicalOffer.title')->default('Offre Technique')->columnSpan(2),
                                        Textarea::make('technicalOffer.description')->label('Description')->columnSpan(2),
                                        ComponentsGrid::make(3)
                                            ->schema([
                                                Select::make('technicalOffer.submission_mode')
                                                    ->options([
                                                        'online' => 'En ligne',
                                                        'physical' => 'Physique',
                                                        'email' => 'Email',
                                                    ])->label('Mode de dépôt'),
                                                Select::make('technicalOffer.result')
                                                    ->label('Résultat')
                                                    ->options([
                                                        'En attente' => 'En attente',
                                                        'Gagné' => 'Gagné',
                                                        'Perdu' => 'Perdu',
                                                        'Abandonné' => 'Abandonné',
                                                    ]),
                                                TextInput::make('technicalOffer.note')
                                                    ->label('Note')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->suffix('/ 100'),
                                            ]),
                                    ]),

                                ComponentsSection::make('Calendrier')
                                    ->schema([
                                        ComponentsGrid::make(3)
                                            ->schema([
                                                DatePicker::make('technicalOffer.deadline')->label('Date limite'),
                                                DatePicker::make('technicalOffer.internal_control_date')->label('Date contrôle interne'),
                                                DatePicker::make('technicalOffer.submission_date')->label('Date de dépôt'),
                                            ]),
                                    ]),

                                Tabs::make('Documents Techniques')
                                    ->tabs([
                                        Tabs\Tab::make('Tech 0: Présentation')
                                            ->schema([
                                                self::getDocUpload('tech_cover', 'Page de garde', 'offre technique/presentation', 'technicalOffer'),
                                                self::getDocUpload('tech_summary', 'Sommaire', 'offre technique/presentation', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 1: Admin')
                                            ->schema([
                                                self::getDocUpload('tech_1_1', 'Lettre de soumission', 'offre technique/tech 1', 'technicalOffer'),
                                                self::getDocUpload('tech_1_2', 'Accord de groupement', 'offre technique/tech 1', 'technicalOffer')
                                                    ->visible(fn(Get $get) => $get('../../is_consortium')),
                                                self::getDocUpload('tech_1_3', 'Pouvoir Habilitation / Statut Juridique', 'offre technique/tech 1', 'technicalOffer'),
                                                self::getDocUpload('tech_1_4', 'Pouvoir Chef de file', 'offre technique/tech 1', 'technicalOffer')
                                                    ->visible(fn(Get $get) => $get('../../is_consortium')),
                                                self::getDocUpload('tech_1_5', 'Pièces Admin', 'offre technique/tech 1', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 2: Orga & Exp')
                                            ->schema([
                                                self::getDocUpload('tech_2_a', 'Organisation', 'offre technique/tech 2', 'technicalOffer'),
                                                self::getDocUpload('tech_2_b', 'Expérience', 'offre technique/tech 2', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 3: Commentaires')
                                            ->schema([
                                                self::getDocUpload('tech_3_a', 'Sur TDR', 'offre technique/tech 3', 'technicalOffer'),
                                                self::getDocUpload('tech_3_b', 'Sur Personnel/Prestations', 'offre technique/tech 3', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 4: Méthodologie')
                                            ->schema([
                                                self::getDocUpload('tech_4', 'Méthodologie', 'offre technique/tech 4', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 5: Programme')
                                            ->schema([
                                                self::getDocUpload('tech_5', 'Programme et Calendrier', 'offre technique/tech 5', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 6: Équipe')
                                            ->schema([
                                                self::getDocUpload('tech_6_1', 'Composition Équipe', 'offre technique/tech 6', 'technicalOffer'),
                                                self::getDocUpload('tech_6_2', 'CVs et Disponibilité', 'offre technique/tech 6', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Tech 7: Code')
                                            ->schema([
                                                self::getDocUpload('tech_7', 'Code de Conduite', 'offre technique/tech 7', 'technicalOffer'),
                                            ]),
                                        Tabs\Tab::make('Autres')
                                            ->schema([
                                                self::getDocUpload('tech_other_1', 'Document Additionnel 1', 'offre technique/autres', 'technicalOffer'),
                                                self::getDocUpload('tech_other_2', 'Document Additionnel 2', 'offre technique/autres', 'technicalOffer'),
                                                self::getDocUpload('tech_other_3', 'Document Additionnel 3', 'offre technique/autres', 'technicalOffer'),
                                            ]),
                                    ]),
                            ]),

                        // Onglet Offre Financière
                        Tabs\Tab::make('Offre Financière')
                            ->schema([
                                ComponentsSection::make('Détails de l\'Offre Financière')
                                    ->schema([
                                        TextInput::make('financialOffer.title')->default('Offre Financière')->columnSpan(2),
                                        Textarea::make('financialOffer.description')->label('Description')->columnSpan(2),
                                        ComponentsGrid::make(3)
                                            ->schema([
                                                Select::make('financialOffer.submission_mode')
                                                    ->options([
                                                        'online' => 'En ligne',
                                                        'physical' => 'Physique',
                                                        'email' => 'Email',
                                                    ])->label('Mode de dépôt'),
                                                Select::make('financialOffer.result')
                                                    ->label('Résultat')
                                                    ->options([
                                                        'En attente' => 'En attente',
                                                        'Gagné' => 'Gagné',
                                                        'Perdu' => 'Perdu',
                                                        'Abandonné' => 'Abandonné',
                                                    ]),
                                                TextInput::make('financialOffer.note')
                                                    ->label('Note')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->suffix('/ 100'),
                                            ]),
                                    ]),

                                ComponentsSection::make('Calendrier')
                                    ->schema([
                                        ComponentsGrid::make(3)
                                            ->schema([
                                                DatePicker::make('financialOffer.deadline')->label('Date limite'),
                                                DatePicker::make('financialOffer.internal_control_date')->label('Date contrôle interne'),
                                                DatePicker::make('financialOffer.submission_date')->label('Date de dépôt'),
                                            ]),
                                    ]),

                                Tabs::make('Documents Financiers')
                                    ->tabs([
                                        Tabs\Tab::make('Fine 0: Présentation')
                                            ->schema([
                                                self::getDocUpload('fine_cover', 'Page de garde', 'offre financiere/presentation', 'financialOffer'),
                                            ]),
                                        Tabs\Tab::make('Fine 1-5')
                                            ->schema([
                                                self::getDocUpload('fine_1', 'Lettre de soumission', 'offre financiere/fine 1', 'financialOffer'),
                                                self::getDocUpload('fine_2', 'Tableau Récapitulatif', 'offre financiere/fine 2', 'financialOffer'),
                                                self::getDocUpload('fine_3', 'Sous détail rémunération', 'offre financiere/fine 3', 'financialOffer'),
                                                self::getDocUpload('fine_4', 'Autres dépenses', 'offre financiere/fine 4', 'financialOffer'),
                                                self::getDocUpload('fine_5', 'Déclaration des coûts', 'offre financiere/fine 5', 'financialOffer'),
                                            ]),
                                        Tabs\Tab::make('Autres')
                                            ->schema([
                                                self::getDocUpload('fine_other_1', 'Document Additionnel 1', 'offre financiere/autres', 'financialOffer'),
                                                self::getDocUpload('fine_other_2', 'Document Additionnel 2', 'offre financiere/autres', 'financialOffer'),
                                                self::getDocUpload('fine_other_3', 'Document Additionnel 3', 'offre financiere/autres', 'financialOffer'),
                                            ]),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getDocUpload(string $type, string $label, string $subfolder, string $relation = null): FileUpload
    {
        $fieldName = "documents_{$type}";
        if ($relation) {
            $fieldName = "{$relation}.{$fieldName}";
        }

        return FileUpload::make($fieldName)
            ->label($label)
            ->directory(function (Get $get, ?Model $record) use ($subfolder) {
                // Try to get title from record (Edit mode), otherwise from form state (Create mode).
                // Note: On Create, title might be empty initially, defaulting to 'temp'.
                $title = $record?->title ?? $get('../../title');
                $slug = Str::slug($title) ?: 'temp';
                return 'offres/' . $slug . '/' . $subfolder . '/fichiers';
            })
            ->preserveFilenames()
            ->acceptedFileTypes(['application/pdf'])
            ->downloadable()
            ->openable();
    }
}

<?php

namespace App\Filament\Resources\Offers\Schemas;

use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Models\Client; // Add this import for client lookup
use App\Models\Manifestation;
use App\Support\OfferDocumentSections;
use App\Utils\Pays;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
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
use Illuminate\Support\Str;
use Slimani\MediaManager\Form\MediaPicker;

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
                                                        'won'       => 'Gagné',
                                                        'lost'      => 'Perdu',
                                                        'abandoned' => 'Abandonné',
                                                    ])
                                                    ->placeholder('En cours')
                                                    ->default(null),
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
                                                        'won'       => 'Gagné',
                                                        'lost'      => 'Perdu',
                                                        'abandoned' => 'Abandonné',
                                                    ])
                                                    ->placeholder('En cours'),
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

                                self::documentsRepeater('technical', 'technicalDocuments', 'offre technique'),
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
                                                        'won'       => 'Gagné',
                                                        'lost'      => 'Perdu',
                                                        'abandoned' => 'Abandonné',
                                                    ])
                                                    ->placeholder('En cours'),
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

                                self::documentsRepeater('financial', 'financialDocuments', 'offre financiere'),
                            ]),

                        // Onglet Médiathèque
                        Tabs\Tab::make('Médiathèque')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                ComponentsSection::make('Fichiers joints')
                                    ->description('Attachez des fichiers supplémentaires depuis la médiathèque.')
                                    ->schema([
                                        MediaPicker::make('mediaFiles')
                                            ->label('Documents')
                                            ->relationship('mediaFiles')
                                            ->multiple()
                                            ->directory(fn(Get $get) => 'Offres/' . (Str::slug($get('title')) ?: 'temp'))
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                'application/vnd.ms-excel',
                                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                'application/vnd.ms-powerpoint',
                                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                                'image/jpeg',
                                                'image/png',
                                                'image/webp',
                                                'application/zip',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    /**
     * Répéteur réordonnable des pièces d'une offre (technique ou financière).
     *
     * Chaque ligne = une pièce dont l'utilisateur peut :
     *  - renommer l'intitulé librement (champ « label ») ;
     *  - téléverser/remplacer le fichier PDF ;
     *  - changer l'ordre par glisser-déposer (persisté dans « sort_order »).
     *
     * L'intitulé et l'ordre définis ici sont ceux respectés lors de la génération
     * (compilation) des PDF par App\Services\OfferPdfService.
     */
    public static function documentsRepeater(string $category, string $relation, string $subfolder): Repeater
    {
        return Repeater::make($relation)
            ->relationship($relation)
            ->label('Pièces du dossier')
            ->helperText('Renommez les intitulés et glissez les pièces pour définir l\'ordre de génération du PDF compilé.')
            ->schema([
                Hidden::make('category')->default($category),
                Hidden::make('type'),
                TextInput::make('label')
                    ->label('Intitulé')
                    ->maxLength(255)
                    ->columnSpan(2),
                FileUpload::make('path')
                    ->label('Fichier (PDF)')
                    ->directory(fn($livewire) => self::documentDirectory($livewire, $subfolder))
                    ->preserveFilenames()
                    ->storeFileNamesIn('original_name')
                    ->acceptedFileTypes(['application/pdf'])
                    ->downloadable()
                    ->openable()
                    ->columnSpan(3),
            ])
            ->columns(5)
            ->orderColumn('sort_order')
            ->reorderable()
            ->reorderableWithButtons()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'Nouvelle pièce')
            ->addActionLabel('Ajouter une pièce')
            ->default(OfferDocumentSections::defaults($category))
            ->columnSpanFull();
    }

    /**
     * Dossier de stockage d'une pièce, basé sur le slug du titre de l'offre.
     * Retombe sur « temp » tant que le titre n'est pas saisi ; le fichier est
     * ensuite déplacé automatiquement (cf. App\Models\OfferDocument).
     */
    protected static function documentDirectory($livewire, string $subfolder): string
    {
        $title = data_get($livewire, 'data.title');
        $slug  = Str::slug($title) ?: 'temp';

        return 'offres/' . $slug . '/' . $subfolder . '/fichiers';
    }
}

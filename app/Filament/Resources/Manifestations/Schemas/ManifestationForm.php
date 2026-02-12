<?php

namespace App\Filament\Resources\Manifestations\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use App\utils\Pays;

class ManifestationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Détails de la Manifestation')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        TextInput::make('score')
                            ->label('Note')
                            ->numeric()
                            ->maxValue(100)
                            ->suffix('/100'),
                        \Filament\Forms\Components\Textarea::make('observation')
                            ->label('Observation')
                            ->columnSpanFull(),
                        Select::make('avis_manifestation_id')
                            ->label('Avis de Manifestation')
                            ->relationship('avisManifestation', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $avis = \App\Models\AvisManifestation::find($state);
                                    if ($avis && $avis->client) {
                                        $set('client_name', $avis->client->name);
                                        $set('country', $avis->client->country);
                                        $set('deadline', $avis->deadline);
                                    }
                                }
                            }),

                        Select::make('country')
                            ->label('Pays')
                            ->options(Pays::$LISTEPAYS)
                            ->searchable(),

                        Select::make('domains')
                            ->label('Domaines')
                            ->options(\App\Utils\Domaines::getOptions())
                            ->searchable()
                            ->multiple()
                            ->required(),

                        TextInput::make('client_name')
                            ->label('Client'),

                        Select::make('status')
                            ->options([
                                'draft' => 'Brouillon',
                                'submitted' => 'Soumis',
                                'won' => 'Gagné',
                                'lost' => 'Perdu',
                                'abandoned' => 'Abandonné',
                            ])
                            ->default('draft')
                            ->required(),

                        DateTimePicker::make('deadline')
                            ->label('Date Limite'),

                        DateTimePicker::make('internal_control_date')
                            ->label('Date Contrôle Interne'),

                        Select::make('submission_mode')
                            ->label('Mode de Dépôt')
                            ->options([
                                'online' => 'En ligne',
                                'physical' => 'Physique',
                                'email' => 'Email',
                            ]),
                    ])->columns(2),

                Section::make('Équipe')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->schema([
                        Repeater::make('manifestationUsers')
                            ->relationship('manifestationUsers')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Utilisateur')
                                    ->options(\App\Models\User::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Select::make('role')
                                    ->options([
                                        'charge_etude' => 'Chargé d\'étude',
                                        'assistant' => 'Assistant',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->label('Membres de l\'équipe')
                            ->itemLabel(fn(array $state): ?string => \App\Models\User::find($state['user_id'] ?? null)?->name ?? null),
                    ]),

                Section::make('Partenaires')
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->collapsed(fn(Get $get) => !$get('is_groupement'))
                    ->schema([
                        Toggle::make('is_groupement')
                            ->label('Groupement ?')
                            ->live(),

                        Repeater::make('manifestationPartners')
                            ->relationship('manifestationPartners')
                            ->schema([
                                Select::make('partner_id')
                                    ->options(\App\Models\Partner::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->label('Partenaire')
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                Toggle::make('is_lead')
                                    ->label('Chef de file'),
                            ])
                            ->visible(fn(Get $get) => $get('is_groupement'))
                            ->label('Partenaires du Groupement')
                            ->columnSpanFull()
                            ->grid(2),
                    ]),

                Section::make('Experts')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('manifestationExperts')
                            ->relationship('manifestationExperts')
                            ->schema([
                                Select::make('expert_id')
                                    ->options(\App\Models\Expert::all()->mapWithKeys(fn($expert) => [$expert->id => $expert->first_name . ' ' . $expert->last_name]))
                                    ->searchable()
                                    ->required()
                                    ->label('Expert')
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                FileUpload::make('cv_path')
                                    ->label('CV Spécifique')
                                    ->directory('manifestations/experts')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->openable()
                                    ->downloadable(),
                            ])
                            ->columns(2)
                            ->label('Experts Mobiles')
                            ->grid(1)
                            ->columnSpanFull(),
                    ]),

                Tabs::make('Documents')
                    ->tabs([
                        self::makeDocTab('Page de Garde', 'page_garde', 'pageGardeDocuments'),
                        self::makeDocTab('Sommaire', 'sommaire', 'sommaireDocuments'),
                        self::makeDocTab('Lettre de Manifestation', 'lettre', 'lettreDocuments'),
                        self::makeDocTab('Pièces Administratives', 'piece_admin', 'pieceAdminDocuments'),
                        self::makeDocTab('Présentation', 'presentation', 'presentationDocuments'),
                        self::makeDocTab('Adresse', 'adresse', 'adresseDocuments'),
                        self::makeDocTab('Références Techniques', 'reference', 'referenceDocuments'),
                        self::makeDocTab('Autres Documents', 'autre', 'autreDocuments'),
                    ])->columnSpanFull(),
            ]);
    }

    protected static function makeDocTab(string $label, string $type, string $relationship): Tabs\Tab
    {
        return Tabs\Tab::make($label)
            ->schema([
                Repeater::make($relationship)
                    ->relationship($relationship)
                    ->schema([
                        TextInput::make('title')->label('Titre (Optionnel)'),
                        Hidden::make('type')->default($type),
                        FileUpload::make('file_path')
                            ->label('Fichier PDF')
                            ->required()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory(fn(Get $get) => 'manifestations/' . ($get('../../avis_manifestation_id') ?? 'temp') . '/' . $type)
                            ->preserveFilenames(),
                    ])
                    ->reorderableWithButtons()
                    ->label($label),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Manifestations\Schemas;

use App\Models\Expert;
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
use App\Utils\Pays;
use Slimani\MediaManager\Form\MediaPicker;

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

                            TextInput::make('score')
                            ->label('Note')
                            ->numeric()
                            ->maxValue(100)
                            ->suffix('/100'),
                        \Filament\Forms\Components\Textarea::make('observation')
                            ->label('Observation')
                            ->columnSpanFull(),
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
                                    ->label('Expert')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->options(function (Get $get) {
                                        $selectedDomains = $get('../../domains') ?? [];
                                        $experts         = Expert::orderBy('last_name')->get();

                                        $matched = [];
                                        $others  = [];

                                        foreach ($experts as $expert) {
                                            $skills  = array_map('mb_strtolower', $expert->skills ?? []);
                                            $name    = $expert->first_name . ' ' . $expert->last_name;
                                            $isMatch = false;

                                            foreach ($selectedDomains as $domain) {
                                                $domainLower = mb_strtolower($domain);
                                                foreach ($skills as $skill) {
                                                    if (str_contains($skill, $domainLower) || str_contains($domainLower, $skill)) {
                                                        $isMatch = true;
                                                        break 2;
                                                    }
                                                }
                                            }

                                            if (!empty($selectedDomains) && $isMatch) {
                                                $matched[$expert->id] = '⭐ ' . $name;
                                            } else {
                                                $others[$expert->id] = $name;
                                            }
                                        }

                                        return $matched + $others;
                                    })
                                    ->helperText(function (Get $get) {
                                        $domains = $get('../../domains') ?? [];
                                        if (empty($domains)) {
                                            return 'Renseignez les domaines pour voir les suggestions (⭐).';
                                        }
                                        $count = Expert::get()->filter(function ($e) use ($domains) {
                                            $skills = array_map('mb_strtolower', $e->skills ?? []);
                                            foreach ($domains as $d) {
                                                $dl = mb_strtolower($d);
                                                foreach ($skills as $s) {
                                                    if (str_contains($s, $dl) || str_contains($dl, $s)) return true;
                                                }
                                            }
                                            return false;
                                        })->count();
                                        return "{$count} expert(s) ⭐ correspondent aux domaines sélectionnés.";
                                    })
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

                Section::make('Médiathèque')
                    ->description('Attachez des fichiers supplémentaires depuis la médiathèque.')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        MediaPicker::make('mediaFiles')
                            ->label('Fichiers joints')
                            ->relationship('mediaFiles')
                            ->multiple()
                            ->directory(fn(Get $get) => 'Manifestations/' . ($get('avis_manifestation_id') ?? 'temp'))
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

<?php

namespace App\Filament\Resources\Experts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use App\Services\MistralCVParser;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Set;

class ExpertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('CV & Analyse')
                    ->description('Uploadez le CV principal pour remplir automatiquement les informations.')
                    ->schema([
                        FileUpload::make('cv_path')
                            ->label('CV Principal (PDF)')
                            ->disk('public')
                            ->directory('cv/tmp')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if (!$state) return;

                                // Gestion du chemin du fichier (Temp vs Stored)
                                $path = null;

                                // Si c'est un objet upload temporaire (Livewire)
                                if (is_object($state) && method_exists($state, 'getRealPath')) {
                                    $path = $state->getRealPath();
                                }
                                // Si c'est une chaîne (chemin)
                                elseif (is_string($state)) {
                                    // Si chemin absolu (cas du temp file renvoyé en string parfois)
                                    if (str_starts_with($state, '/') && file_exists($state)) {
                                        $path = $state;
                                    }
                                    // Sinon chemin relatif storage
                                    else {
                                        $storagePath = storage_path('app/public/' . $state);
                                        if (file_exists($storagePath)) {
                                            $path = $storagePath;
                                        }
                                    }
                                }

                                if (!$path || !file_exists($path)) {
                                    // Fail silently or log
                                    return;
                                }

                                try {
                                    $parser = new \App\Services\MistralCVParser();
                                    $data = $parser->parse($path);

                                    // Remplissage automatique
                                    if (isset($data['first_name'])) $set('first_name', $data['first_name']);
                                    if (isset($data['last_name'])) $set('last_name', $data['last_name']);
                                    if (isset($data['email'])) $set('email', $data['email']);
                                    if (isset($data['phone'])) $set('phone', $data['phone']);
                                    if (isset($data['years_of_experience'])) $set('years_of_experience', $data['years_of_experience']);
                                    if (isset($data['skills'])) $set('skills', $data['skills']);
                                    if (isset($data['formations'])) $set('formations', $data['formations']);
                                    if (isset($data['experiences'])) $set('experiences', $data['experiences']);
                                    if (isset($data['full_cv_text'])) $set('full_cv_text', $data['full_cv_text']);

                                    \Filament\Notifications\Notification::make()
                                        ->title('Analyse CV terminée')
                                        ->success()
                                        ->send();
                                } catch (\Exception $e) {
                                    \Filament\Notifications\Notification::make()
                                        ->title("Erreur d'analyse")
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Informations Personnelles')
                    ->schema([
                        TextInput::make('last_name')->label('Nom')->required(),
                        TextInput::make('first_name')->label('Prénom')->required(),
                        TextInput::make('email')->email()->label('Email'),
                        TextInput::make('phone')->tel()->label('Téléphone'),
                        \Filament\Forms\Components\TextInput::make('years_of_experience')
                            ->numeric()
                            ->label("Années d'expérience")
                            ->default(0),
                        \Filament\Forms\Components\Select::make('rating')
                            ->label('Note interne')
                            ->options([
                                1 => '⭐ (1)',
                                2 => '⭐⭐ (2)',
                                3 => '⭐⭐⭐ (3)',
                                4 => '⭐⭐⭐⭐ (4)',
                                5 => '⭐⭐⭐⭐⭐ (5)',
                            ])
                            ->placeholder('Non noté'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('CV multi-langues')
                    ->description('Versions du CV par langue pour les soumissions internationales.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('cv_paths')
                            ->label('CVs additionnels')
                            ->schema([
                                \Filament\Forms\Components\Select::make('lang')
                                    ->label('Langue')
                                    ->options([
                                        'fr' => '🇫🇷 Français',
                                        'en' => '🇬🇧 Anglais',
                                        'pt' => '🇵🇹 Portugais',
                                        'ar' => '🇩🇿 Arabe',
                                    ])
                                    ->required(),
                                \Filament\Forms\Components\FileUpload::make('path')
                                    ->label('Fichier PDF')
                                    ->disk('public')
                                    ->directory('cv/multilang')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->required()
                                    ->downloadable(),
                                TextInput::make('label')
                                    ->label('Intitulé (optionnel)')
                                    ->placeholder('ex: CV Banque Mondiale'),
                            ])
                            ->columns(3)
                            ->addActionLabel('Ajouter une version')
                            ->reorderableWithButtons()
                            ->columnSpanFull(),
                    ]),

                \Filament\Schemas\Components\Section::make('Compétences & Expérience')
                    ->schema([
                        \Filament\Forms\Components\TagsInput::make('skills')
                            ->label('Compétences')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('formations')
                            ->label('Formations')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('diplome')->label('Diplôme'),
                                \Filament\Forms\Components\TextInput::make('ecole')->label('École'),
                                \Filament\Forms\Components\TextInput::make('date_obtention')->label("Date d'obtention"),
                            ])
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('experiences')
                            ->label('Expériences Professionnelles')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('poste')->label('Poste'),
                                \Filament\Forms\Components\TextInput::make('entreprise')->label('Entreprise'),
                                \Filament\Forms\Components\TextInput::make('duree')->label('Durée'),
                            ])
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Textarea::make('full_cv_text')
                            ->label('Texte brut du CV')
                            ->hidden() // Caché mais présent
                            ->dehydrated(),
                    ]),
            ]);
    }
}

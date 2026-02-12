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
                    ->description('Uploadez le CV pour remplir automatiquement les informations.')
                    ->schema([
                        FileUpload::make('cv_path')
                            ->label('CV (PDF)')
                            ->disk('public')
                            ->directory('cv/tmp')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->live() // Réactif
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
                    ])->columns(2),

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

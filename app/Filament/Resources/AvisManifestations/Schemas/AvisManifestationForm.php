<?php

namespace App\Filament\Resources\AvisManifestations\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class AvisManifestationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Générales')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre de l\'Avis')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('reference_number')
                            ->label('Numéro de Référence')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        // Idéalement une relation, mais fallback texte simple possible
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(\App\Filament\Resources\Clients\Schemas\ClientForm::getComponents())
                            ->nullable(),

                        DateTimePicker::make('deadline')
                            ->label('Date Limite de Soumission')
                            ->required()
                            ->native(false),
                    ])->columns(2),

                Section::make('Fichiers & Description')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Document de l\'Avis (PDF)')
                            ->disk('public')
                            ->directory('avis_manifestations')
                            ->acceptedFileTypes(['application/pdf'])
                            ->openable()
                            ->downloadable()
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description / Notes/ Instructions')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Analyse & Workflow')
                    ->description('Assignation et suivi.')
                    ->schema([
                        Select::make('projectManagers')
                            ->label('Chargés de Projet (Analyse)')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(fn() => \App\Models\User::select('id', 'name')->orderBy('name')->pluck('name', 'id'))
                            ->getSearchResultsUsing(
                                fn(string $search) => \App\Models\User::query()
                                    ->select('id', 'name')
                                    ->where('name', 'like', "%{$search}%")
                                    ->orderBy('name')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                            ),


                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'submitted_to_analysis' => 'Soumis à analyse',
                                'validated' => 'Validé',
                                'rejected' => 'Rejeté',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),

                        DatePicker::make('submission_date')
                            ->label('Date de Soumission Effective'),
                    ])->columns(3)->columnSpanFull(),
            ]);
    }
}

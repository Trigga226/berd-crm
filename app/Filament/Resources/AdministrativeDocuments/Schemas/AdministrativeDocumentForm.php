<?php

namespace App\Filament\Resources\AdministrativeDocuments\Schemas;

use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Schema;

class AdministrativeDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->label('Titre du document')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-document-text'),

                        \Filament\Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options(\App\Models\AdministrativeDocument::getCategories())
                            ->searchable()
                            ->required()
                            ->live()
                            ->prefixIcon('heroicon-m-tag'),

                        \Filament\Forms\Components\DatePicker::make('expiration_date')
                            ->label("Date d'expiration")
                            ->native(false)
                            ->prefixIcon('heroicon-m-calendar')
                            ->helperText('Laisser vide si illimité'),

                        \Filament\Forms\Components\FileUpload::make('file_path')
                            ->label('Document (PDF)')
                            ->required()
                            ->disk('public')
                            ->directory(fn(UtilitiesGet $get) => 'documents_admin/' . \Illuminate\Support\Str::slug($get('category') ?? 'autres'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2)->columnSpanFull(),

                \Filament\Schemas\Components\Section::make('Informations complémentaires')
                    ->description('Détails spécifiques au type de document')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('additional_info.date_obtention')
                            ->label("Date d'obtention")
                            ->prefixIcon('heroicon-m-calendar-days'),
                        \Filament\Forms\Components\TagsInput::make('additional_info.domaines')
                            ->label('Domaines de compétence')
                            ->placeholder('Ajouter un domaine')
                            ->color('info'),
                    ])->columnSpanFull()
                    ->visible(fn(\Filament\Schemas\Components\Utilities\Get $get) => $get('category') === 'References')
                    ->columns(2),
            ]);
    }
}

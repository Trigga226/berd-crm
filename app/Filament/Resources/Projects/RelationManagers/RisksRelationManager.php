<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RisksRelationManager extends RelationManager
{
    protected static string $relationship = 'risks';

    protected static ?string $title = 'Risques';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->required(),
                Select::make('probability')
                    ->options([
                        'low' => 'Faible',
                        'medium' => 'Moyenne',
                        'high' => 'Élevée',
                    ])
                    ->default('medium')
                    ->required()
                    ->label('Probabilité'),
                Select::make('impact')
                    ->options([
                        'low' => 'Faible',
                        'medium' => 'Moyen',
                        'high' => 'Élevé',
                    ])
                    ->default('medium')
                    ->required()
                    ->label('Impact'),
                Textarea::make('mitigation_plan')
                    ->label('Plan d\'atténuation')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'identified' => 'Identifié',
                        'mitigated' => 'Atténué',
                        'occurred' => 'Survenu',
                        'closed' => 'Fermé',
                    ])
                    ->default('identified')
                    ->required()
                    ->label('Statut'),
                FileUpload::make('file_path')
                    ->label('Fichier joint')
                    ->directory('risks')
                    ->downloadable()
                    ->openable(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Titre'),
                TextEntry::make('description')->label('Description'),
                TextEntry::make('probability')
                    ->badge()
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ])
                    ->label('Probabilité'),
                TextEntry::make('impact')
                    ->badge()
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ])
                    ->label('Impact'),
                TextEntry::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'identified',
                        'success' => 'mitigated',
                        'danger' => 'occurred',
                        'success' => 'closed',
                    ])
                    ->label('Statut'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable(),
                TextColumn::make('probability')
                    ->badge()
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ])
                    ->label('Probabilité'),
                TextColumn::make('impact')
                    ->badge()
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ])
                    ->label('Impact'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'identified',
                        'success' => 'mitigated',
                        'danger' => 'occurred',
                        'success' => 'closed',
                    ])
                    ->label('Statut'),
                TextColumn::make('file_path')
                    ->label('Fichier')
                    ->formatStateUsing(fn() => 'Télécharger')
                    ->url(fn($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn($record) => ! empty($record->file_path)),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}

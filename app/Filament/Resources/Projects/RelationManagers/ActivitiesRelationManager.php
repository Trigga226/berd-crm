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
use Filament\Forms\Components\DatePicker;
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

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Activités';

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
                    ->columnSpanFull(),
                DatePicker::make('planned_start_date')
                    ->label('Date de début prévue')
                    ->required(),
                DatePicker::make('planned_end_date')
                    ->label('Date de fin prévue')
                    ->required(),
                DatePicker::make('actual_start_date')
                    ->label('Date de début réelle'),
                DatePicker::make('actual_end_date')
                    ->label('Date de fin réelle'),
                Select::make('status')
                    ->options([
                        'not_started' => 'Non commencé',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminé',
                        'blocked' => 'Bloqué',
                    ])
                    ->default('not_started')
                    ->required()
                    ->label('Statut'),
                Select::make('responsible_user_id')
                    ->relationship('responsibleUser', 'name')
                    ->label('Responsable')
                    ->searchable()
                    ->preload(),
                FileUpload::make('file_path')
                    ->label('Fier joint')
                    ->directory('activities')
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
                TextEntry::make('planned_start_date')->date()->label('Début prévu'),
                TextEntry::make('planned_end_date')->date()->label('Fin prévue'),
                TextEntry::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'not_started',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'blocked',
                    ]),
                TextEntry::make('responsibleUser.name')->label('Responsable'),
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
                TextColumn::make('planned_start_date')
                    ->label('Début')
                    ->date()
                    ->sortable(),
                TextColumn::make('planned_end_date')
                    ->label('Fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'not_started',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'blocked',
                    ]),
                TextColumn::make('responsibleUser.name')
                    ->label('Responsable')
                    ->searchable(),
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

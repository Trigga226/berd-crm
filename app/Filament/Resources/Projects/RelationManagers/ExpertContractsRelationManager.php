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
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ExpertContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'expertContracts';

    protected static ?string $title = 'Contrats Experts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('expert_id')
                    ->relationship('expert', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Expert'),
                TextInput::make('role')
                    ->label('Rôle')
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Date de début')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Date de fin')
                    ->required(),
                TextInput::make('daily_rate')
                    ->label('Taux journalier')
                    ->numeric()
                    ->prefix('XOF'),
                TextInput::make('planned_days')
                    ->label('Jours prévus')
                    ->numeric(),
                TextInput::make('actual_days')
                    ->label('Jours réels')
                    ->numeric()
                    ->helperText('Renseigner en fin de mission'),
                FileUpload::make('contract_path')
                    ->label('Contrat (PDF)')
                    ->disk('local')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('contracts')
                    ->downloadable()
                    ->openable(),
                Select::make('status')
                    ->options([
                        'draft' => 'Brouillon',
                        'active' => 'Actif',
                        'completed' => 'Terminé',
                        'terminated' => 'Résilié',
                    ])
                    ->default('draft')
                    ->required()
                    ->label('Statut'),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('expert.last_name')
                    ->label('Expert')
                    ->formatStateUsing(fn($record) => "{$record->expert->first_name} {$record->expert->last_name}"),
                TextEntry::make('role'),
                TextEntry::make('start_date')->date(),
                TextEntry::make('end_date')->date(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('expert.last_name')
                    ->label('Expert')
                    ->formatStateUsing(fn($record) => "{$record->expert->first_name} {$record->expert->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Rôle')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Début')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('planned_days')
                    ->label('J. Prévus')
                    ->suffix('j')
                    ->sortable(),
                TextColumn::make('actual_days')
                    ->label('J. Réels')
                    ->suffix('j')
                    ->placeholder('—')
                    ->color(fn ($record) => ($record->actual_days ?? 0) > ($record->planned_days ?? 0) ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active', 'completed' => 'success',
                        'terminated' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('contract_path')
                    ->label('Contrat')
                    ->formatStateUsing(fn() => 'Télécharger')
                    ->url(fn($record) => $record->contract_path ? route('private.file.show', ['path' => $record->contract_path]) : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn($record) => ! empty($record->contract_path)),
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

 

    protected function canCreate(): bool
    {
        return Auth::user()->can('create', \App\Models\ProjectExpertContract::class);
    }

    protected function canEdit(Model $record): bool
    {
        return Auth::user()->can('update', $record);
    }

    protected function canDelete(Model $record): bool
    {
        return Auth::user()->can('delete', $record);
    }

    protected function canDeleteAny(): bool
    {
        return Auth::user()->can('deleteAny', \App\Models\ProjectExpertContract::class);
    }
}

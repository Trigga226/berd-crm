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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
                Select::make('assigned_to')
                    ->label('Responsable')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Non assigné'),
                FileUpload::make('file_path')
                    ->label('Fichier joint')
                    ->disk('local')
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
                TextColumn::make('risk_score')
                    ->label('Score')
                    ->state(fn ($record) => match(true) {
                        $record->probability === 'high' && $record->impact === 'high' => '🔴 Critique',
                        ($record->probability === 'high' && $record->impact === 'medium')
                            || ($record->probability === 'medium' && $record->impact === 'high') => '🟠 Élevé',
                        $record->probability === 'low' && $record->impact === 'low' => '🟢 Faible',
                        default => '🟡 Moyen',
                    })
                    ->sortable(false),
                TextColumn::make('assignedTo.name')
                    ->label('Responsable')
                    ->placeholder('—')
                    ->icon('heroicon-o-user'),
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
                    ->url(fn($record) => $record->file_path ? route('private.file.show', ['path' => $record->file_path]) : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn($record) => ! empty($record->file_path)),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'identified' => 'Identifié',
                        'mitigated' => 'Atténué',
                        'occurred' => 'Survenu',
                        'closed' => 'Fermé',
                    ]),
                SelectFilter::make('impact')
                    ->label('Impact')
                    ->options(['low' => 'Faible', 'medium' => 'Moyen', 'high' => 'Élevé']),
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
        return Auth::user()->can('create', \App\Models\ProjectRisk::class);
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
        return Auth::user()->can('deleteAny', \App\Models\ProjectRisk::class);
    }
}

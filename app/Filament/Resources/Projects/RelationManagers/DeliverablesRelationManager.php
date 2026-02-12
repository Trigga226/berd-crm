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
use App\Models\ProjectDeliverable;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DeliverablesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliverables';

    protected static ?string $title = 'Livrables';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('planned_date')
                    ->label('Date limite de dépôt')
                    ->required(),
                DatePicker::make('internal_control_date')
                    ->label('Date de contrôle interne'),
                DatePicker::make('actual_date')
                    ->label('Date de dépôt'),
                FileUpload::make('file_path')
                    ->label('Fichier (PDF)')
                    ->acceptedFileTypes(['application/pdf'])
                    ->downloadable()
                    ->openable(),
                Select::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'submitted' => 'Soumis',
                        'validated' => 'Validé',
                        'rejected' => 'Rejeté',
                    ])
                    ->default('pending')
                    ->required()
                    ->label('Statut'),
                Textarea::make('validation_comments')
                    ->label('Commentaires de validation')
                    ->columnSpanFull(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
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
                TextColumn::make('planned_date')
                    ->label('Date limite')
                    ->date()
                    ->sortable(),
                TextColumn::make('internal_control_date')
                    ->label('Contrôle interne')
                    ->date()
                    ->sortable(),
                TextColumn::make('actual_date')
                    ->label('Date dépôt')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'submitted' => 'info',
                        'validated' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
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
                ActionsAction::make('validate')
                    ->label('Valider')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('validation_comments')
                            ->label('Commentaire')
                            ->required(),
                    ])
                    ->action(function (ProjectDeliverable $record, array $data): void {
                        $record->update([
                            'status' => 'validated',
                            'validated_at' => now(),
                            'validated_by' => Auth::user()->id,
                            'validation_comments' => $data['validation_comments'],
                        ]);

                        Notification::make()
                            ->title('Livrable validé avec succès')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(ProjectDeliverable $record) => $record->status !== 'validated'),
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

    protected function canEdit(Model $record): bool
    {
        return Auth::user()->hasRole('Charge') || Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Gerant') || Auth::user()->hasRole('secretaire de direction') || Auth::user()->hasRole('Assistant charge');
    }
}

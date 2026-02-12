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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AmendmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'amendments';

    protected static ?string $title = 'Avenants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')->required()
                    ->rows(3)
                    ->maxLength(65535),
                DatePicker::make('signature_date')
                    ->label('Date de signature')->required(),
                TextInput::make('budget_impact')
                    ->label('Impact budgétaire')
                    ->numeric()
                    ->prefix('XOF'),
                TextInput::make('delay_impact_days')
                    ->label('Impact délai (jours)')
                    ->numeric()->required(),
                FileUpload::make('file_path')
                    ->label('Avenant (PDF)')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('amendments')
                    ->downloadable()
                    ->openable(),
                Select::make('status')
                    ->options([
                        'draft' => 'Brouillon',
                        'signed' => 'Signé',
                        'cancelled' => 'Annulé',
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
                TextEntry::make('title'),
                TextEntry::make('description'),
                TextEntry::make('signature_date')->date(),
                TextEntry::make('budget_impact')->money('XOF'),
                TextEntry::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'signed',
                        'danger' => 'cancelled',
                    ]),
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
                TextColumn::make('signature_date')
                    ->label('Signé le')
                    ->date()
                    ->sortable(),
                TextColumn::make('budget_impact')
                    ->label('Impact Budget')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('delay_impact_days')
                    ->label('Impact Délai')
                    ->suffix(' jours')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'signed',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('file_path')
                    ->label('Document')
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

    protected function canEdit(Model $record): bool
    {
        return Auth::user()->hasRole('Charge') || Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('Gerant') || Auth::user()->hasRole('secretaire de direction') || Auth::user()->hasRole('Assistant charge');
    }
}

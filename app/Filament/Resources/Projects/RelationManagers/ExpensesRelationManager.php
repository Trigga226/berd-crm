<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\ProjectExpense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = 'Frais Remboursables';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Libellé')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('category')
                    ->label('Catégorie')
                    ->options(ProjectExpense::CATEGORIES)
                    ->native(false),
                TextInput::make('amount')
                    ->label('Montant')
                    ->numeric()
                    ->required()
                    ->prefix('XOF')
                    ->step(0.01),
                DatePicker::make('expense_date')
                    ->label('Date de la dépense'),
                Select::make('status')
                    ->label('Statut')
                    ->options(ProjectExpense::STATUSES)
                    ->default('pending')
                    ->required()
                    ->native(false),
                FileUpload::make('file_path')
                    ->label('Justificatif (PDF/Image)')
                    ->disk('local')
                    ->directory('projets/frais')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => ProjectExpense::CATEGORIES[$state] ?? '—'),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('XOF')->label('Total')),
                TextColumn::make('expense_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => ProjectExpense::STATUSES[$state] ?? '—')
                    ->color(fn(?string $state): string => $state === 'reimbursed' ? 'success' : 'warning'),
                TextColumn::make('file_path')
                    ->label('Justificatif')
                    ->formatStateUsing(fn() => 'Télécharger')
                    ->url(fn($record) => $record->file_path ? route('private.file.show', ['path' => $record->file_path]) : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn($record) => ! empty($record->file_path)),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(ProjectExpense::CATEGORIES),
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(ProjectExpense::STATUSES),
                TrashedFilter::make(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
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
        return Auth::user()->can('create', ProjectExpense::class);
    }

    protected function canEdit(Model $record): bool
    {
        return Auth::user()->can('update', $record);
    }

    protected function canDelete(Model $record): bool
    {
        return Auth::user()->can('delete', $record);
    }
}

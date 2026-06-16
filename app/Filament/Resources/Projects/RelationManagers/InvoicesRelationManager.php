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
use Filament\Actions\Action;
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
use App\Services\ProjectInvoicePdfService;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Factures';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->label('Numéro de facture')
                    ->required()
                    ->maxLength(255),
                Select::make('deliverable_id')
                    ->relationship(
                        'deliverable',
                        'title',
                        fn(Builder $query, \App\Filament\Resources\Projects\RelationManagers\InvoicesRelationManager $livewire) => $query
                            ->where('project_id', $livewire->getOwnerRecord()->id)
                            ->where('status', 'validated')
                    )
                    ->label('Livrable associé')
                    ->searchable()
                    ->preload(),
                DatePicker::make('issue_date')
                    ->label('Date d\'émission')
                    ->required(),
                DatePicker::make('due_date')
                    ->label('Date d\'échéance')
                    ->required(),
                TextInput::make('amount')
                    ->label('Montant')
                    ->numeric()
                    ->prefix('XOF')
                    ->required(),
                TextInput::make('paid_amount')
                    ->label('Montant payé')
                    ->numeric()
                    ->prefix('XOF')
                    ->default(0),
                FileUpload::make('file_path')
                    ->label('Facture (PDF)')
                    ->disk('local')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('invoices')
                    ->downloadable()
                    ->openable(),
                Select::make('status')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyée',
                        'paid' => 'Payée',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulée',
                    ])
                    ->default('draft')
                    ->required()
                    ->label('Statut'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('invoice_number')->label('Numéro'),
                TextEntry::make('deliverable.title')->label('Livrable'),
                TextEntry::make('issue_date')->date()->label('Date émission'),
                TextEntry::make('due_date')->date()->label('Date échéance'),
                TextEntry::make('amount')->money('XOF')->label('Montant'),
                TextEntry::make('paid_amount')->money('XOF')->label('Payé'),
                TextEntry::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft'     => 'Brouillon',
                        'sent'      => 'Envoyée',
                        'paid'      => 'Payée',
                        'overdue'   => 'En retard',
                        'cancelled' => 'Annulée',
                        default     => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft'              => 'gray',
                        'sent'               => 'info',
                        'paid'               => 'success',
                        'overdue', 'cancelled' => 'danger',
                        default              => 'gray',
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->searchable(),
                TextColumn::make('deliverable.title')
                    ->label('Livrable')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft'     => 'Brouillon',
                        'sent'      => 'Envoyée',
                        'paid'      => 'Payée',
                        'overdue'   => 'En retard',
                        'cancelled' => 'Annulée',
                        default     => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft'                => 'gray',
                        'sent'                 => 'info',
                        'paid'                 => 'success',
                        'overdue', 'cancelled' => 'danger',
                        default                => 'gray',
                    }),
                TextColumn::make('file_path')
                    ->label('Document')
                    ->formatStateUsing(fn() => 'Télécharger')
                    ->url(fn($record) => $record->file_path ? route('private.file.show', ['path' => $record->file_path]) : null)
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
                Action::make('generatePdf')
                    ->label('Générer PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn (Model $record) => app(ProjectInvoicePdfService::class)->generate($record))
                    ->visible(fn (Model $record) => $record->invoice_number !== null),
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

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Auth::user()->can('viewAny', \App\Models\ProjectInvoice::class);
    }

    protected function canCreate(): bool
    {
        return Auth::user()->can('create', \App\Models\ProjectInvoice::class);
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
        return Auth::user()->can('deleteAny', \App\Models\ProjectInvoice::class);
    }
}

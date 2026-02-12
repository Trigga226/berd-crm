<?php

namespace App\Filament\Resources\AdministrativeDocuments\Tables;

use Filament\Actions\Action as ActionsAction;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ForceDeleteAction as ActionsForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction as ActionsForceDeleteBulkAction;
use Filament\Actions\RestoreAction as ActionsRestoreAction;
use Filament\Actions\RestoreBulkAction as ActionsRestoreBulkAction;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AdministrativeDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-document-text'),
                TextColumn::make('category')
                    ->label('Catégorie')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('expiration_date')
                    ->label("Expire le")
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success')
                    ->icon('heroicon-m-calendar'),
                TextColumn::make('file_path')
                    ->label('Fichier')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->formatStateUsing(fn() => 'Télécharger')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->color('primary'),
                TextColumn::make('deleted_at')
                    ->label('Supprimé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(\App\Models\AdministrativeDocument::getCategories()),
                TrashedFilter::make()->native(false)->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            ])
            ->recordActions([
                ActionsViewAction::make(),
                ActionsEditAction::make(),
                ActionsAction::make('open')
                    ->label('Ouvrir')
                    ->icon('heroicon-m-eye')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                ActionsDeleteAction::make(),
                ActionsForceDeleteAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                ActionsRestoreAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            ])
            ->toolbarActions([
                ActionsBulkActionGroup::make([
                    ActionsDeleteBulkAction::make(),
                    ActionsForceDeleteBulkAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                    ActionsRestoreBulkAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                ]),
            ]);
    }
}

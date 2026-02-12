<?php

namespace App\Filament\Resources\Postes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PostesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-briefcase'),
                TextColumn::make('department.name')
                    ->label('Département')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Utilisateurs')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\TrashedFilter::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RestoreAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                ForceDeleteAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                    ForceDeleteBulkAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                ]),
            ]);
    }
}

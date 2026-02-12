<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(url('/logo.png')), // Optional placeholder
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(User $record): string => $record->email),
                TextColumn::make('roles.name')
                    ->badge()
                    ->state(function ($record) {
                        $role = $record->roles->first();
                        if ($role->name === "super_admin") {
                            return "Admin";
                        } else {
                            return $role->name;
                        }
                    })
                    ->searchable(),
                TextColumn::make('poste.title')
                    ->label('Poste')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('num_poste')
                    ->searchable()->label("Numéro poste"),
                TextColumn::make('num_perso')
                    ->searchable()->label("Numéro personnel"),

            ])
            ->filters([
                TrashedFilter::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                    RestoreBulkAction::make()->visible(Auth::user()->email === "franck.b@berd-ing.com"),
                ]),
            ]);
    }
}

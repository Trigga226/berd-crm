<?php

namespace App\Filament\Resources\Postes;

use App\Filament\Resources\Postes\Pages\CreatePoste;
use App\Filament\Resources\Postes\Pages\EditPoste;
use App\Filament\Resources\Postes\Pages\ListPostes;
use App\Filament\Resources\Postes\Pages\ViewPoste;
use App\Filament\Resources\Postes\Schemas\PosteForm;
use App\Filament\Resources\Postes\Schemas\PosteInfolist;
use App\Filament\Resources\Postes\Tables\PostesTable;
use App\Models\Poste;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PosteResource extends Resource
{
    protected static ?string $model = Poste::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Organisation Interne';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Schema $schema): Schema
    {
        return PosteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PosteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPostes::route('/'),
            'create' => CreatePoste::route('/create'),
            'view' => ViewPoste::route('/{record}'),
            'edit' => EditPoste::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}

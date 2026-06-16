<?php

namespace App\Filament\Resources\Bailleurs;

use App\Filament\Resources\Bailleurs\Pages\CreateBailleur;
use App\Filament\Resources\Bailleurs\Pages\EditBailleur;
use App\Filament\Resources\Bailleurs\Pages\ListBailleurs;
use App\Filament\Resources\Bailleurs\Pages\ViewBailleur;
use App\Filament\Resources\Bailleurs\Schemas\BailleurForm;
use App\Filament\Resources\Bailleurs\Schemas\BailleurInfolist;
use App\Filament\Resources\Bailleurs\Tables\BailleursTable;
use App\Models\Bailleur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BailleurResource extends Resource
{
    protected static ?string $model = Bailleur::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Tiers & Ressources';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Bailleur';
    protected static ?string $pluralModelLabel = 'Bailleurs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'acronym', 'email', 'ifu'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Sigle' => $record->acronym ?? '—',
            'Type'  => $record->type ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return BailleurForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BailleurInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BailleursTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBailleurs::route('/'),
            'create' => CreateBailleur::route('/create'),
            'view' => ViewBailleur::route('/{record}'),
            'edit' => EditBailleur::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

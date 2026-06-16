<?php

namespace App\Filament\Resources\References;

use App\Filament\Resources\References\Pages\CreateReference;
use App\Filament\Resources\References\Pages\EditReference;
use App\Filament\Resources\References\Pages\ListReferences;
use App\Filament\Resources\References\Pages\ViewReference;
use App\Filament\Resources\References\Schemas\ReferenceForm;
use App\Filament\Resources\References\Schemas\ReferenceInfolist;
use App\Filament\Resources\References\Tables\ReferencesTable;
use App\Models\Reference;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReferenceResource extends Resource
{
    protected static ?string $model = Reference::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Références';

    protected static \UnitEnum|string|null $navigationGroup = 'Pilotage d\'Activités';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'client_name', 'country', 'description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Client' => $record->client_name ?? '—',
            'Année'  => $record->year ?? '—',
            'Statut' => $record->status ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ReferenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReferenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReferencesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListReferences::route('/'),
            'create' => CreateReference::route('/create'),
            'view'   => ViewReference::route('/{record}'),
            'edit'   => EditReference::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}

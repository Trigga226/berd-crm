<?php

namespace App\Filament\Resources\AvisManifestations;

use App\Filament\Resources\AvisManifestations\Pages\CreateAvisManifestation;
use App\Filament\Resources\AvisManifestations\Pages\EditAvisManifestation;
use App\Filament\Resources\AvisManifestations\Pages\ListAvisManifestations;
use App\Filament\Resources\AvisManifestations\Pages\ViewAvisManifestation;
use App\Filament\Resources\AvisManifestations\Schemas\AvisManifestationForm;
use App\Filament\Resources\AvisManifestations\Schemas\AvisManifestationInfolist;
use App\Filament\Resources\AvisManifestations\Tables\AvisManifestationsTable;
use App\Models\AvisManifestation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvisManifestationResource extends Resource
{
    protected static ?string $model = AvisManifestation::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Pilotage d\'Activités';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'reference_number', 'client_name', 'description'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Réf.'   => $record->reference_number ?? '—',
            'Statut' => $record->status ?? '—',
            'Score'  => $record->ai_score !== null ? $record->ai_score . '/10' : '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AvisManifestationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AvisManifestationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvisManifestationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ManifestationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvisManifestations::route('/'),
            'create' => CreateAvisManifestation::route('/create'),
            'view' => ViewAvisManifestation::route('/{record}'),
            'edit' => EditAvisManifestation::route('/{record}/edit'),
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

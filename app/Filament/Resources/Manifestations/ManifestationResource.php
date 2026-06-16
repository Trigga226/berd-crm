<?php

namespace App\Filament\Resources\Manifestations;

use App\Filament\Resources\Manifestations\Pages\CreateManifestation;
use App\Filament\Resources\Manifestations\Pages\EditManifestation;
use App\Filament\Resources\Manifestations\Pages\ListManifestations;
use App\Filament\Resources\Manifestations\Pages\ViewManifestation;
use App\Filament\Resources\Manifestations\Schemas\ManifestationForm;
use App\Filament\Resources\Manifestations\Schemas\ManifestationInfolist;
use App\Filament\Resources\Manifestations\Tables\ManifestationsTable;
use App\Models\Manifestation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManifestationResource extends Resource
{
    protected static ?string $model = Manifestation::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Pilotage d\'Activités';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'client_name';

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->avisManifestation?->title ?? $record->client_name ?? "Manifestation #{$record->id}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['client_name', 'country', 'observation', 'avisManifestation.title'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Statut' => match ($record->status ?? '') {
                'draft'     => 'Brouillon',
                'submitted' => 'Soumis',
                'won'       => 'Gagné',
                'lost'      => 'Perdu',
                'abandoned' => 'Abandonné',
                default     => $record->status ?? '—',
            },
            'Client' => $record->client_name ?? '—',
            'Pays'   => $record->country ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ManifestationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ManifestationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ManifestationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OffersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListManifestations::route('/'),
            'create' => CreateManifestation::route('/create'),
            'view' => ViewManifestation::route('/{record}'),
            'edit' => EditManifestation::route('/{record}/edit'),
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

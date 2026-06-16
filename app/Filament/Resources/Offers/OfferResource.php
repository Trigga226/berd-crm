<?php

namespace App\Filament\Resources\Offers;

use App\Filament\Resources\Offers\Pages;
use App\Filament\Resources\Offers\Schemas\OfferForm;
use App\Filament\Resources\Offers\Schemas\OfferInfolist;
use App\Filament\Resources\Offers\Tables\OffersTable;
use App\Models\Offer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';
    protected static \UnitEnum|string|null $navigationGroup = 'Pilotage d\'Activités';
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Offres';

    protected static ?string $modelLabel = 'Offre';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'country'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Résultat' => $record->result ?? '—',
            'Client'   => $record->client?->name ?? '—',
            'Pays'     => $record->country ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return OfferForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OfferInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OffersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PartnersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'view' => Pages\ViewOffer::route('/{record}'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
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

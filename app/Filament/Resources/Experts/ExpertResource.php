<?php

namespace App\Filament\Resources\Experts;

use App\Filament\Resources\Experts\Pages\CreateExpert;
use App\Filament\Resources\Experts\Pages\EditExpert;
use App\Filament\Resources\Experts\Pages\ListExperts;
use App\Filament\Resources\Experts\Pages\ViewExpert;
use App\Filament\Resources\Experts\Schemas\ExpertForm;
use App\Filament\Resources\Experts\Schemas\ExpertInfolist;
use App\Filament\Resources\Experts\Tables\ExpertsTable;
use App\Models\Expert;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpertResource extends Resource
{
    protected static ?string $model = Expert::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Tiers & Ressources';
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordTitleAttribute = 'last_name';

    public static function form(Schema $schema): Schema
    {
        return ExpertForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExpertInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpertsTable::configure($table);
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
            'index' => ListExperts::route('/'),
            'create' => CreateExpert::route('/create'),
            'analysis' => \App\Filament\Resources\Experts\Pages\ExpertAnalysis::route('/analysis'),
            'view' => ViewExpert::route('/{record}'),
            'edit' => EditExpert::route('/{record}/edit'),
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

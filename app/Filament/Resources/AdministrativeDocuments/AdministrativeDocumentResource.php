<?php

namespace App\Filament\Resources\AdministrativeDocuments;

use App\Filament\Resources\AdministrativeDocuments\Pages\ManageAdministrativeDocuments;
use App\Models\AdministrativeDocument;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdministrativeDocumentResource extends Resource
{
    protected static ?string $model = AdministrativeDocument::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Organisation Interne';
    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\AdministrativeDocuments\Schemas\AdministrativeDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\AdministrativeDocuments\Schemas\AdministrativeDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\AdministrativeDocuments\Tables\AdministrativeDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAdministrativeDocuments::route('/'),
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

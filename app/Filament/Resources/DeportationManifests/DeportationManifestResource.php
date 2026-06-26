<?php

namespace App\Filament\Resources\DeportationManifests;

use App\Filament\Resources\DeportationManifests\Pages\CreateDeportationManifest;
use App\Filament\Resources\DeportationManifests\Pages\EditDeportationManifest;
use App\Filament\Resources\DeportationManifests\Pages\ListDeportationManifests;
use App\Filament\Resources\DeportationManifests\Schemas\DeportationManifestForm;
use App\Filament\Resources\DeportationManifests\Tables\DeportationManifestsTable;
use App\Models\DeportationManifest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeportationManifestResource extends Resource
{
    protected static ?string $model = DeportationManifest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Manifests';

    public static function getNavigationGroup(): ?string
    {
        return 'Operational';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function form(Schema $schema): Schema
    {
        return DeportationManifestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeportationManifestsTable::configure($table);
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
            'index' => ListDeportationManifests::route('/'),
            'create' => CreateDeportationManifest::route('/create'),
            'edit' => EditDeportationManifest::route('/{record}/edit'),
        ];
    }
}

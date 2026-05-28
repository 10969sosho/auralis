<?php

namespace App\Filament\Resources\BoardingLogs;

use App\Filament\Resources\BoardingLogs\Pages\CreateBoardingLog;
use App\Filament\Resources\BoardingLogs\Pages\EditBoardingLog;
use App\Filament\Resources\BoardingLogs\Pages\ListBoardingLogs;
use App\Filament\Resources\BoardingLogs\Schemas\BoardingLogForm;
use App\Filament\Resources\BoardingLogs\Tables\BoardingLogsTable;
use App\Models\BoardingLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoardingLogResource extends Resource
{
    protected static ?string $model = BoardingLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BoardingLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardingLogsTable::configure($table);
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
            'index' => ListBoardingLogs::route('/'),
            'create' => CreateBoardingLog::route('/create'),
            'edit' => EditBoardingLog::route('/{record}/edit'),
        ];
    }
}

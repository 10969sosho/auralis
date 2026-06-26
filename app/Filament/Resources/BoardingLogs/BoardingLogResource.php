<?php

namespace App\Filament\Resources\BoardingLogs;

use App\Filament\Resources\BoardingLogs\Pages\ListBoardingLogs;
use App\Filament\Resources\BoardingLogs\Tables\BoardingLogsTable;
use App\Models\BoardingLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoardingLogResource extends Resource
{
    protected static ?string $model = BoardingLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $navigationLabel = 'Boarding Logs';

    public static function getNavigationGroup(): ?string
    {
        return 'Operational';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function table(Table $table): Table
    {
        return BoardingLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBoardingLogs::route('/'),
        ];
    }
}

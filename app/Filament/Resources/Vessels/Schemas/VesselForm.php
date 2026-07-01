<?php

namespace App\Filament\Resources\Vessels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Schedule;

class VesselForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('capacity')
                    ->label('Total Capacity (auto-calculated)')
                    ->disabled()
                    ->dehydrated(true)
                    ->helperText('Auto-calculated from VIP + Regular capacity.'),
                TextInput::make('vip_capacity')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->disabled(fn (Get $get): bool => self::isLocked($get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateCapacity($get, $set))
                    ->helperText(fn (Get $get): string => self::isLocked($get) ? 'This vessel has active schedules. Deactivate them first to edit.' : ''),
                TextInput::make('regular_capacity')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->disabled(fn (Get $get): bool => self::isLocked($get))
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateCapacity($get, $set))
                    ->helperText(fn (Get $get): string => self::isLocked($get) ? 'This vessel has active schedules. Deactivate them first to edit.' : ''),
                TextInput::make('free_baggage')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }

    private static function updateCapacity(Get $get, Set $set): void
    {
        $vip = (int) ($get('vip_capacity') ?? 0);
        $regular = (int) ($get('regular_capacity') ?? 0);
        $set('capacity', $vip + $regular);
    }

    private static function isLocked(Get $get): bool
    {
        if ($get('id') === null) {
            return false;
        }

        return Schedule::where('vessel_id', $get('id'))
            ->where('is_active', true)
            ->exists();
    }
}

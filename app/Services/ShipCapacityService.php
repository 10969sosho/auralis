<?php

namespace App\Services;

use App\Models\OpenReturnTicket;
use Illuminate\Support\Facades\DB;

class ShipCapacityService
{
    public function capacity(): int
    {
        return (int) DB::table('master_ships')->sum('capacity');
    }

    public function usedOn(string $date): int
    {
        return OpenReturnTicket::whereDate('return_date', $date)
            ->whereIn('status', ['assigned', 'used'])
            ->count();
    }

    public function canAllocate(string $date, int $qty = 1): bool
    {
        if ($qty <= 0) {
            return true;
        }

        return $this->usedOn($date) + $qty <= $this->capacity();
    }
}

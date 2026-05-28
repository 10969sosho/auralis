<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassengerProfile extends Model
{
    protected $fillable = ['user_id', 'full_name', 'gender', 'birth_date', 'nationality', 'passport_number', 'phone', 'relationship'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

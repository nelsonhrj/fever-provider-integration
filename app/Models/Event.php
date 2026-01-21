<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'title', 'sell_mode', 'starts_at', 'ends_at',
        'zones', 'raw_data', 'last_seen_at',
    ];

    protected $casts = [
        'zones' => 'array',
        'raw_data' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'options',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * المزايدات التي تستخدم هذه الخاصية
     */
    public function bids()
    {
        return $this->belongsToMany(Bids::class, 'bid_property', 'property_id', 'bid_id')
            ->withPivot('value')
            ->withTimestamps();
    }
}

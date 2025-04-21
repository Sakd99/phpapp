<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidProperty extends Model
{
    use HasFactory;

    protected $table = 'bid_property';

    protected $fillable = [
        'bid_id',
        'property_id',
        'value',
    ];

    public function bid()
    {
        return $this->belongsTo(Bids::class, 'bid_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'banner_image',
        'banner_title',
        'banner_address',
        'banner_description',
        'banner_date',
    ];
}

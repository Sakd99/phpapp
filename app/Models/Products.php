<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'product_name',
        'product_category',
        'product_price',
        'product_stock',
        'product_status',
        'product_description',
        'product_image1',
        'product_image2',
        'product_image3',
        'prodeuct_discount',
        'product_rating',
        'product_review',
        'prodeuct_color',
        'product_size',
        'product_weight',
        'product_dimension',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['product_price' => 'float'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model // تغيير اسم الكلاس إلى المفرد
{
    protected $table = 'products'; // اسم الجدول في قاعدة البيانات
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
        'product_discount', // إصلاح الخطأ الإملائي من "prodeuct_discount" إلى "product_discount"
        'product_rating',
        'product_review',
        'product_color', // إصلاح الخطأ الإملائي من "prodeuct_color" إلى "product_color"
        'product_size',
        'product_weight',
        'product_dimension',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['product_price' => 'float'];
}

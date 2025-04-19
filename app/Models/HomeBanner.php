<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'subsubcategory_id',
        'image',
        'priority',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function subSubCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subsubcategory_id');
    }
}

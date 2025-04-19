<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'category_id',
        'parent_id', // إضافة معرف الفئة الفرعية الرئيسية
    ];

    /**
     * Get the category that owns the subcategory.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategories that belong to this subcategory.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'parent_id');
    }

    /**
     * Get the parent subcategory that owns this subcategory.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'parent_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Bids extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'product_name',
        'product_description',
        'initial_price',
        'current_price',
        'end_time',
        'product_image1',
        'product_image2',
        'product_image3',
        'product_image4',
        'product_image5',
        'shipping_option',
        'user_id',
        'category_id',
        'subcategory_id',
        'parent_id', // تأكد من أن الحقل موجود هنا
        'governorate',
        'region',
        'product_condition',
        'product_origin', // منشأ المنتج (اسم الدولة)
        'status',
        'delivery_address',
        'fcm_token',
        'available_colors', // الألوان المتاحة للمنتج
        'available_sizes', // الأحجام المتاحة للمنتج
        'properties_data', // بيانات الخصائص المخزنة كـ JSON
    ];

    /**
     * Get the category that owns the bid.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory that owns the bid.
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Get the bidders for the bid.
     */
    public function bidders()
    {
        return $this->belongsToMany(User::class, 'bid_user', 'bid_id', 'user_id')
            ->withPivot('bid_amount', 'shipping_address', 'phone_id') // تضمين phone_id في الـ pivot
            ->withTimestamps();
    }


    /**
     * Get the user who created the bid.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the buyer of the bid (if the bid is completed).
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the parent subcategory.
     */
    public function parentSubCategory()
    {
        return $this->belongsTo(SubCategory::class, 'parent_id');
    }

    /**
     * Get the orders associated with the bid.
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'bid_id');
    }

    /**
     * الخصائص المرتبطة بالمزايدة
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'bid_property', 'bid_id', 'property_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function routeNotificationForOneSignal()
    {
        return $this->fcm_token; // استخدام fcm_token لإرسال الإشعارات
    }

    /**
     * Get the product image URL attribute.
     *
     * @return string
     */
    public function getProductImage1UrlAttribute()
    {
        if (empty($this->product_image1)) {
            return null;
        }

        // إذا كان المسار يبدأ بـ /storage/
        if (str_starts_with($this->product_image1, '/storage/')) {
            // إزالة /storage من بداية المسار وإضافة مسار كامل باستخدام asset
            $path = str_replace('/storage/', '', $this->product_image1);
            return asset('storage/' . $path);
        }

        // إذا كان المسار يبدأ بـ http:// أو https://
        if (str_starts_with($this->product_image1, 'http://') || str_starts_with($this->product_image1, 'https://')) {
            return $this->product_image1;
        }

        // المسار العادي
        return asset('storage/' . $this->product_image1);
    }

    /**
     * Get the product image URL attribute.
     *
     * @return string
     */
    public function getProductImage2UrlAttribute()
    {
        if (empty($this->product_image2)) {
            return null;
        }

        // إذا كان المسار يبدأ بـ /storage/
        if (str_starts_with($this->product_image2, '/storage/')) {
            // إزالة /storage من بداية المسار وإضافة مسار كامل باستخدام asset
            $path = str_replace('/storage/', '', $this->product_image2);
            return asset('storage/' . $path);
        }

        // إذا كان المسار يبدأ بـ http:// أو https://
        if (str_starts_with($this->product_image2, 'http://') || str_starts_with($this->product_image2, 'https://')) {
            return $this->product_image2;
        }

        // المسار العادي
        return asset('storage/' . $this->product_image2);
    }

    /**
     * Get the product image URL attribute.
     *
     * @return string
     */
    public function getProductImage3UrlAttribute()
    {
        if (empty($this->product_image3)) {
            return null;
        }

        // إذا كان المسار يبدأ بـ /storage/
        if (str_starts_with($this->product_image3, '/storage/')) {
            // إزالة /storage من بداية المسار وإضافة مسار كامل باستخدام asset
            $path = str_replace('/storage/', '', $this->product_image3);
            return asset('storage/' . $path);
        }

        // إذا كان المسار يبدأ بـ http:// أو https://
        if (str_starts_with($this->product_image3, 'http://') || str_starts_with($this->product_image3, 'https://')) {
            return $this->product_image3;
        }

        // المسار العادي
        return asset('storage/' . $this->product_image3);
    }

    /**
     * Get the available colors attribute.
     *
     * @param  string|null  $value
     * @return array
     */
    public function getAvailableColorsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the available colors attribute.
     *
     * @param  array  $value
     * @return void
     */
    public function setAvailableColorsAttribute($value)
    {
        $this->attributes['available_colors'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the available sizes attribute.
     *
     * @param  string|null  $value
     * @return array
     */
    public function getAvailableSizesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the available sizes attribute.
     *
     * @param  array  $value
     * @return void
     */
    public function setAvailableSizesAttribute($value)
    {
        $this->attributes['available_sizes'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * الحصول على بيانات الخصائص
     *
     * @param  string|null  $value
     * @return array
     */
    public function getPropertiesDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * تعيين بيانات الخصائص
     *
     * @param  array|string  $value
     * @return void
     */
    public function setPropertiesDataAttribute($value)
    {
        $this->attributes['properties_data'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * الحصول على قيمة خاصية محددة
     *
     * @param  int  $propertyId
     * @return mixed|null
     */
    public function getPropertyValue($propertyId)
    {
        $propertiesData = $this->properties_data;
        return $propertiesData[$propertyId] ?? null;
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Orders extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',            // معرف المشتري
        'order_number',       // رقم الطلب
        'order_status',       // حالة الطلب
        'order_note',         // ملاحظات الطلب
        'buyer_name',         // اسم المشتري
        'buyer_email',        // بريد المشتري الإلكتروني
        'buyer_phone',        // هاتف المشتري
        'buyer_address',      // عنوان المشتري
        'buyer_city',         // مدينة المشتري
        'total',              // المجموع الكلي للطلب
        'seller_id',          // معرف البائع
        'bid_id',             // معرف المزايدة
        'fcm_token',          // fcm_token للبائع
    ];

    /**
     * العلاقة مع المشتري (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // المشتري
    }

    /**
     * العلاقة مع المزايدة
     */
    public function bid()
    {
        return $this->belongsTo(Bids::class, 'bid_id'); // تأكد من وجود العلاقة مع المزايدة
    }

    /**
     * العلاقة مع عناصر الطلب
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id'); // عناصر الطلب المرتبطة بالطلب
    }

    /**
     * العلاقة مع البائع
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id'); // البائع المرتبط بالطلب
    }

    /**
     * إرجاع phone_id من جدول bid_user لإرسال الإشعارات للمشتري
     */
    public function routeNotificationForOneSignal()
    {
        $bidder = $this->bid->bidders()->where('user_id', $this->user_id)->first();
        return $bidder ? $bidder->pivot->phone_id : null; // إعادة phone_id
    }



    /**
     * إرجاع fcm_token للبائع لإرسال الإشعارات له
     */
    public function routeNotificationForSeller()
    {
        return $this->fcm_token; // استخدام fcm_token لإرسال الإشعارات للبائع
    }
}

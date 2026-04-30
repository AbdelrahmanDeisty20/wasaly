<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'unit_price',
        'discount_amount',
        'quantity',
        'total_price',
        'customer_name',
        'customer_phone',
        'customer_address',
        'payment_method',
        'status',
        'governorate_id',
        'center_id',
        'coupon_code',
        'shipping_cost',
        'provider_id',
        'service_id',
        'booking_id',
        'available_date_id',
        'available_time_id',
        'problem_description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

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
        'quantity',
        'total_price',
        'customer_name',
        'customer_phone',
        'customer_address',
        'payment_method',
        'status',
        'governorate_id',
        'region',
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

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

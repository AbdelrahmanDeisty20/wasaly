<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'type',
        'value',
        'min_order_value',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function isValidForOrder($orderTotal)
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) return false;
        if ($this->end_date && $now->gt($this->end_date)) return false;
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        
        if ($orderTotal < $this->min_order_value) return false;
        
        return true;
    }

    public function calculateDiscount($orderTotal)
    {
        if ($this->type === 'fixed') {
            return min($this->value, $orderTotal);
        } else {
            return ($this->value / 100) * $orderTotal;
        }
    }}

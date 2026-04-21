<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'phone',
        'email',
        'code',
        'type',
        'reset_token',
        'user_id',
        'expires_at',
        'verified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

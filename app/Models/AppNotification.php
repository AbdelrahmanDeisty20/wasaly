<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title_ar',
        'title_en',
        'message_ar',
        'message_en',
        'type',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    protected $attributes = [
        'data' => '[]',
    ];

    public function getTitleAttribute()
    {
        if (app()->getLocale() === 'ar') {
            return $this->title_ar ?? $this->title_en;
        }
        return $this->title_en ?? $this->title_ar;
    }

    public function getMessageAttribute()
    {
        if (app()->getLocale() === 'ar') {
            return $this->message_ar ?? $this->message_en;
        }
        return $this->message_en ?? $this->message_ar;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userStates()
    {
        return $this->hasMany(UserNotificationState::class, 'notification_id');
    }

    public function currentUserState()
    {
        return $this->hasOne(UserNotificationState::class, 'notification_id')
            ->where('user_id', auth()->id());
    }
}

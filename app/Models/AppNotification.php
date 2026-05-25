<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notifications extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'user_code',
        'notification_type',
        'important',
        'status',
        'lock_warehouse',
        'is_read',
        'content',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function notification_types()
    {
        return $this->belongsTo(Notification_types::class, 'notification_type', 'id');
    }

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_code', 'code');
    }
}
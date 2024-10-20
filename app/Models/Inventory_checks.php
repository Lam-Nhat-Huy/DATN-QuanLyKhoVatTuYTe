<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class Inventory_checks extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'user_code',
        'recheck_user_code',
        'check_date',
        'note',
        'status',
        'equipment_note',
        'check_count',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function details()
    {
        return $this->hasMany(Inventory_check_details::class, 'inventory_check_code', 'code');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_code', 'code');
    }

    public function recheckUser()
    {
        return $this->belongsTo(Users::class, 'recheck_user_code', 'code');
    }
}
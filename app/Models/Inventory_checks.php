<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'check_date',
        'note',
        'status',
        'equipment_note',
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
}
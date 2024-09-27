<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory_check_details extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'inventory_check_code',
        'equipment_code',
        'current_quantity',
        'actual_quantity',
        'unequal',
        'batch_number',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipments::class, 'equipment_code', 'code');
    }
}

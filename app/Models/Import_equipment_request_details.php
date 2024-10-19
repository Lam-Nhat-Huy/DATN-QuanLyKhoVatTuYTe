<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import_equipment_request_details extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'id',
        'import_request_code',
        'equipment_code',
        'quantity',
        'price',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function equipments()
    {
        return $this->belongsTo(Equipments::class, 'equipment_code', 'code');
    }

    public function importEquipmentRequests()
    {
        return $this->belongsTo(Import_equipment_requests::class, 'import_request_code', 'code');
    }
}

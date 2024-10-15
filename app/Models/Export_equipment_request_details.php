<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export_equipment_request_details extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'id',
        'export_request_code',
        'equipment_code',
        'quantity',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function equipments()
    {
        return $this->belongsTo(Equipments::class, 'equipment_code', 'code');
    }

    public function exportEquipmentRequests()
    {
        return $this->belongsTo(Export_equipment_requests::class, 'export_request_code', 'code');
    }
}

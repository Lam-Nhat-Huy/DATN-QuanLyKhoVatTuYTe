<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export_equipment_requests extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'user_code',
        'department_code',
        'reason_export',
        'note',
        'status',
        'request_date',
        'required_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_code', 'code');
    }

    public function departments()
    {
        return $this->belongsTo(Departments::class, 'department_code', 'code');
    }

    public function export_equipment_request_details()
    {
        return $this->hasMany(Export_equipment_request_details::class, 'export_request_code', 'code');
    }
}

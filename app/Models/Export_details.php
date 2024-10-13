<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export_details extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    public $timestamps = false;
    
    protected $keyType = 'string';

    use HasFactory;

    protected $fillable = [
        'code',
        'export_code',
        'equipment_code',
        'batch_number',
        'quantity'
    ];

    public function export()
    {
        return $this->belongsTo(Exports::class, 'export_code', 'code'); 
    }

    public function equipments(){
        return $this->belongsTo(Equipments::class, 'equipment_code', 'code');
    }
}

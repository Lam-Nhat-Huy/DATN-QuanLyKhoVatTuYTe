<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exports extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'department_code',
        'note',
        'status',
        'export_date',
        'created_by'
    ];

    public function exportDetail()
    {
        return $this->hasMany(Export_details::class, 'export_code', 'code');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'created_by', 'code');
    }
}
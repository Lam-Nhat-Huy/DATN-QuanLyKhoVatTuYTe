<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventories extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'equipment_code',
        'batch_number',
        'current_quantity',
        'import_code',
        'export_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

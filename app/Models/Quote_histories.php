<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote_histories extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'supplier_code',
        'file_excel',
        'user_code',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_code', 'code');
    }

    public function suppliers()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_code', 'code');
    }
}

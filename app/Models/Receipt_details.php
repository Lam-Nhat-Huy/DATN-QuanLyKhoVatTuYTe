<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'receipt_code',
        'batch_number',
        'expire_date',
        'quantity',
        'price',
        'discount',
        'created_at',
        'updated_at',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipt_code', 'code');
    }

    public function equipments()
    {
        return $this->belongsTo(Equipments::class, 'equipment_code', 'code');
    }
}
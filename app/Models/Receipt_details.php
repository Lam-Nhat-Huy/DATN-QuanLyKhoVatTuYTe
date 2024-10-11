<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   Receipt_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_code',
        'batch_number',
        'expiry_date',
        'manufacture_date',
        'quantity',
        'VAT',
        'discount',
        'price',
        'equipment_code',
        'created_at',
        'updated_at',
        'deleted_at',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\Storage;

class Equipments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipments';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'barcode',
        'description',
        'price',
        'country',
        'equipment_type_code',
        'supplier_code',
        'unit_code',
        'image',
        'expiry_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Tạo mã vạch 1D
    protected static function booted()
    {
        static::created(function ($equipments) {
            $barcode = new DNS1D();
            $barcodeData = $barcode->getBarcodePNG($equipments->barcode, 'C128');
            $barcodeImage = base64_decode($barcodeData);
            $barcodePath = 'barcodes/' . $equipments->barcode . '.png';
            Storage::disk('public')->put($barcodePath, $barcodeImage);
            $equipments->barcode = $barcodePath;
            $equipments->save();
        });
    }

    // Tạo mã vạch 2D (QRCODE)
    // public function generateBarcode()
    // {
    //     $barcode = new DNS2D();
    //     return $barcode->getBarcodePNGPath($this->barcode, 'C128', 4, 4);
    // }

    // Định nghĩa mối quan hệ với Inventories
    public function inventories()
    {
        return $this->hasMany(Inventories::class, 'equipment_code', 'code');
    }

    // Định nghĩa mối quan hệ với Units
    public function units()
    {
        return $this->belongsTo(Units::class, 'unit_code', 'code');
    }

    // Định nghĩa mối quan hệ với Suppliers
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_code', 'code');
    }

    // Định nghĩa mối quan hệ với Equipment_types
    public function equipmentType()
    {
        return $this->belongsTo(Equipment_types::class, 'equipment_type_code', 'code');
    }
}
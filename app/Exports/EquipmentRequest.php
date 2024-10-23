<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class EquipmentRequest implements FromCollection, WithHeadings
{
    protected $equipmentRequestList;

    /**
     * Nhận dữ liệu thông qua constructor.
     */
    public function __construct($equipmentRequestList)
    {
        $this->equipmentRequestList = $equipmentRequestList;
    }

    /**
     * Trả về dữ liệu cần xuất.
     */
    public function collection()
    {
        $data = [];
        $count = 1;

        foreach ($this->equipmentRequestList as $item) {
            $data[] = [
                'STT'              => $count++,
                'equipment_name'   => $item->equipments->name,
                'unit_name'     => $item->equipments->units->name,
                'quantity_request' => $item->quantity,
                'your_quantity_have'  => '0',
                'price'   => '0',
            ];
        }

        return new Collection($data);
    }


    /**
     * Tiêu đề của các cột trong Excel.
     */
    public function headings(): array
    {
        return [
            'STT',
            'Tên thiết bị',
            'Đơn vị tính',
            'Số lượng yêu cầu',
            'Số lượng có sẵn',
            'Đơn giá',
        ];
    }
}

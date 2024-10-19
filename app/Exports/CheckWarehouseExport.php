<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class CheckWarehouseExport implements FromCollection, WithHeadings
{
    protected $equipments;

    /**
     * Nhận dữ liệu thông qua constructor.
     */
    public function __construct($equipments)
    {
        $this->equipments = $equipments;
    }

    /**
     * Trả về dữ liệu cần xuất.
     */
    public function collection()
    {
        $data = [];
        $count = 1;

        foreach ($this->equipments as $equipment) {
            foreach ($equipment->inventories as $inventory) {
                $data[] = [
                    'STT'              => $count++,
                    'equipment_code'   => $inventory->equipment_code,
                    'equipment_name'   => $equipment->name,
                    'batch_number'     => $inventory->batch_number,
                    'current_quantity' => $inventory->current_quantity,
                    'actual_quantity'  => '',
                    // 'unequal'          => '',
                    'equipment_note'   => '',
                ];
            }
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
            'Mã thiết bị',
            'Tên thiết bị',
            'Số lô',
            'Tồn kho',
            'Thực tế',
            // 'Lệch',
            'Ghi chú',
        ];
    }
}
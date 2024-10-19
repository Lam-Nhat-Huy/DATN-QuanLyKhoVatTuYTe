<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Equipments;
use App\Models\Inventories;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CheckWarehouseImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $equipment = Equipments::where('code', $row['ma_thiet_bi'])->first();

        if ($equipment) {
            $inventory = Inventories::where('equipment_code', $equipment->code)
                ->where('batch_number', $row['so_lo'])
                ->first();

            if ($inventory) {
                $inventory->update([
                    'actual_quantity' => $row['thuc_te'],
                    'note' => $row['ghi_chu'],
                ]);
            }
        }
    }
}
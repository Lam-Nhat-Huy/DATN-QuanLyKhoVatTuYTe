<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Equipment_types;
use App\Models\Equipments;
use App\Models\Inventories;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $route = 'inventory';

    public function index(Request $request)
    {
        $title = 'Tồn Kho';
        $equipmentType = Equipment_types::all();
        $totalEquipments = Equipments::with('inventories')->count();

        // Đừng ghi đè biến $equipments trong hàm index
        $initialEquipments = Equipments::with('inventories')->orderBy('created_at', 'desc')->paginate(100);

        $outOfStockCount = 0;
        $lowStockCount = 0;
        $totalInventories = [];

        foreach ($initialEquipments as $equipment) {
            $totalQuantity = $equipment->inventories->sum('current_quantity');
            $totalInventories[$equipment->code] = [
                'inventories' => $equipment->inventories,
                'total_quantity' => $totalQuantity,
            ];

            if ($totalQuantity < 1) {
                $outOfStockCount++;
            } elseif ($totalQuantity <= 10) {
                $lowStockCount++;
            }
        }

        if ($request->ajax()) {
            return view('inventory.index', [
                'equipments' => $initialEquipments,
                'inventories' => $totalInventories,
                'outOfStockCount' => $outOfStockCount,
                'lowStockCount' => $lowStockCount,
            ]);
        }

        return view("{$this->route}.inventory", [
            'inventories' => $totalInventories,
            'equipments' => $initialEquipments,
            'equipmentType' => $equipmentType,
            'totalEquipments' => $totalEquipments,
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
            'title' => $title,
        ]);
    }


    public function filter(Request $request)
    {
        $title = 'Tồn kho';
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $category = $request->input('category');
        $expiry_date = $request->input('expiry_date');
        $quantity = $request->input('quantity');
        $search = $request->input('search');

        $equipments = Equipments::with('inventories');

        // Các điều kiện lọc
        if (!empty($search)) {
            $equipments->where('name', 'LIKE', "%{$search}%");
        }
        if (!empty($start_date) || !empty($end_date)) {
            $equipments->whereHas('inventories', function ($subQuery) use ($start_date, $end_date) {
                if (!empty($start_date)) {
                    $subQuery->whereDate('expiry_date', '>=', $start_date);
                }
                if (!empty($end_date)) {
                    $subQuery->whereDate('expiry_date', '<=', $end_date);
                }
            });
        }
        if (!empty($category)) {
            $equipments->whereHas('equipmentType', function ($subQuery) use ($category) {
                $subQuery->where('code', $category);
            });
        }
        if (!empty($expiry_date)) {
            $now = now();
            $fiveMonthsLater = now()->addMonths(5);

            $equipments->whereHas('inventories', function ($subQuery) use ($expiry_date, $now, $fiveMonthsLater) {
                if ($expiry_date == 'valid') {
                    $subQuery->whereDate('expiry_date', '>', $fiveMonthsLater);
                } elseif ($expiry_date == 'expiring_soon') {
                    $subQuery->whereDate('expiry_date', '>', $now)
                        ->whereDate('expiry_date', '<=', $fiveMonthsLater);
                } elseif ($expiry_date == 'expired') {
                    $subQuery->whereDate('expiry_date', '<=', $now);
                }
            });
        }
        if (!empty($quantity)) {
            $equipments->whereHas('inventories', function ($subQuery) use ($quantity) {
                if ($quantity === 'enough') {
                    $subQuery->where('current_quantity', '>=', 25);
                } elseif ($quantity === 'low') {
                    $subQuery->where('current_quantity', '<', 25);
                } elseif ($quantity === 'out_stock') {
                    $subQuery->where('current_quantity', '=', 0);
                }
            });
        }

        // Lấy dữ liệu đã lọc
        $filteredEquipments = $equipments->orderBy('created_at', 'desc');

        // Kiểm tra số lượng bản ghi
        $totalFiltered = $filteredEquipments->count();

        // Phân trang chỉ khi có đủ 10 bản ghi
        if ($totalFiltered > 10) {
            $filteredEquipments = $filteredEquipments->paginate(100);
        } else {
            $filteredEquipments = $filteredEquipments->get(); // Lấy toàn bộ nếu ít hơn 10
        }

        $totalInventories = [];
        foreach ($filteredEquipments as $equipment) {
            $totalQuantity = $equipment->inventories->sum('current_quantity');
            $totalInventories[$equipment->code] = [
                'inventories' => $equipment->inventories,
                'total_quantity' => $totalQuantity,
            ];
        }

        return view("{$this->route}.search", [
            'title' => $title,
            'inventories' => $totalInventories,
            'equipments' => $filteredEquipments,
            'totalEquipment' => Equipments::count(),
        ]);
    }
}
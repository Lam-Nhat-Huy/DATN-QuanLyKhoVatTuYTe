<?php

namespace App\Http\Controllers\Warehouse;

use App\Exports\CheckWarehouseExport;
use App\Http\Controllers\Controller;
use App\Imports\CheckWarehouseImport;
use App\Models\Equipments;
use App\Models\Export_details;
use App\Models\Exports;
use App\Models\Inventories;
use App\Models\Inventory_check_details;
use App\Models\Inventory_checks;
use App\Models\Notifications;
use App\Models\Receipt_details;
use App\Models\Receipts;
use App\Models\Suppliers;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CheckWarehouseController extends Controller
{
    protected $route = 'check_warehouse';

    public function index()
    {
        $title = 'Kiểm Kho';

        $inventoryChecks = Inventory_checks::with(['details.equipment', 'user'])
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $countAll = Inventory_checks::count();
        $countBalanced = Inventory_checks::where('status', 1)->count();
        $countDraft = Inventory_checks::where('status', 0)->count();
        $countCanceled = Inventory_checks::where('status', 3)->count();

        $users = Users::all();

        return view("{$this->route}.check", compact(
            'title',
            'inventoryChecks',
            'users',
            'countAll',
            'countBalanced',
            'countDraft',
            'countCanceled'
        ));
    }

    public function create()
    {
        $title = 'Kiểm Kho';

        $action = 'create';

        $statusMessage = 'Đang kiểm';

        $equipmentsWithStock = Equipments::whereHas('inventories', function ($query) {
            $query->where('current_quantity', '>', 0);
        })->with(['inventories' => function ($query) {
            $query->select('equipment_code', 'current_quantity', 'batch_number')
                ->where('current_quantity', '>', 0);
        }])->get();

        return view("{$this->route}.form", compact('title', 'action', 'equipmentsWithStock', 'statusMessage'));
    }

    public function exportCheckWarehouseExcel()
    {
        $equipmentsWithStock = Equipments::whereHas('inventories', function ($query) {
            $query->where('current_quantity', '>', 0);
        })->with(['inventories' => function ($query) {
            $query->select('equipment_code', 'current_quantity', 'batch_number')
                ->where('current_quantity', '>', 0);
        }])->get();

        return Excel::download(new CheckWarehouseExport($equipmentsWithStock), 'FileKiemKhoTatCaThietBi.xlsx');
    }

    public function importCheckWarehouseExcel(Request $request)
    {
        $title = 'Excel';

        $action = 'excel';

        $statusMessage = 'Đang kiểm';

        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);

        $equipmentsWithStock = Inventories::where('current_quantity', '>', 0)
            ->join('equipments', 'inventories.equipment_code', '=', 'equipments.code')
            ->select('inventories.equipment_code', 'inventories.current_quantity', 'inventories.batch_number', 'equipments.name')
            ->get();

        $data = Excel::toArray(new CheckWarehouseImport, $request->file('file'));

        $importedData = [];

        foreach ($data[0] as $row) {
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

                    $importedData[] = [
                        'equipment_code' => $inventory->equipment_code,
                        'current_quantity' => $inventory->current_quantity,
                        'actual_quantity' => $row['thuc_te'],
                        'unequal' => ($inventory->current_quantity !== $row['thuc_te']) ? abs($inventory->current_quantity - $row['thuc_te']) : 0,
                        'batch_number' => $row['so_lo'],
                        'equipment_note' => $row['ghi_chu'],
                        'check_date' => $row['check_date'] ?? now()->toDateString(),
                        'note' => '',
                        'status' => '0',
                        'created_by' => $row['created_by'] ?? 'U002',
                        'name' => $equipment->name,
                    ];
                }
            }
        }

        $equipmentsWithExcel = $importedData;

        return view("{$this->route}.form", compact('title', 'action', 'equipmentsWithExcel', 'equipmentsWithStock', 'statusMessage'));
    }

    public function edit($code)
    {
        $title = 'Chỉnh sửa';

        $action = 'edit';

        $statusMessage = 'Đang sửa';

        $inventoryCheck = Inventory_checks::findOrFail($code);

        $equipmentsWithStock = Equipments::whereHas('inventories', function ($query) {
            $query->where('current_quantity', '>', 0);
        })->with(['inventories' => function ($query) {
            $query->select('equipment_code', 'current_quantity', 'batch_number')
                ->where('current_quantity', '>', 0);
        }])->get();

        $equipmentsWithJson = $this->showInventoryCheckEdits($code);

        return view("{$this->route}.form", compact('title', 'action', 'equipmentsWithJson', 'inventoryCheck', 'equipmentsWithStock', 'statusMessage'));
    }

    public function update(Request $request, $code)
    {
        $inventoryCheck = Inventory_checks::where('code', $code)->firstOrFail();

        $materialData = json_decode($request->input('materialData'), true);

        if (empty($materialData)) {
            toastr()->error('Không có dữ liệu để cập nhật.');
            return redirect()->back();
        }

        $inventoryCheckData = [
            'check_date' => $materialData[0]['check_date'],
            'note' => $materialData[0]['note'],
            'user_code' => $materialData[0]['created_by'],
            'status' => $materialData[0]['status']
        ];

        $inventoryCheck->update($inventoryCheckData);

        Inventory_check_details::where('inventory_check_code', $code)->delete();

        $materialsForExport = [];
        $materialsForImport = [];
        $inventoryCheckDetailData = [];

        foreach ($materialData as $material) {
            $inventoryCheckDetailData[] = [
                'inventory_check_code' => $code,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'current_quantity' => $material['current_quantity'],
                'actual_quantity' => $material['actual_quantity'],
                'unequal' => $material['unequal'],
                'equipment_note' => $material['equipment_note']
            ];

            $this->handleStockDiscrepancy($material, $materialsForExport, $materialsForImport);
        }

        if ($inventoryCheckData['status'] == 1) {
            if (!empty($materialsForExport)) {
                $this->createExportReceipt($materialsForExport);
            }

            if (!empty($materialsForImport)) {
                $this->createImportReceipt($materialsForImport);
            }
        }

        try {
            Inventory_check_details::insert($inventoryCheckDetailData);
        } catch (\Exception $e) {
            toastr()->error('Lỗi khi lưu chi tiết phiếu kiểm kho: ' . $e->getMessage());
            return redirect()->back();
        }

        toastr()->success('Đã cập nhật phiếu kiểm kho thành công với mã ' . $inventoryCheck->code);
        return redirect()->route('check_warehouse.index');
    }


    public function showInventoryCheckEdits($code)
    {
        $inventoryCheckEdit = Inventory_check_details::where('inventory_check_code', $code)
            ->with('equipment')
            ->get();

        if ($inventoryCheckEdit->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy chi tiết cho phiếu kiểm kho này.'], 404);
        }

        return response()->json($inventoryCheckEdit);
    }


    public function store(Request $request)
    {
        $materialData = json_decode($request->input('materialData'), true);

        if (empty($materialData)) {
            toastr()->error('Đã lưu phiếu kiểm kho thất bại.');
            return redirect()->back();
        }

        $inventoryCheckData = [
            'equipment_code' => $materialData[0]['equipment_code'],
            'check_date' => $materialData[0]['check_date'],
            'note' => $materialData[0]['note'],
            'user_code' => $materialData[0]['created_by'],
            'status' => $materialData[0]['status']
        ];


        $inventoryCheckData['code'] = "KK" . $this->generateRandomString(8);
        $inventoryCheck = Inventory_checks::create($inventoryCheckData);

        if (!$inventoryCheck) {
            toastr()->error('Lỗi khi lưu phiếu kiểm kho.');
            return redirect()->back();
        }

        $inventoryCheckCode = $inventoryCheck->code;
        $inventoryCheckDetailData = [];

        $materialsForExport = [];
        $materialsForImport = [];

        foreach ($materialData as $material) {
            $inventoryCheckDetailData[] = [
                'inventory_check_code' => $inventoryCheckCode,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'current_quantity' => $material['current_quantity'],
                'actual_quantity' => $material['actual_quantity'],
                'unequal' => $material['unequal'],
                'equipment_note' => $material['equipment_note']
            ];

            $this->handleStockDiscrepancy($material, $materialsForExport, $materialsForImport);
        }

        if ($inventoryCheckData['status'] == 1) {
            if (!empty($materialsForExport)) {
                $this->createExportReceipt($materialsForExport);
            }

            if (!empty($materialsForImport)) {
                $this->createImportReceipt($materialsForImport);
            }
        }

        try {
            Inventory_check_details::insert($inventoryCheckDetailData);
        } catch (\Exception $e) {
            toastr()->error('Lỗi khi lưu chi tiết phiếu kiểm kho: ' . $e->getMessage());
            return redirect()->back();
        }
        if ($inventoryCheckData['status'] == 1) {
        }

        toastr()->success('Đã lưu phiếu kiểm kho thành công với mã ' . $inventoryCheckCode);
        return redirect()->route('check_warehouse.index');
    }

    private function handleStockDiscrepancy($material, &$materialsForExport, &$materialsForImport)
    {
        $difference = $material['actual_quantity'] - $material['current_quantity'];

        if ($difference < 0) {
            $materialsForExport[] = [
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'quantity' => abs($difference),
                'department_code' => $material['department_code'] ?? null
            ];
        } elseif ($difference > 0) {
            $materialsForImport[] = [
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'quantity' => $difference,
                'supplier_code' => $material['supplier_code'] ?? 'unknown',
                'price' => $material['price'] ?? 0
            ];
        }
    }

    private function createExportReceipt($materials)
    {
        $exportCode = 'PX-KK' . $this->generateRandomString(5);

        Exports::create([
            'code' => $exportCode,
            'note' => 'Xuất kho các vật tư thiếu',
            'status' => true,
            'created_by' => session('user_code'),
            'export_date' => now(),
            'department_code' => $materials[0]['department_code'] ?? null,
        ]);

        foreach ($materials as $material) {
            Export_details::create([
                'export_code' => $exportCode,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'quantity' => $material['quantity'],
            ]);

            $inventory = Inventories::where('equipment_code', $material['equipment_code'])
                ->where('batch_number', $material['batch_number'])
                ->first();

            if ($inventory) {
                $inventory->current_quantity -= $material['quantity'];
                $inventory->save();
            }
        }

        toastr()->info("Phiếu xuất kho {$exportCode} đã được tạo cho các vật tư thiếu.");
    }

    private function createImportReceipt($materials)
    {
        $receiptCode = 'PN-KK' . $this->generateRandomString(5);

        Receipts::create([
            'code' => $receiptCode,
            'supplier_code' => $materials[0]['supplier_code'],
            'note' => 'Nhập kho các vật tư dư',
            'status' => true,
            'receipt_no' => 'RN-' . $this->generateRandomString(5),
            'receipt_date' => now(),
            'created_by' => session('user_code'),
        ]);

        foreach ($materials as $material) {
            Receipt_details::create([
                'receipt_code' => $receiptCode,
                'batch_number' => $material['batch_number'],
                'quantity' => $material['quantity'],
                'equipment_code' => $material['equipment_code'],
                'VAT' => 0,
                'discount' => 0,
                'price' => $material['price'],
                'expiry_date' => now()->addYear(),
                'manufacture_date' => now()->subMonth(),
            ]);

            $inventory = Inventories::where('equipment_code', $material['equipment_code'])
                ->where('batch_number', $material['batch_number'])
                ->first();

            if ($inventory) {
                $inventory->current_quantity += $material['quantity'];
                $inventory->save();
            }
        }

        toastr()->info("Phiếu nhập kho {$receiptCode} đã được tạo.");
    }


    public function approveCheck($code)
    {
        $inventoryCheck = Inventory_checks::where('code', $code)->first();

        if ($inventoryCheck && $inventoryCheck->status == 0 && session('isAdmin') == true) {
            $inventoryCheck->status = 1;
            $inventoryCheck->check_date = now();
            $inventoryCheck->save();

            $inventoryCheckDetails = Inventory_check_details::where('inventory_check_code', $code)->get();

            $materialsForExport = [];
            $materialsForImport = [];

            foreach ($inventoryCheckDetails as $detail) {
                $material = [
                    'equipment_code' => $detail->equipment_code,
                    'batch_number' => $detail->batch_number,
                    'actual_quantity' => $detail->actual_quantity,
                    'current_quantity' => $detail->current_quantity,
                    'unequal' => $detail->unequal,
                    'department_code' => $detail->department_code ?? null,
                    'supplier_code' => $detail->supplier_code ?? 'unknown',
                    'price' => $detail->price ?? 0,
                ];

                $difference = $material['actual_quantity'] - $material['current_quantity'];

                if ($difference < 0) {
                    $materialsForExport[] = [
                        'equipment_code' => $material['equipment_code'],
                        'batch_number' => $material['batch_number'],
                        'quantity' => abs($difference),
                        'department_code' => $material['department_code']
                    ];
                } elseif ($difference > 0) {
                    $materialsForImport[] = [
                        'equipment_code' => $material['equipment_code'],
                        'batch_number' => $material['batch_number'],
                        'quantity' => $difference,
                        'supplier_code' => $material['supplier_code'],
                        'price' => $material['price'],
                    ];
                }
            }

            if (!empty($materialsForExport)) {
                $this->createExportReceipt($materialsForExport);
            }

            if (!empty($materialsForImport)) {
                $this->createImportReceipt($materialsForImport);
            }

            foreach ($inventoryCheckDetails as $detail) {
                $material = [
                    'equipment_code' => $detail->equipment_code,
                    'batch_number' => $detail->batch_number,
                    'actual_quantity' => $detail->actual_quantity,
                    'unequal' => $detail->unequal
                ];

                $this->updateInventoryByCheck($material);
            }

            toastr()->success('Đã duyệt phiếu kiểm kho thành công với mã ' . $inventoryCheck->code);
            return redirect()->back();
        } else {
            toastr()->error('Bạn không có quyền để sử dụng chức năng này!');
            return redirect()->back();
        }

        toastr()->success('Phiếu kiểm kho đã được duyệt trước đó.');
        return redirect()->back();
    }

    private function updateInventoryByCheck($material)
    {
        $inventory = Inventories::where('equipment_code', $material['equipment_code'])
            ->where('batch_number', $material['batch_number'])
            ->first();

        if ($inventory) {
            $inventory->current_quantity = $material['actual_quantity'];
            $inventory->save();
        } else {
            $newInventoryCode = 'TK' . $this->generateRandomString();

            Inventories::create([
                'code' => $newInventoryCode,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'current_quantity' => $material['actual_quantity'],
                'import_date' => now(),
                'expiry_date' => $material['expiry_date'],
            ]);
        }
    }

    public function search(Request $request)
    {
        $title = 'Kiểm Kho';

        $query = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userCode = $request->input('user_code');
        $status = $request->input('status');

        $inventoryChecks = Inventory_checks::with(['user'])
            ->where(function ($q) use ($query) {
                $q->where('code', 'LIKE', "%{$query}%")
                    ->orWhere('note', 'LIKE', "%{$query}%");
            })
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('check_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('check_date', '<=', $endDate);
            })
            ->when(!is_null($status), function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($userCode, function ($q) use ($userCode) {
                return $q->where('user_code', $userCode);
            })
            ->get();


        return view("{$this->route}.search", [
            'title' => $title,
            'inventoryChecks' => $inventoryChecks,
        ]);
    }

    function generateRandomString($length = 9)
    {
        $characters = '0123456789';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function deleteCheck($code)
    {
        $check = Inventory_checks::where('code', $code)->first();

        if (!$check) {
            toastr()->error('Phiếu kiểm kho không tồn tại.');
            return redirect()->back();
        }

        if ($check->user_code != session('user_code') && session('isAdmin') != true) {
            toastr()->error('Bạn không có quyền xóa phiếu này. Chỉ có người tạo phiếu hoặc admin mới có quyền xóa.');
            return redirect()->back();
        }

        if ($check->status != 0 && $check->status != 3) {
            return redirect()->back()->with('error', 'Chỉ có thể xóa phiếu tạm hoặc phiếu đã hủy.');
        }

        Inventory_check_details::where('inventory_check_code', $check->code)->delete();
        $check->delete();

        toastr()->success('Phiếu kiểm kho đã được xóa thành công.');
        return redirect()->route('check_warehouse.index');
    }


    public function createNotificationAfterUpdateInventory($inventoryCheckCode, $userCode)
    {
        $notificationContent = "Kho đã được cân bằng thành công với mã phiếu kiểm kho: {$inventoryCheckCode}";

        $payload = [
            'code' => 'TB' . $this->generateRandomString(8),
            'user_code' => $userCode,
            'content' => $notificationContent,
            'created_at' => now(),
            'updated_at' => null,
            'important' => 0,
            'status' => 2
        ];

        Notifications::create($payload);
    }
}
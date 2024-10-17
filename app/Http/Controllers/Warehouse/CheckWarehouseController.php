<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
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

        $equipmentsWithStock = Equipments::whereHas('inventories', function ($query) {
            $query->where('current_quantity', '>', 0);
        })->with(['inventories' => function ($query) {
            $query->select('equipment_code', 'current_quantity', 'batch_number')
                ->where('current_quantity', '>', 0);
        }])->get();

        return view("{$this->route}.form", compact('title', 'action', 'equipmentsWithStock'));
    }

    public function edit($code)
    {
        $title = 'Chỉnh sửa';

        $action = 'edit';

        $inventoryCheck = Inventory_checks::findOrFail($code);

        $equipmentsWithStock = Equipments::whereHas('inventories', function ($query) {
            $query->where('current_quantity', '>', 0);
        })->with(['inventories' => function ($query) {
            $query->select('equipment_code', 'current_quantity', 'batch_number')
                ->where('current_quantity', '>', 0);
        }])->get();

        $equipmentsWithJson = $this->showInventoryCheckEdits($code);

        return view("{$this->route}.form", compact('title', 'action', 'equipmentsWithJson', 'inventoryCheck', 'equipmentsWithStock'));
    }

    public function update(Request $request, $code)
    {
        // Tìm phiếu kiểm kho theo mã
        $inventoryCheck = Inventory_checks::where('code', $code)->firstOrFail();

        // Lấy dữ liệu vật tư từ request
        $materialData = json_decode($request->input('materialData'), true);

        if (empty($materialData)) {
            toastr()->error('Không có dữ liệu để cập nhật.');
            return redirect()->back();
        }

        // Cập nhật thông tin phiếu kiểm kho
        $inventoryCheckData = [
            'check_date' => $materialData[0]['check_date'],
            'note' => $materialData[0]['note'],
            'user_code' => $materialData[0]['created_by'],
            'status' => $materialData[0]['status']
        ];


        $inventoryCheck->update($inventoryCheckData);

        // Xóa chi tiết cũ
        Inventory_check_details::where('inventory_check_code', $code)->delete();

        // Mảng để lưu các vật tư bị thiếu hoặc dư
        $materialsForExport = [];
        $materialsForImport = [];
        $inventoryCheckDetailData = [];

        foreach ($materialData as $material) {
            // Thêm chi tiết phiếu kiểm kho
            $inventoryCheckDetailData[] = [
                'inventory_check_code' => $code,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'current_quantity' => $material['current_quantity'],
                'actual_quantity' => $material['actual_quantity'],
                'unequal' => $material['unequal'],
                'equipment_note' => $material['equipment_note']
            ];

            // Kiểm tra lệch và gom dữ liệu vào các mảng tương ứng
            $this->handleStockDiscrepancy($material, $materialsForExport, $materialsForImport);
        }

        if ($inventoryCheckData['status'] == 1) {
            // Tạo phiếu xuất cho các vật tư thiếu (nếu có)
            if (!empty($materialsForExport)) {
                $this->createExportReceipt($materialsForExport);
            }

            // Tạo phiếu nhập cho các vật tư dư (nếu có)
            if (!empty($materialsForImport)) {
                $this->createImportReceipt($materialsForImport);
            }
        }

        // Cập nhật chi tiết phiếu kiểm kho mới
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

        // Tạo dữ liệu phiếu kiểm kho
        $inventoryCheckData = [
            'equipment_code' => $materialData[0]['equipment_code'],
            'check_date' => $materialData[0]['check_date'],
            'note' => $materialData[0]['note'],
            'user_code' => $materialData[0]['created_by'],
            'status' => $materialData[0]['status']
        ];

        // dd($inventoryCheckData['status']);

        $inventoryCheckData['code'] = "KK" . $this->generateRandomString();
        $inventoryCheck = Inventory_checks::create($inventoryCheckData);

        if (!$inventoryCheck) {
            toastr()->error('Lỗi khi lưu phiếu kiểm kho.');
            return redirect()->back();
        }

        $inventoryCheckCode = $inventoryCheck->code;
        $inventoryCheckDetailData = [];

        // Mảng để lưu các vật tư bị thiếu hoặc dư
        $materialsForExport = [];
        $materialsForImport = [];

        foreach ($materialData as $material) {
            // Thêm chi tiết phiếu kiểm kho
            $inventoryCheckDetailData[] = [
                'inventory_check_code' => $inventoryCheckCode,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'current_quantity' => $material['current_quantity'],
                'actual_quantity' => $material['actual_quantity'],
                'unequal' => $material['unequal'],
                'equipment_note' => $material['equipment_note']
            ];

            // Kiểm tra lệch và gom dữ liệu vào các mảng tương ứng
            $this->handleStockDiscrepancy($material, $materialsForExport, $materialsForImport);
        }

        // Tạo phiếu xuất cho các vật tư thiếu (nếu có)
        if ($inventoryCheckData['status'] == 1) {
            // Tạo phiếu xuất cho các vật tư thiếu (nếu có)
            if (!empty($materialsForExport)) {
                $this->createExportReceipt($materialsForExport);
            }

            // Tạo phiếu nhập cho các vật tư dư (nếu có)
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

        toastr()->success('Đã lưu phiếu kiểm kho thành công với mã ' . $inventoryCheckCode);
        return redirect()->route('check_warehouse.index');
    }

    private function handleStockDiscrepancy($material, &$materialsForExport, &$materialsForImport)
    {
        $difference = $material['actual_quantity'] - $material['current_quantity'];

        if ($difference < 0) {
            // Nếu thiếu, thêm vào mảng materialsForExport
            $materialsForExport[] = [
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'quantity' => abs($difference),
                'department_code' => $material['department_code'] ?? null
            ];
        } elseif ($difference > 0) {
            // Nếu dư, thêm vào mảng materialsForImport
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

        // Tạo phiếu xuất kho
        Exports::create([
            'code' => $exportCode,
            'note' => 'Xuất kho các vật tư thiếu',
            'status' => true,
            'created_by' => session('user_code'),
            'export_date' => now(),
            'department_code' => $materials[0]['department_code'] ?? null,
        ]);

        foreach ($materials as $material) {
            // Thêm chi tiết xuất kho
            Export_details::create([
                'export_code' => $exportCode,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'quantity' => $material['quantity'],
            ]);

            // Cập nhật tồn kho
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

        // Tạo phiếu nhập kho
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
            // Thêm chi tiết nhập kho
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

            // Cập nhật tồn kho
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

        if ($inventoryCheck && $inventoryCheck->status == 0) {
            $inventoryCheck->status = 1;
            $inventoryCheck->check_date = now();
            $inventoryCheck->save();

            $inventoryCheckDetails = Inventory_check_details::where('inventory_check_code', $code)->get();

            // Arrays to store materials for export and import
            $materialsForExport = [];
            $materialsForImport = [];

            foreach ($inventoryCheckDetails as $detail) {
                $material = [
                    'equipment_code' => $detail->equipment_code,
                    'batch_number' => $detail->batch_number,
                    'actual_quantity' => $detail->actual_quantity,
                    'current_quantity' => $detail->current_quantity, // Assuming this is available
                    'unequal' => $detail->unequal,
                    'department_code' => $detail->department_code ?? null,
                    'supplier_code' => $detail->supplier_code ?? 'unknown',
                    'price' => $detail->price ?? 0,
                ];

                // Calculate the discrepancy
                $difference = $material['actual_quantity'] - $material['current_quantity'];

                if ($difference < 0) {
                    // If there is a shortage, add to materials for export
                    $materialsForExport[] = [
                        'equipment_code' => $material['equipment_code'],
                        'batch_number' => $material['batch_number'],
                        'quantity' => abs($difference),
                        'department_code' => $material['department_code']
                    ];
                } elseif ($difference > 0) {
                    // If there is excess, add to materials for import
                    $materialsForImport[] = [
                        'equipment_code' => $material['equipment_code'],
                        'batch_number' => $material['batch_number'],
                        'quantity' => $difference,
                        'supplier_code' => $material['supplier_code'],
                        'price' => $material['price'],
                    ];
                }
            }

            // Create export receipt for any missing materials
            if (!empty($materialsForExport)) {
                $this->createExportReceipt($materialsForExport);
            }

            // Create import receipt for any excess materials
            if (!empty($materialsForImport)) {
                $this->createImportReceipt($materialsForImport);
            }

            // Update inventory based on the approved check
            foreach ($inventoryCheckDetails as $detail) {
                $material = [
                    'equipment_code' => $detail->equipment_code,
                    'batch_number' => $detail->batch_number,
                    'actual_quantity' => $detail->actual_quantity,
                    'unequal' => $detail->unequal
                ];

                $this->updateInventoryByCheck($material);
            }

            $this->createNotificationAfterUpdateInventory($inventoryCheck->code, $inventoryCheck->user_code);

            toastr()->success('Đã duyệt phiếu kiểm kho thành công với mã ' . $inventoryCheck->code);
            return redirect()->back();
        }

        toastr()->success('Phiếu kiểm kho đã được duyệt trước đó.');
        return redirect()->back();
    }



    public function cancelCheck($code)
    {
        if (!session('isAdmin')) {
            toastr()->error('Bạn không có quyền hủy phiếu kiểm kho.');
            return redirect()->back();
        }

        $inventoryCheck = Inventory_checks::where('code', $code)->first();
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        if ($inventoryCheck && $inventoryCheck->status == 1) {
            $checkDate = Carbon::parse($inventoryCheck->check_date)->setTimezone('Asia/Ho_Chi_Minh');
            $daysPassed = $checkDate->diffInDays($now);

            if ($daysPassed > 1) {
                toastr()->error('Không thể hủy phiếu kiểm kho vì đã quá thời gian cho phép (1 ngày).');
                return redirect()->back();
            }

            $this->deleteRelatedReceiptsAndExports($code);

            $inventoryCheckDetails = Inventory_check_details::where('inventory_check_code', $code)->get();

            foreach ($inventoryCheckDetails as $detail) {
                $inventory = Inventories::where('equipment_code', $detail->equipment_code)
                    ->where('batch_number', $detail->batch_number)
                    ->first();

                if ($inventory) {
                    $inventory->current_quantity += $detail->actual_quantity;
                    $inventory->save();
                }
            }

            $inventoryCheck->status = 3;
            $inventoryCheck->save();

            toastr()->success('Phiếu kiểm kho đã được hủy và số lượng tồn kho đã được phục hồi.');
            return redirect()->back();
        }

        toastr()->error('Không thể hủy phiếu kiểm kho. Chỉ có thể hủy phiếu đã được duyệt.');
        return redirect()->back();
    }

    private function deleteRelatedReceiptsAndExports($inventoryCheckCode)
    {
        $receipts = Receipts::where('code', 'like', 'PN-KK%')->get();

        foreach ($receipts as $receipt) {
            Receipt_details::where('receipt_code', $receipt->code)->delete();
            $receipt->delete();
        }

        $exports = Exports::where('code', 'like', 'PX-KK%')->get();

        foreach ($exports as $export) {
            Export_details::where('export_code', $export->code)->delete();
            $export->delete();
        }
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
            return redirect()->back()->with('error', 'Phiếu kiểm kho không tồn tại.');
        }

        if ($check->status != 0 && $check->status != 3) {
            return redirect()->back()->with('error', 'Chỉ có thể xóa phiếu tạm hoặc phiếu đã hủy.');
        }

        Inventory_check_details::where('inventory_check_code', $check->code)->delete();

        $check->delete();

        return redirect()->route('check_warehouse.index')->with('success', 'Phiếu kiểm kho đã được xóa thành công.');
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
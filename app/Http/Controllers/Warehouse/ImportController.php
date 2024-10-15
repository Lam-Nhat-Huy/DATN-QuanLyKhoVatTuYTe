<?php

namespace App\Http\Controllers\Warehouse;

use App\Exports\ReceiptsExport;
use App\Http\Controllers\Controller;
use App\Imports\ReceiptsImport;
use App\Models\Equipments;
use App\Models\Import_equipment_request_details;
use App\Models\Import_equipment_requests;
use App\Models\Inventories;
use App\Models\Receipt_details;
use App\Models\Receipts;
use App\Models\Suppliers;
use App\Models\Users;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    protected $route = 'warehouse';

    public function import(Request $request)
    {
        $title = 'Nhập Kho';

        $allReceiptCount = Receipts::all()->count();

        $draftReceiptsCount = Receipts::where('status', 0)->count();

        $approvedReceiptsCount = Receipts::where('status', 1)->count();

        $tempReceiptsCount = Receipts::where('status', 3)->count();

        $suppliers = Suppliers::all();

        $users = Users::all();

        $kw = $request->input('kw');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $supplierCode = $request->input('spl');
        $status = $request->input('stt');
        $createdBy = $request->input('us');

        $receipts = Receipts::with(['supplier', 'user', 'details.equipments'])
            ->orderBy('created_at', 'desc')
            ->whereNull('deleted_at')
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('receipt_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('receipt_date', '<=', $endDate);
            });

        if (isset($kw)) {
            $receipts = $receipts->where(function ($q) use ($kw) {
                $q->where('code', 'LIKE', '%' . $kw . '%')
                    ->orWhere('receipt_no', 'LIKE', "%{$kw}%");
            });
        }

        if (isset($supplierCode)) {
            $receipts = $receipts->where('supplier_code', $supplierCode);
        }

        if (isset($status)) {
            $receipts = $receipts->where('status', $status);
        }

        if (isset($createdBy)) {
            $receipts = $receipts->where('created_by', $createdBy);
        }

        $receipts = $receipts->paginate(10);

        if (!empty($request->import_codes)) {

            if ($request->action_type === 'browse') {

                $getReceipt = Receipts::whereIn('code', $request->import_codes)->where('status', 0);

                $getReceipt->update(['status' => 1]);

                $receiptDetails = Receipt_details::whereIn('receipt_code', $request->import_codes)->get();

                foreach ($receiptDetails as $item) {
                    // Tìm bản ghi inventory theo batch_number và equipment_code từ $item
                    $countQuantityInventoryWhere = Inventories::where('batch_number', $item->batch_number)
                        ->where('equipment_code', $item->equipment_code)
                        ->first();

                    // Nếu tìm thấy trong Inventories thì cộng số lượng
                    $current_quantity = $countQuantityInventoryWhere ? $countQuantityInventoryWhere->current_quantity + $item->quantity : $item->quantity;

                    // Cập nhật hoặc tạo mới Inventory
                    Inventories::updateOrCreate(
                        [
                            'batch_number' => $item->batch_number,
                            'equipment_code' => $item->equipment_code
                        ],
                        [
                            'code' => $countQuantityInventoryWhere ? $countQuantityInventoryWhere->code : 'TK' . $this->generateRandomString(8),
                            'batch_number' => $item->batch_number,
                            'current_quantity' => $current_quantity,
                            'import_code' => $item->receipt_code,
                            'expiry_date' => !empty($item->expiry_date) ? $item->expiry_date : null,
                            'manufacture_date' => $item->manufacture_date,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    // Cập nhật giá cho thiết bị
                    Equipments::where('code', $item->equipment_code)->update([
                        'price' => $item->price,
                    ]);
                }

                toastr()->success('Duyệt phiếu chờ thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                Receipts::whereIn('code', $request->import_codes)->where('status', 0)->orWhere('status', 3)->delete();

                toastr()->success('Hủy thành công');

                return redirect()->back();
            }
        }

        return view("{$this->route}.import_warehouse.import", [
            'title' => $title,
            'receipts' => $receipts,
            'suppliers' => $suppliers,
            'users' => $users,
            'draftReceiptsCount' => $draftReceiptsCount,
            'approvedReceiptsCount' => $approvedReceiptsCount,
            'allReceiptCount' => $allReceiptCount,
            'tempReceiptsCount' => $tempReceiptsCount
        ]);
    }

    public function importTrash(Request $request)
    {
        $title = 'Nhập Kho';

        $allReceiptCount = Receipts::onlyTrashed()->count();

        $draftReceiptsCount = Receipts::where('status', 0)->onlyTrashed()->count();

        $approvedReceiptsCount = Receipts::where('status', 1)->onlyTrashed()->count();

        $tempReceiptsCount = Receipts::where('status', 3)->onlyTrashed()->count();

        $suppliers = Suppliers::all();

        $users = Users::all();

        $receiptTrash = Receipts::with(['supplier', 'user', 'details.equipments'])
            ->orderBy('deleted_at', 'desc')
            ->onlyTrashed()
            ->paginate(10);

        if (!empty($request->import_codes)) {

            if ($request->action_type === 'restore') {

                Receipts::whereIn('code', $request->import_codes)->onlyTrashed()->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                Receipts::whereIn('code', $request->import_codes)->onlyTrashed()->forceDelete();

                toastr()->success('Xóa vĩnh viễn thành công');

                return redirect()->back();
            }
        }

        if (!empty($request->restore_value)) {

            Receipts::where('code', $request->restore_value)->onlyTrashed()->restore();

            toastr()->success('Khôi phục thành công');

            return redirect()->back();
        }

        if (!empty($request->delete_value)) {

            Receipts::where('code', $request->delete_value)->onlyTrashed()->forceDelete();

            toastr()->success('Xóa vĩnh viễn thành công');

            return redirect()->back();
        }

        return view("{$this->route}.import_warehouse.trash", [
            'title' => $title,
            'receiptTrash' => $receiptTrash,
            'suppliers' => $suppliers,
            'users' => $users,
            'draftReceiptsCount' => $draftReceiptsCount,
            'approvedReceiptsCount' => $approvedReceiptsCount,
            'allReceiptCount' => $allReceiptCount,
            'tempReceiptsCount' => $tempReceiptsCount
        ]);
    }

    public function create_import(Request $request)
    {
        $title = 'Tạo Phiếu Nhập Kho';

        $action = 'create';

        $suppliers = Suppliers::all();

        $users = Users::all();

        $getListIERD = '';

        $infoIER = '';

        $equipmentsWithStock = Equipments::all();

        if (
            !empty($request->equipment) &&
            !empty($request->price) &&
            !empty($request->product_date) &&
            !empty($request->batch_number) &&
            !empty($request->quantity) &&
            !empty($request->discount_rate) &&
            !empty($request->VAT)
        ) {
            $equipment = Equipments::where('code', $request->equipment)->first();

            if ($equipment) {
                return response()->json([
                    'success' => true,
                    'equipment_code' => $equipment->code,
                    'equipment_name' => $equipment->name,
                    'price' => $request->price,
                    'product_date' => $request->product_date,
                    'expiry_date' => $request->expiry_date,
                    'batch_number' => $request->batch_number,
                    'quantity' => $request->quantity,
                    'discount_rate' => $request->discount_rate,
                    'vat' => $request->VAT,
                ]);
            }
        }

        if (isset($request->cd)) {
            $getListIERD = Import_equipment_request_details::where('import_request_code', $request->cd)->get();

            $infoIER = Import_equipment_requests::with(['suppliers', 'users', 'import_equipment_request_details'])
                ->where('code', $request->cd)
                ->whereNull('deleted_at')
                ->first();
        }

        return view("{$this->route}.import_warehouse.create_import", [
            'title' => $title,
            'suppliers' => $suppliers,
            'users' => $users,
            'equipmentsWithStock' => $equipmentsWithStock,
            'action' => $action,
            'getListIERD' => $getListIERD,
            'infoIER' => $infoIER,
        ]);
    }

    public function store_import(Request $request)
    {
        if (
            !empty($request->supplier_code) &&
            !empty($request->receipt_no) &&
            !empty($request->importEquipmentStatus) &&
            !empty($request->equipment_list)
        ) {
            $supplierCode = $request->supplier_code;
            $receiptNo = $request->receipt_no;
            $note = $request->note;
            $equipmentList = json_decode($request->equipment_list, true);

            $record = Receipts::create([
                'code' => 'PN' . $this->generateRandomString(8),
                'supplier_code' => $supplierCode,
                'note' => $note ?? '',
                'status' => $request->importEquipmentStatus == 4 ? 0 : $request->importEquipmentStatus,
                'receipt_no' => $receiptNo,
                'receipt_date' => now(),
                'created_by' => session('user_code'),
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ]);

            if ($record) {
                foreach ($equipmentList as $equipment) {
                    Receipt_details::create([
                        'receipt_code' => $record->code,
                        'batch_number' => $equipment['batch_number'],
                        'expiry_date' => !empty($equipment['expiry_date']) ? $equipment['expiry_date'] : NULL,
                        'manufacture_date' => $equipment['product_date'],
                        'quantity' => $equipment['quantity'],
                        'VAT' => $equipment['vat'],
                        'discount' => $equipment['discount_rate'],
                        'price' => $equipment['price'],
                        'equipment_code' => $equipment['equipment_code'],
                        'created_at' => now(),
                        'updated_at' => null,
                        'deleted_at' => null,
                    ]);
                }

                return response()->json(['success' => true, 'message' => 'Đã tạo phiếu nhập']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    // Tạo phiếu nhập bằng yêu cầu mua hàng
    public function import_equipment_request(Request $request)
    {
        if (
            !empty($request->supplier_code) &&
            !empty($request->receipt_no) &&
            !empty($request->importEquipmentStatus) &&
            !empty($request->equipment_list)
        ) {
            $supplierCode = $request->supplier_code;
            $receiptNo = $request->receipt_no;
            $note = $request->note;
            $equipmentList = json_decode($request->equipment_list, true);

            // Tạo phiếu nhập
            $record = Receipts::create([
                'code' => 'PN' . $this->generateRandomString(8),
                'supplier_code' => $supplierCode,
                'note' => $note ?? '',
                'status' => 1,
                'receipt_no' => $receiptNo,
                'receipt_date' => now(),
                'created_by' => session('user_code'),
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ]);

            Import_equipment_requests::where('code', $receiptNo)->update([
                'status' => 4,
            ]);

            Import_equipment_request_details::where('import_request_code', $receiptNo)->update([
                'status' => 4,
            ]);

            // Tạo chi tiết phiếu nhập
            if ($record) {
                foreach ($equipmentList as $equipment) {
                    $record_detail = Receipt_details::create([
                        'receipt_code' => $record->code,
                        'batch_number' => $equipment['batch_number'],
                        'expiry_date' => !empty($equipment['expiry_date']) ? $equipment['expiry_date'] : NULL,
                        'manufacture_date' => $equipment['product_date'],
                        'quantity' => $equipment['quantity'],
                        'VAT' => $equipment['vat'],
                        'discount' => $equipment['discount_rate'],
                        'price' => $equipment['price'],
                        'equipment_code' => $equipment['equipment_code'],
                        'created_at' => now(),
                        'updated_at' => null,
                        'deleted_at' => null,
                    ]);

                    Equipments::where('code', $record_detail->equipment_code)->update([
                        'price' => $record_detail->price,
                    ]);
                }

                // Insert inventories
                $receiptDetails = Receipt_details::where('receipt_code', $record->code)->get();

                foreach ($receiptDetails as $item) {
                    // Tìm bản ghi inventory theo batch_number và equipment_code từ $item
                    $countQuantityInventoryWhere = Inventories::where('batch_number', $item->batch_number)
                        ->where('equipment_code', $item->equipment_code)
                        ->first();

                    // Nếu tìm thấy trong Inventories thì cộng số lượng
                    $current_quantity = $countQuantityInventoryWhere ? $countQuantityInventoryWhere->current_quantity + $item->quantity : $item->quantity;

                    // Cập nhật hoặc tạo mới Inventory
                    $inventoryCode = $countQuantityInventoryWhere ? $countQuantityInventoryWhere->code : 'TK' . $this->generateRandomString(8);

                    Inventories::updateOrCreate(
                        [
                            'batch_number' => $item['batch_number'],
                            'equipment_code' => $item['equipment_code']
                        ],
                        [
                            'code' => $inventoryCode,
                            'batch_number' => $item['batch_number'],
                            'current_quantity' => $current_quantity,
                            'import_code' => $record->code,
                            'expiry_date' => !empty($item['expiry_date']) ? $item['expiry_date'] : null,
                            'manufacture_date' => $item['manufacture_date'],
                            'created_at' => $record->receipt_date,
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã tạo phiếu nhập']);
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    public function edit_import($code)
    {
        $title = 'Tạo Phiếu Nhập Kho';

        $action = 'update';

        $AllSuppiler = Suppliers::orderBy('created_at', 'DESC')->get();

        $AllEquipment = Equipments::orderBy('created_at', 'DESC')->get();

        $equipmentDetail = Receipt_details::where('receipt_code', $code);

        $getList = $equipmentDetail->get();

        $checkList = $equipmentDetail->pluck('equipment_code')->toArray();

        $editForm = Receipts::with(['supplier', 'user', 'details.equipments'])
            ->where('code', $code)
            ->whereNull('deleted_at')
            ->first();

        return view("{$this->route}.import_warehouse.create_import", [
            'title' => $title,
            'suppliers' => $AllSuppiler,
            'equipmentsWithStock' => $AllEquipment,
            'getList' => $getList,
            'checkList' => $checkList,
            'editForm' => $editForm,
            'action' => $action
        ]);
    }

    public function update_import(Request $request, $code)
    {
        if (
            !empty($request->supplier_code) &&
            !empty($request->receipt_no) &&
            !empty($request->importEquipmentStatus) &&
            !empty($request->equipment_list)
        ) {
            $supplierCode = $request->supplier_code;
            $receiptNo = $request->receipt_no;
            $note = $request->note;
            $equipmentList = json_decode($request->equipment_list, true);

            // Tìm các bản ghi không có mã trong $equipmentList và thuộc về receipt_code
            $equipmentToDelete = Receipt_details::whereNotIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where('receipt_code', $code)
                ->get();

            // Xóa các bản ghi tìm thấy
            if ($equipmentToDelete->isNotEmpty()) {
                $equipmentToDelete->each(function ($item) {
                    $item->forceDelete();
                });
            }

            $existingRequest = Receipts::where('code', $code);

            $record = $existingRequest->first();

            $existingRequest->update([
                'supplier_code' => $supplierCode,
                'note' => $note ?? $record->note,
                'receipt_no' => $receiptNo,
                'updated_at' => now(),
            ]);

            foreach ($equipmentList as $equipment) {
                Receipt_details::updateOrCreate(
                    [
                        'receipt_code' => $code,
                        'equipment_code' => $equipment['equipment_code']
                    ],
                    [
                        'quantity' => $equipment['quantity'],
                        'batch_number' => $equipment['batch_number'],
                        'expiry_date' => !empty($equipment['expiry_date']) ? $equipment['expiry_date'] : NULL,
                        'manufacture_date' => $equipment['product_date'],
                        'quantity' => $equipment['quantity'],
                        'VAT' => $equipment['vat'],
                        'discount' => $equipment['discount_rate'],
                        'price' => $equipment['price'],
                        'created_at' => $record->receipt_date,
                        'updated_at' => null,
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Cập nhật phiếu nhập thành công']);
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    public function checkBatchNumber(Request $request)
    {
        // Kiểm tra nếu có bản ghi có cùng batch_number nhưng khác equipment_code
        $existingInventory = Receipt_details::where('batch_number', $request->batch_number)
            ->where('equipment_code', '!=', $request->equipment_code) // Khác mã thiết bị
            ->first();

        // Nếu có bản ghi với cùng số lô nhưng mã thiết bị khác, trả về false (không cho thêm)
        if ($existingInventory) {
            return response()->json(['success' => false, 'message' => 'Số lô đã tồn tại với thiết bị khác']);
        }

        // Nếu không có bản ghi nào trùng cả số lô và khác mã thiết bị, cho phép thêm
        return response()->json(['success' => true, 'message' => 'Có thể thêm']);
    }


    public function checkReceiptNo(Request $request)
    {
        $existingRN = Receipts::where('receipt_no', $request->receipt_no)
            ->where('code', '!=', $request->code)
            ->first();

        if ($existingRN) {
            return response()->json([
                'success' => true,
                'message' => 'Số hóa đơn này đã tồn tại trên hệ thống'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Được phép tạo'
        ]);
    }

    public function approve(Request $request)
    {
        if (!empty($request->browse_code)) {
            $existingRequest = Receipts::find($request->browse_code);

            $record = $existingRequest->first();

            $existingRequest->update([
                'status' => 1,
            ]);

            $receiptDetails = Receipt_details::where('receipt_code', $request->browse_code)->get();

            foreach ($receiptDetails as $item) {
                // Tìm bản ghi inventory theo batch_number và equipment_code từ $item
                $countQuantityInventoryWhere = Inventories::where('batch_number', $item->batch_number)
                    ->where('equipment_code', $item->equipment_code)
                    ->first();

                // Nếu tìm thấy trong Inventories thì cộng số lượng
                $current_quantity = $countQuantityInventoryWhere ? $countQuantityInventoryWhere->current_quantity + $item->quantity : $item->quantity;

                // Cập nhật hoặc tạo mới Inventory
                Inventories::updateOrCreate(
                    [
                        'batch_number' => $item['batch_number'],
                        'equipment_code' => $item['equipment_code']
                    ],
                    [
                        'code' => $countQuantityInventoryWhere ? $countQuantityInventoryWhere->code : 'TK' . $this->generateRandomString(8),
                        'batch_number' => $item['batch_number'],
                        'current_quantity' => $current_quantity,
                        'import_code' => $request->browse_code,
                        'expiry_date' => !empty($item['expiry_date']) ? $item['expiry_date'] : null,
                        'manufacture_date' => $item['manufacture_date'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                Equipments::where('code', $item['equipment_code'])->update([
                    'price' => $item['price'],
                ]);
            }

            toastr()->success("Phiếu #$request->browse_code đã được duyệt");

            return redirect()->back();
        } else if (!empty($request->create_code)) {
            Receipts::find($request->create_code)->update([
                'status' => 0
            ]);

            toastr()->success("Phiếu tạm #$request->create_code đã được tạo và đang ở trạng thái chờ duyệt");

            return redirect()->back();
        }

        toastr()->success('Phiếu đã được duyệt trước đó.');
        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $receipt = Receipts::where('code', $request->delete_code)->first();

        if (!$receipt) {
            toastr()->error('Không tìm thấy phiếu nhập kho.');
            return redirect()->back();
        }

        if ($receipt->status == 1) {
            toastr()->error('Không thể hủy phiếu đã được duyệt.');
            return redirect()->back();
        }

        $receipt->delete();

        toastr()->success('Đã hủy phiếu nhập kho.');
        return redirect()->back();
    }

    public function exportExcel()
    {
        return Excel::download(new ReceiptsExport, 'receipts_sample.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:10240', // tối đa 10MB
        ]);

        Excel::import(new ReceiptsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Dữ liệu đã được nhập thành công!');
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
}

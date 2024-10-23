<?php

namespace App\Http\Controllers\EquipmentRequest;

use App\Exports\EquipmentRequest;
use App\Http\Controllers\Controller;
use App\Imports\equipmentRequest as ImportsEquipmentRequest;
use App\Models\Departments;
use App\Models\Equipments;
use App\Models\Export_equipment_request_details;
use App\Models\Export_equipment_requests;
use App\Models\Exports;
use App\Models\Import_equipment_request_details;
use App\Models\Import_equipment_requests;
use App\Models\Receipts;
use App\Models\Suppliers;
use App\Models\Users;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentRequestController extends Controller
{
    protected $route = 'equipment_request';

    protected $callModel;

    public function __construct()
    {
        $this->callModel = new Import_equipment_requests();
    }

    // Nhập

    public function import_equipment_request(Request $request)
    {
        $title = 'Yêu Cầu Mua Hàng';

        $AllSupplier = Suppliers::orderBy('created_at', 'DESC')->get();

        $AllUser = Users::orderBy('created_at', 'DESC')->get();

        $AllEquipmentRequest = $this->callModel::with(['suppliers', 'users', 'import_equipment_request_details'])
            ->orderBy('request_date', 'DESC')
            ->whereNull('deleted_at');

        if (isset($request->spr)) {
            $AllEquipmentRequest = $AllEquipmentRequest->where("supplier_code", $request->spr);
        }

        if (isset($request->us)) {
            $AllEquipmentRequest = $AllEquipmentRequest->where("user_code", $request->us);
        }

        if (isset($request->stt)) {
            if ($request->stt == 2) {

                $AllEquipmentRequest = $AllEquipmentRequest
                    ->where(function ($query) {
                        $query->where('status', 0)
                            ->orWhere('status', 3);
                    })
                    ->where("request_date", '<', now()->subDays(3));
            } elseif ($request->stt == 3) {

                $AllEquipmentRequest = $AllEquipmentRequest->where("status", 3)
                    ->where("request_date", '>', now()->subDays(3));
            } elseif ($request->stt == 0) {

                $AllEquipmentRequest = $AllEquipmentRequest->where("status", 0)
                    ->where("request_date", '>', now()->subDays(3));
            } elseif ($request->stt == 4) {

                $AllEquipmentRequest = $AllEquipmentRequest->where("status", 4);
            } elseif ($request->stt == 5) {

                $AllEquipmentRequest = $AllEquipmentRequest->where("status", 5);
            } else {

                $AllEquipmentRequest = $AllEquipmentRequest->where("status", 1);
            }
        }

        if (isset($request->kw)) {

            $AllEquipmentRequest = $AllEquipmentRequest->where(function ($query) use ($request) {
                $query->where('code', 'like', '%' . $request->kw . '%');
            });
        }

        $AllEquipmentRequest = $AllEquipmentRequest->paginate(10);

        if (!empty($request->save_status)) {
            $record = $this->callModel::where('code', $request->save_status)
                ->update([
                    'status' => 0,
                ]);

            toastr()->success('Phiếu tạm đã được tạo và đang ở trạng thái chờ duyệt');

            return redirect()->back();
        }

        if (!empty($request->delete_request)) {
            $this->callModel::where('code', $request->delete_request)
                ->delete();

            toastr()->success('Đã hủy yêu cầu mua hàng');

            return redirect()->back();
        }

        if (!empty($request->browse_request)) {
            $this->callModel::where('code', $request->browse_request)
                ->where('status', 0)
                ->update([
                    'status' => 2,
                ]);

            Import_equipment_request_details::where('import_request_code', $request->browse_request)
                ->update(['status' => 2]);

            toastr()->success('Đã duyệt phiếu yêu cầu mua hàng');

            return redirect()->back();
        }

        if (!empty($request->quote_received)) {
            $this->callModel::where('code', $request->quote_received)
                ->where('status', 2)
                ->update([
                    'status' => 5,
                ]);

            Import_equipment_request_details::where('import_request_code', $request->quote_received)
                ->update(['status' => 5]);

            toastr()->success('Đã nhận báo giá');

            return redirect()->back();
        }

        if (!empty($request->price_entered)) {
            $this->callModel::where('code', $request->price_entered)
                ->where('status', 5)
                ->update([
                    'status' => 1,
                ]);

            Import_equipment_request_details::where('import_request_code', $request->price_entered)
                ->update(['status' => 1]);

            toastr()->success('Đã nhập giá cho danh sách');

            return redirect()->back();
        }

        if (!empty($request->import_reqest_codes)) {

            if ($request->action_type === 'browse') {

                $this->callModel::whereIn('code', $request->import_reqest_codes)->where('status', 0)->update(['status' => 2]);

                Import_equipment_request_details::whereIn('import_request_code', $request->import_reqest_codes)
                    ->update(['status' => 2]);

                toastr()->success('Duyệt phiếu chờ thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $this->callModel::whereIn('code', $request->import_reqest_codes)->delete();

                toastr()->success('Hủy thành công');

                return redirect()->back();
            }
        }

        $allReceiptNo = Receipts::pluck('receipt_no');

        return view("{$this->route}.import_equipment_request.index", compact('title', 'AllEquipmentRequest', 'AllSupplier', 'AllUser', 'allReceiptNo'));
    }

    public function exportExcelEquipmentRequestList($code)
    {
        $equipmentRequestList = Import_equipment_request_details::with(['equipments'])
            ->where('import_request_code', $code)
            ->get();

        return Excel::download(new EquipmentRequest($equipmentRequestList), 'YeuCauBaoGiaThietBi_' . now() . '.xlsx');
    }

    public function import_equipment_request_trash(Request $request)
    {
        $title = 'Yêu Cầu Mua Hàng';

        $AllEquipmentRequestTrash = $this->callModel::with(['suppliers', 'users', 'import_equipment_request_details'])
            ->orderBy('deleted_at', 'DESC')
            ->onlyTrashed()
            ->paginate(10);

        if (!empty($request->delete_request)) {
            $this->callModel::where('code', $request->delete_request)
                ->forceDelete();

            toastr()->success('Đã xóa vĩnh viễn yêu cầu mua hàng');

            return redirect()->back();
        }

        if (!empty($request->restore_request)) {
            $this->callModel::where('code', $request->restore_request)->restore();

            toastr()->success('Đã khôi phục phiếu yêu cầu mua hàng');

            return redirect()->back();
        }

        if (!empty($request->import_reqest_codes)) {

            if ($request->action_type === 'restore') {

                $this->callModel::whereIn('code', $request->import_reqest_codes)->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $this->callModel::whereIn('code', $request->import_reqest_codes)->forceDelete();

                toastr()->success('Xóa vĩnh viễn thành công');

                return redirect()->back();
            }
        }

        return view("{$this->route}.import_equipment_request.trash", compact('title', 'AllEquipmentRequestTrash'));
    }

    public function create_import_equipment_request(Request $request)
    {
        $title = 'Yêu Cầu Mua Hàng';

        $action = 'create';

        $AllSupplier = Suppliers::orderBy('created_at', 'DESC')->get();

        $AllEquipment = Equipments::orderBy('created_at', 'DESC')->get();

        if (!empty($request->name)) {
            $supplier = Suppliers::create([
                'code' => 'SUP' . $this->generateRandomString(7),
                'name' => $request->name,
            ]);

            if ($supplier) {
                return response()->json([
                    'success' => true,
                    'code' => $supplier->code,
                    'name' => $supplier->name,
                ]);
            }
        }

        if (!empty($request->equipment) && !empty($request->quantity)) {
            $equipment = Equipments::where('code', $request->equipment)->first();

            if ($equipment) {
                return response()->json([
                    'success' => true,
                    'equipment_name' => $equipment->name,
                    'inventory' => $equipment->inventories->sum('current_quantity'),
                    'unit' => $equipment->units->name,
                    'quantity' => $request->quantity,
                    'equipment_code' => $equipment->code,
                ]);
            }
        }

        return view("{$this->route}.import_equipment_request.form", compact('title', 'action', 'AllSupplier', 'AllEquipment'));
    }

    public function store_import_equipment_request(Request $request)
    {
        if (!empty($request->input('supplier_code')) && !empty($request->input('equipment_list')) && !empty($request->input('importEquipmentStatus'))) {
            $supplierCode = $request->input('supplier_code');
            $note = $request->input('note');
            $equipmentList = json_decode($request->input('equipment_list'), true);

            $existingEquipment = Import_equipment_request_details::whereIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where(function ($query) {
                    $query->where('status', 0)
                        ->orWhere('status', 3);
                })
                ->where('created_at', '>', now()->subDays(3))
                ->get(['equipment_code']);

            if ($existingEquipment->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiết bị yêu cầu mua đã tồn tại trong lịch sử yêu cầu hoặc thùng rác, vui lòng kiểm tra lại',
                    'list_duplicated' => $existingEquipment->pluck('equipment_code')->toArray(),
                ]);
            }

            // Tạo yêu cầu nhập thiết bị
            $insertImportEquipmentRequest = $this->callModel::create([
                'code' => 'YCMH' . $this->generateRandomString(6),
                'user_code' => session('user_code'),
                'supplier_code' => $supplierCode,
                'note' => $note ?? '',
                'status' => $request->input('importEquipmentStatus') == 4 ? 0 : $request->input('importEquipmentStatus'),
                'request_date' => now(),
                'created_at' => now(),
                'updated_at' => null,
            ]);

            if ($insertImportEquipmentRequest) {
                foreach ($equipmentList as $equipment) {
                    Import_equipment_request_details::create([
                        'import_request_code' => $insertImportEquipmentRequest->code,
                        'equipment_code' => $equipment['equipment_code'],
                        'quantity' => $equipment['quantity'],
                        'status' => $request->input('importEquipmentStatus') == 4 ? 0 : $request->input('importEquipmentStatus'),
                        'created_at' => $insertImportEquipmentRequest->request_date,
                        'updated_at' => null,
                    ]);
                }

                return response()->json(['success' => true, 'message' => 'Đã tạo phiếu yêu cầu']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    public function delete_supplier($code)
    {
        $importExists = Import_equipment_requests::where('supplier_code', $code)->exists();
        $receiptsExists = Receipts::where('supplier_code', $code)->exists();

        if ($importExists || $receiptsExists) {
            return response()->json([
                'success' => false,
                'messages' => 'Không thể xóa nhà cung cấp này vì đã có giao dịch trong hệ thống'
            ]);
        }

        $supplier = Suppliers::where('code', $code)->whereNull('deleted_at')->first();

        $supplier->delete();

        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'messages' => 'Đã xóa nhà cung cấp'
        ]);
    }

    public function update_import_equipment_request($code)
    {
        $title = 'Yêu Cầu Mua Hàng';

        $action = 'update';

        $AllSupplier = Suppliers::orderBy('created_at', 'DESC')->get();

        $AllEquipment = Equipments::orderBy('created_at', 'DESC')->get();

        $equipmentDetail = Import_equipment_request_details::where('import_request_code', $code);

        $getList = $equipmentDetail->get();

        $checkList = $equipmentDetail->pluck('equipment_code')->toArray();

        $editForm = $this->callModel::with(['suppliers', 'users'])
            ->where('code', $code)
            ->whereNull('deleted_at')
            ->first();

        return view("{$this->route}.import_equipment_request.form", compact('title', 'action', 'AllEquipment', 'AllSupplier', 'editForm', 'getList', 'checkList'));
    }

    public function edit_import_equipment_request(Request $request, $code)
    {
        if (!empty($request->input('supplier_code')) && !empty($request->input('equipment_list'))) {
            $supplierCode = $request->input('supplier_code');
            $note = $request->input('note');
            $equipmentList = json_decode($request->input('equipment_list'), true);

            $existingEquipment = Import_equipment_request_details::whereIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where('import_request_code', '!=', $code)
                ->where(function ($query) {
                    $query->where('status', 0)
                        ->orWhere('status', 3);
                })
                ->where('created_at', '>', now()->subDays(3))
                ->get(['equipment_code']);

            if ($existingEquipment->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiết bị yêu cầu mua đã tồn tại trong lịch sử yêu cầu hoặc thùng rác, vui lòng kiểm tra lại',
                    'list_duplicated' => $existingEquipment->pluck('equipment_code')->toArray(),
                ]);
            }

            // Tìm các bản ghi không có mã trong $equipmentList và thuộc về import_request_code
            $equipmentToDelete = Import_equipment_request_details::whereNotIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where('import_request_code', $code)
                ->get();

            // Xóa các bản ghi tìm thấy
            if ($equipmentToDelete->isNotEmpty()) {
                $equipmentToDelete->each(function ($item) {
                    $item->forceDelete();
                });
            }

            $existingRequest = $this->callModel::where('code', $code);

            $record = $existingRequest->first();

            $existingRequest->update([
                'supplier_code' => $supplierCode ?? $record->supplier_code,
                'note' => $note ?? $record->note,
                'status' => $record->status,
                'updated_at' => now(),
            ]);

            foreach ($equipmentList as $equipment) {
                Import_equipment_request_details::updateOrCreate(
                    [
                        'import_request_code' => $code,
                        'equipment_code' => $equipment['equipment_code']
                    ],
                    [
                        'quantity' => $equipment['quantity'],
                        'status' => $record->status,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Cập nhật phiếu yêu cầu mua thiết bị thành công']);
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    public function edit_import_equipment_request_price(Request $request, $code)
    {
        if (!empty($request->input('supplier_code')) && !empty($request->input('equipment_list'))) {
            $supplierCode = $request->input('supplier_code');
            $note = $request->input('note');
            $equipmentList = json_decode($request->input('equipment_list'), true);

            $existingRequest = $this->callModel::where('code', $code);

            $record = $existingRequest->first();

            $existingRequest->update([
                'supplier_code' => $supplierCode ?? $record->supplier_code,
                'note' => $note ?? $record->note,
                'status' => $record->status,
                'updated_at' => now(),
            ]);

            foreach ($equipmentList as $equipment) {
                Import_equipment_request_details::updateOrCreate(
                    [
                        'import_request_code' => $code,
                        'equipment_code' => $equipment['equipment_code']
                    ],
                    [
                        'quantity' => $equipment['quantity'],
                        'price' => $equipment['price'],
                        'status' => $record->status,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Cập nhật số lượng và giá thành công']);
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    // Xuất

    public function export_equipment_request(Request $request)
    {
        $title = 'Yêu Cầu Xuất Kho';

        $AllDepartment = Departments::orderBy('created_at', 'DESC')->get();

        $AllUser = Users::orderBy('created_at', 'DESC')->get();

        $AllWarehouseExportRequest = Export_equipment_requests::with(['departments', 'users', 'export_equipment_request_details'])
            ->orderBy('request_date', 'DESC')
            ->whereNull('deleted_at');

        if (isset($request->dpm)) {
            $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("department_code", $request->dpm);
        }

        if (isset($request->us)) {
            $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("user_code", $request->us);
        }

        if (isset($request->stt)) {
            if ($request->stt == 2) {

                $AllWarehouseExportRequest = $AllWarehouseExportRequest
                    ->where(function ($query) {
                        $query->where('status', 0)
                            ->orWhere('status', 3);
                    })
                    ->where("required_date", '<', now());
            } elseif ($request->stt == 3) {

                $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("status", 3)
                    ->where("required_date", '>', now());
            } elseif ($request->stt == 0) {

                $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("status", 0)
                    ->where("required_date", '>', now());
            } elseif ($request->stt == 4) {

                $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("status", 4);
            } elseif ($request->stt == 5) {

                $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("status", 5);
            } else {

                $AllWarehouseExportRequest = $AllWarehouseExportRequest->where("status", 1);
            }
        }

        if (isset($request->kw)) {

            $AllWarehouseExportRequest = $AllWarehouseExportRequest->where(function ($query) use ($request) {
                $query->where('code', 'like', '%' . $request->kw . '%');
            });
        }

        $AllWarehouseExportRequest = $AllWarehouseExportRequest->paginate(10);

        if (!empty($request->save_status)) {
            $record = Export_equipment_requests::where('code', $request->save_status)
                ->update([
                    'status' => 0,
                ]);

            toastr()->success('Phiếu tạm đã được tạo và đang ở trạng thái chờ duyệt');

            return redirect()->back();
        }

        if (!empty($request->delete_request)) {
            Export_equipment_requests::where('code', $request->delete_request)
                ->delete();

            toastr()->success('Đã hủy yêu cầu xuất kho');

            return redirect()->back();
        }

        if (!empty($request->browse_request)) {
            Export_equipment_requests::where('code', $request->browse_request)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                ]);

            Export_equipment_request_details::where('export_request_code', $request->browse_request)
                ->update(['status' => 1]);

            toastr()->success('Đã duyệt phiếu yêu cầu mua hàng');

            return redirect()->back();
        }

        if (!empty($request->export_reqest_codes)) {

            if ($request->action_type === 'browse') {

                Export_equipment_requests::whereIn('code', $request->export_reqest_codes)->where('status', 0)->update(['status' => 1]);

                Export_equipment_request_details::whereIn('export_request_code', $request->export_reqest_codes)
                    ->update(['status' => 1]);

                toastr()->success('Duyệt phiếu chờ thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                Export_equipment_requests::whereIn('code', $request->export_reqest_codes)->delete();

                toastr()->success('Hủy thành công');

                return redirect()->back();
            }
        }

        return view("{$this->route}.export_equipment_request.index", compact('title', 'AllWarehouseExportRequest', 'AllDepartment', 'AllUser'));
    }

    public function export_equipment_request_trash(Request $request)
    {
        $title = 'Yêu Cầu Xuất Kho';

        $AllWarehouseExportRequestTrash = Export_equipment_requests::with(['departments', 'users', 'export_equipment_request_details'])
            ->orderBy('deleted_at', 'DESC')
            ->onlyTrashed()
            ->paginate(10);

        if (!empty($request->delete_request)) {
            Export_equipment_requests::where('code', $request->delete_request)
                ->forceDelete();

            toastr()->success('Đã xóa vĩnh viễn yêu cầu xuất kho');

            return redirect()->back();
        }

        if (!empty($request->restore_request)) {
            Export_equipment_requests::where('code', $request->restore_request)
                ->restore();

            toastr()->success('Đã khôi phục yêu cầu xuất kho');

            return redirect()->back();
        }

        if (!empty($request->export_reqest_codes)) {

            if ($request->action_type === 'restore') {

                Export_equipment_requests::whereIn('code', $request->export_reqest_codes)->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                Export_equipment_requests::whereIn('code', $request->export_reqest_codes)->forceDelete();

                toastr()->success('Xóa vĩnh viễn thành công');

                return redirect()->back();
            }
        }

        return view("{$this->route}.export_equipment_request.trash", compact('title', 'AllWarehouseExportRequestTrash'));
    }

    public function create_export_equipment_request(Request $request)
    {
        $title = 'Yêu Cầu Xuất Kho';

        $action = 'create';

        $AllDepartment = Departments::orderBy('created_at', 'DESC')->get();

        $AllEquipment = Equipments::orderBy('created_at', 'DESC')->get();

        if (!empty($request->name) && !empty($request->location)) {
            $department = Departments::create([
                'code' => 'PB' . $this->generateRandomString(8),
                'name' => $request->name,
                'location' => $request->location,
            ]);

            if ($department) {
                return response()->json([
                    'success' => true,
                    'code' => $department->code,
                    'name' => $department->name,
                    'location' => $department->location,
                ]);
            }
        }

        if (!empty($request->equipment) && !empty($request->quantity)) {
            $equipment = Equipments::where('code', $request->equipment)->first();

            if ($equipment) {
                return response()->json([
                    'success' => true,
                    'equipment_name' => $equipment->name,
                    'inventory' => $equipment->inventories->sum('current_quantity'),
                    'unit' => $equipment->units->name,
                    'quantity' => $request->quantity,
                    'equipment_code' => $equipment->code,
                ]);
            }
        }

        return view("{$this->route}.export_equipment_request.form", compact('title', 'action', 'AllDepartment', 'AllEquipment'));
    }

    public function store_export_equipment_request(Request $request)
    {
        if (!empty($request->input('department_code')) && !empty($request->input('reason_export')) && !empty($request->input('required_date')) && !empty($request->input('equipment_list')) && !empty($request->input('exportEquipmentStatus'))) {
            $departmentCode = $request->input('department_code');
            $reasonExport = $request->input('reason_export');
            $requiredDate = $request->input('required_date');
            $note = $request->input('note');
            $equipmentList = json_decode($request->input('equipment_list'), true);

            $existingEquipment = Export_equipment_request_details::whereIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where(function ($query) {
                    $query->where('status', 0)
                        ->orWhere('status', 3);
                })
                ->where('created_at', '>', now()->subDays(3))
                ->whereHas('exportEquipmentRequests', function ($query) use ($departmentCode) {
                    $query->where('department_code', $departmentCode);
                })
                ->get(['equipment_code']);

            if ($existingEquipment->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiết bị yêu cầu xuất kho trong danh sách đã tồn tại trong lịch sử yêu cầu hoặc thùng rác, vui lòng kiểm tra lại',
                    'list_duplicated' => $existingEquipment->pluck('equipment_code')->toArray(),
                ]);
            }

            // Tạo yêu cầu nhập thiết bị
            $insertExportEquipmentRequest = Export_equipment_requests::create([
                'code' => 'YCXK' . $this->generateRandomString(6),
                'user_code' => session('user_code'),
                'department_code' => $departmentCode,
                'reason_export' => $reasonExport,
                'note' => $note ?? '',
                'status' => $request->input('exportEquipmentStatus') == 4 ? 0 : $request->input('exportEquipmentStatus'),
                'request_date' => now(),
                'required_date' => $requiredDate,
                'created_at' => now(),
                'updated_at' => null,
            ]);

            if ($insertExportEquipmentRequest) {
                foreach ($equipmentList as $equipment) {
                    Export_equipment_request_details::create([
                        'export_request_code' => $insertExportEquipmentRequest->code,
                        'equipment_code' => $equipment['equipment_code'],
                        'quantity' => $equipment['quantity'],
                        'status' => $request->input('exportEquipmentStatus') == 4 ? 0 : $request->input('exportEquipmentStatus'),
                        'created_at' => $insertExportEquipmentRequest->request_date,
                        'updated_at' => null,
                    ]);
                }

                return response()->json(['success' => true, 'message' => 'Đã tạo phiếu yêu cầu']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    public function update_export_equipment_request($code)
    {
        $title = 'Yêu Cầu Xuất Kho';

        $action = 'update';

        $AllDepartment = Departments::orderBy('created_at', 'DESC')->get();

        $AllEquipment = Equipments::orderBy('created_at', 'DESC')->get();

        $equipmentDetail = Export_equipment_request_details::where('export_request_code', $code);

        $getList = $equipmentDetail->get();

        $checkList = $equipmentDetail->pluck('equipment_code')->toArray();

        $editForm = Export_equipment_requests::with(['departments', 'users', 'export_equipment_request_details'])
            ->where('code', $code)
            ->whereNull('deleted_at')
            ->first();

        return view("{$this->route}.export_equipment_request.form", compact('title', 'action', 'AllEquipment', 'AllDepartment', 'editForm', 'getList', 'checkList'));
    }

    public function edit_export_equipment_request(Request $request, $code)
    {
        if (!empty($request->input('department_code')) && !empty($request->input('reason_export')) && !empty($request->input('required_date')) && !empty($request->input('equipment_list')) && !empty($request->input('exportEquipmentStatus'))) {
            $departmentCode = $request->input('department_code');
            $reasonExport = $request->input('reason_export');
            $requiredDate = $request->input('required_date');
            $note = $request->input('note');
            $equipmentList = json_decode($request->input('equipment_list'), true);

            $existingEquipment = Export_equipment_request_details::whereIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where('export_request_code', '!=', $code)
                ->where(function ($query) {
                    $query->where('status', 0)
                        ->orWhere('status', 3);
                })
                ->where('created_at', '>', now()->subDays(3))
                ->whereHas('exportEquipmentRequests', function ($query) use ($departmentCode) {
                    $query->where('department_code', $departmentCode);
                })
                ->get(['equipment_code']);

            if ($existingEquipment->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiết bị yêu cầu xuất kho trong danh sách đã tồn tại trong lịch sử yêu cầu hoặc thùng rác, vui lòng kiểm tra lại',
                    'list_duplicated' => $existingEquipment->pluck('equipment_code')->toArray(),
                ]);
            }

            // Tìm các bản ghi không có mã trong $equipmentList và thuộc về export_request_code
            $equipmentToDelete = Export_equipment_request_details::whereNotIn('equipment_code', array_column($equipmentList, 'equipment_code'))
                ->where('export_request_code', $code)
                ->get();

            // Xóa các bản ghi tìm thấy
            if ($equipmentToDelete->isNotEmpty()) {
                $equipmentToDelete->each(function ($item) {
                    $item->forceDelete();
                });
            }

            $existingRequest = Export_equipment_requests::where('code', $code);

            $record = $existingRequest->first();

            $existingRequest->update([
                'department_code' => $departmentCode,
                'reason_export' => $reasonExport,
                'note' => $note ?? $record->note,
                'request_date' => now(),
                'required_date' => $requiredDate,
                'updated_at' => now(),
            ]);

            foreach ($equipmentList as $equipment) {
                Export_equipment_request_details::updateOrCreate(
                    [
                        'export_request_code' => $code,
                        'equipment_code' => $equipment['equipment_code']
                    ],
                    [
                        'quantity' => $equipment['quantity'],
                        'status' => $record->status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Cập nhật phiếu yêu cầu xuất kho thành công']);
        }

        return response()->json(['success' => false, 'message' => 'Vui lòng điền đẩy đủ các trường dữ liệu']);
    }

    public function delete_department($code)
    {
        $importExists = Export_equipment_requests::where('department_code', $code)->exists();
        $departmentExists = Exports::where('department_code', $code)->exists();

        if ($importExists || $departmentExists) {
            return response()->json([
                'success' => false,
                'messages' => 'Không thể xóa phòng ban này vì đã có giao dịch trong hệ thống'
            ]);
        }

        $department = Departments::where('code', $code)->whereNull('deleted_at')->first();

        $department->delete();

        return response()->json([
            'success' => true,
            'department' => $department,
            'messages' => 'Đã xóa phòng ban'
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
}

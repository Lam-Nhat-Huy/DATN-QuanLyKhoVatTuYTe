<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\Equipments;
use App\Models\Export_details;
use App\Models\Exports;
use App\Models\Inventories;
use App\Models\Receipts;
use App\Models\Users;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected $route = 'warehouse';
    protected $inventories = [];
    protected $equipments = [];
    protected $users = [];
    protected $departments = [];
    protected $exports = [];
    protected $exportDetails = [];

    public function __construct()
    {
        $this->inventories = Inventories::all();
        $this->equipments = Equipments::all();
        $this->departments = Departments::all();
        $this->users = Users::where('code', session('user_code'))->first();
    }

    public function export(Request $request)
    {
        $title = 'Xuất Kho';
        $exports = Exports::with(['exportDetail', 'user'])->get();
        if ($request->ajax()) {
            $equipment_code = $request->input('equipment_code');
            $inventories = Inventories::where('equipment_code', $equipment_code)->get();

            return response()->json($inventories);
        }
        $equipments = Equipments::with('inventories')->get();

        foreach ($equipments as $equipment) {
            $equipment->total_inventory = $equipment->inventories->sum('current_quantity');
        }
        return view(
            "{$this->route}.export_warehouse.export",
            [
                'title' => $title,
                'exports' => $exports,
                'departments' => $this->departments,
                'equipments' => $equipments,
                'inventories' => $this->inventories,
            ]
        );
    }

    public function create_export(Request $request)
    {
        $title = 'Tạo phiếu xuất kho';

        if ($request->ajax()) {
            $equipment_code = $request->input('equipment_code');
            $inventories = Inventories::with('equipments')
                ->where('equipment_code', $equipment_code)
                ->get();

            return response()->json($inventories);
        }
        $equipments = Equipments::with('inventories')->get();

        foreach ($equipments as $equipment) {
            $equipment->total_inventory = $equipment->inventories->sum('current_quantity');
        }

        return view(
            "{$this->route}.export_warehouse.add_export",
            [
                'equipments' => $equipments,
                'inventories' => $this->inventories,
                'users' => $this->users,
                'departments' => $this->departments,
                'title' => $title
            ]
        );
    }


    public function store_export(Request $request)
    {
        $materialList = json_decode($request->material_list, true);
        if (empty($materialList)) {
            return redirect()->back()->with('error', 'Danh sách vật tư không hợp lệ.');
        }
        $export = new Exports();
        $export->code = 'EXP' . time();
        $export->note = $request->note;
        $export->status = $request->input('status');
        $export->created_by = session('user_code');
        $export->export_date = $request->export_at;
        $export->department_code = $request->department_code;
        $export->save();

        foreach ($materialList as $material) {
            if (!isset($material['equipment_code'], $material['quantity'], $material['batch_number'])) {
                return redirect()->back()->with('error', 'Thông tin vật tư không đầy đủ.');
            }

            $exportDetail = new Export_details();
            $exportDetail->export_code = $export->code;
            $exportDetail->equipment_code = $material['equipment_code'];
            $exportDetail->quantity = $material['quantity'];
            $exportDetail->batch_number = $material['batch_number'];
            $exportDetail->save();

            if ($export->status == 1) {
                $inventory = Inventories::where('equipment_code', $material['equipment_code'])
                    ->where('batch_number', $material['batch_number'])
                    ->first();
                if ($inventory) {
                    if ($inventory->current_quantity >= $material['quantity']) {
                        $inventory->current_quantity -= $material['quantity'];
                        $inventory->save();
                    } else {
                        return redirect()->back()->with('error', 'Số lượng trong kho không đủ để xuất cho vật tư ' . $material['equipment_code'] . ' - Số lô: ' . $material['batch_number'] . '.');
                    }
                } else {
                    return redirect()->back()->with('error', 'Không tìm thấy vật tư trong kho cho ' . $material['equipment_code'] . ' - Số lô: ' . $material['batch_number'] . '.');
                }
            }
        }
        toastr()->success('Tạo phiếu xuất thành công');
        return redirect()->route('warehouse.export');
    }

    public function approve_export(Request $request)
    {
        $exportCode = $request->input('export_code');
        $export = Exports::where('code', $exportCode)->first();

        if (!$export) {
            return redirect()->back()->with('error', 'Phiếu xuất không tồn tại.');
        }

        // Cập nhật trạng thái hoặc thực hiện các thao tác cần thiết
        $export->status = true; // Hoặc một trạng thái khác mà bạn cần
        $export->save();

        return redirect()->route('warehouse.export')->with('success', 'Duyệt phiếu thành công.');
    }
}
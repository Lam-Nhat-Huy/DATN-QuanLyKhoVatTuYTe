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

    public function export()
    {
        $title = 'Xuất Kho';
        $exports = Exports::with('exportDetail')->get();
        return view(
            "{$this->route}.export_warehouse.export",
            [
                'title' => $title,
                'exports' => $exports,
                'departments' => $this->departments,
            ]
        );
    }

    public function create_export(Request $request)
    {
        $title = 'Tạo phiếu xuất kho';
        if ($request->ajax()) {
            $equipment_code = $request->input('equipment_code');
            $inventories = Inventories::where('equipment_code', $equipment_code)->get();

            return response()->json($inventories);
        }
        return view(
            "{$this->route}.export_warehouse.add_export",
            [
                'equipments' => $this->equipments,
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
        $export->export_date = $request->export_at;
        $export->department_code = $request->department_code;
        $export->save();

        // Lưu thông tin chi tiết phiếu xuất và cập nhật kho
        foreach ($materialList as $material) {
            // Kiểm tra xem thông tin vật tư có hợp lệ không
            if (!isset($material['equipment_code'], $material['quantity'], $material['batch_number'])) {
                return redirect()->back()->with('error', 'Thông tin vật tư không đầy đủ.');
            }

            // Lưu thông tin chi tiết phiếu xuất
            $exportDetail = new Export_details();
            $exportDetail->export_code = $export->code;
            $exportDetail->equipment_code = $material['equipment_code'];
            $exportDetail->quantity = $material['quantity'];
            $exportDetail->batch_number = $material['batch_number'];
            $exportDetail->save();

            // Kiểm tra trạng thái trước khi cập nhật kho
            if ($export->status == 1) { // Nếu trạng thái không bằng 1 thì trừ số lượng
                // Cập nhật số lượng trong kho
                $inventory = Inventories::where('equipment_code', $material['equipment_code'])
                    ->where('batch_number', $material['batch_number'])
                    ->first();
                if ($inventory) {
                    // Kiểm tra xem số lượng trong kho có đủ để xuất không
                    if ($inventory->current_quantity >= $material['quantity']) {
                        // Trừ số lượng đã xuất
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

        return redirect()->route('warehouse.export')->with('success', 'Phiếu xuất đã được tạo thành công.');
    }
}

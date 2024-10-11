<?php

namespace App\Http\Controllers\Equipments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\CreateEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Http\Requests\EquipmentType\CreateEquipmentType;
use App\Http\Requests\EquipmentType\UpdateEquipmentType;
use App\Models\Equipment_types;
use App\Models\Equipments;
use App\Models\Export_details;
use App\Models\Export_equipment_request_details;
use App\Models\Import_equipment_request_details;
use App\Models\Inventories;
use App\Models\Inventory_check_details;
use App\Models\Receipt_details;
use App\Models\Suppliers;
use App\Models\Units;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipmentsController extends Controller
{
    protected $route = 'equipments';

    protected $equipmentModal;

    public function __construct()
    {
        $this->equipmentModal = new Equipments;
    }

    public function index(Request $request)
    {
        $title = 'Thiết Bị';

        $AllEquipment = $this->equipmentModal::with(['supplier', 'units', 'equipmentType', 'inventories'])->orderBy('created_at', 'DESC')->whereNull('deleted_at');

        if (isset($request->un)) {
            $AllEquipment = $AllEquipment->where("unit_code", $request->un);
        }

        if (isset($request->et)) {
            $AllEquipment = $AllEquipment->where("equipment_type_code", $request->et);
        }

        if (isset($request->sp)) {
            $AllEquipment = $AllEquipment->where("supplier_code", $request->sp);
        }

        if (isset($request->ct)) {
            $AllEquipment = $AllEquipment->where("country", 'LILE', '%' . $request->ct . '%');
        }

        if (isset($request->kw)) {
            $AllEquipment = $AllEquipment->where('name', 'LIKE', '%' . $request->kw . '%')
                ->orWhere('code', 'LIKE', '%' . $request->kw . '%')
                ->orWhere('description', 'LIKE', '%' . $request->kw . '%');
        }

        $AllEquipment = $AllEquipment->paginate(10);

        foreach ($AllEquipment as $item) {
            if ($item->expiry_date) {
                $expiryDate = Carbon::parse($item->expiry_date);
                $currentDate = Carbon::now();
                $diff = $currentDate->diff($expiryDate);

                if ($currentDate->lt($expiryDate)) {
                    if ($diff->y > 0) {
                        $item->time_remaining = 'Hết hạn sau ' . $diff->y . ' năm ' . $diff->m . ' tháng ' . $diff->d . ' ngày';
                    } elseif ($diff->m > 0) {
                        $item->time_remaining = 'Hết hạn sau ' . $diff->m . ' tháng ' . $diff->d . ' ngày';
                    } elseif ($diff->d > 0) {
                        $item->time_remaining = 'Hết hạn sau ' . $diff->d . ' ngày';
                    } else {
                        $item->time_remaining = 'Sắp hết hạn';
                    }
                } else {
                    $item->time_remaining = 'Đã hết hạn';
                }
            } else {
                $item->time_remaining = '';
            }
        }

        if (!empty($request->equipment_codes)) {

            $checkDelete = $this->checkRelatedTables($request->equipment_codes);

            $arrayEquipmentNew = array_diff($request->equipment_codes, $checkDelete);

            if (!empty($arrayEquipmentNew)) {

                $this->equipmentModal::whereIn('code', $arrayEquipmentNew)->delete();

                toastr()->success('Đã xóa thiết bị');

                return redirect()->back();
            }

            toastr()->error('Thiết bị này tồn tại trong giao dịch của hệ thống, không thể xóa');

            return redirect()->back();
        }

        $equipmentTypes = Equipment_types::orderBy('created_at', 'DESC')->get();

        $units = Units::orderBy('created_at', 'DESC')->get();

        $suppliers = Suppliers::orderBy('created_at', 'DESC')->get();

        return view("equipments.list", compact('title', 'AllEquipment', 'equipmentTypes', 'units', 'suppliers'));
    }

    public function equipment_trash(Request $request)
    {
        $title = 'Thiết Bị';

        $AllEquipmentTrash = $this->equipmentModal::onlyTrashed()->paginate(10);

        if (isset($request->equipment_codes)) {

            if ($request->action_type === 'restore') {

                $this->equipmentModal::whereIn('code', $request->equipment_codes)->restore();

                toastr()->success('Khôi phục thiết bị thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $equipments = $this->equipmentModal::withTrashed()->whereIn('code', $request->equipment_codes)->get();

                foreach ($equipments as $equipment) {

                    if ($equipment->image) {
                        $imagePath = public_path('images/equipments/' . $equipment->image);
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    $equipment->forceDelete();
                }

                toastr()->success('Đã xóa vĩnh viễn thiết bị');

                return redirect()->back();
            }
        }

        return view("equipments.equipment_trash", compact('title', 'AllEquipmentTrash'));
    }

    public function insert_equipment()
    {
        $title = 'Thiết Bị';
        $title_form = 'Thêm Thiết Bị';
        $action = 'create';

        // Lấy danh sách các nhóm thiết bị có trạng thái "Có"
        $equipmentTypes = Equipment_types::where('status', 1)->get();

        // Tương tự với các đơn vị tính hoặc nhà cung cấp nếu cần thiết
        $units = Units::orderBy('name', 'ASC')->get();
        $suppliers = Suppliers::orderBy('name', 'ASC')->get();
        $AllSuppiler = Suppliers::orderBy('name', 'ASC')->get();

        return view('equipments.form_equipment', compact('title', 'action', 'title_form', 'equipmentTypes', 'suppliers', 'units', 'AllSuppiler'));
    }

    public function create_equipment(CreateEquipmentRequest $request)
    {
        $data = $request->validated();

        $data['code'] = 'EQ' . $this->generateRandomString(8);

        $data['barcode'] = $data['code'];

        if ($request->file('equipment_image')) {

            $data['image'] = $request->file('equipment_image')->store('uploads', 'public');
        }

        $data['expiry_date'] = $request->expiry_date ?? null;

        $this->equipmentModal::create($data);

        toastr()->success('Thiết bị đã được thêm thành công!');

        return redirect()->route('equipments.index');
    }

    public function update_equipment($code)
    {
        $title = 'Thiết Bị';
        $title_form = 'Cập Nhật Thiết Bị';
        $action = 'edit';

        $AllSuppiler = Suppliers::orderBy('created_at', 'DESC')->get();

        // Tìm thiết bị bằng code thay vì id
        $currentEquipment = $this->equipmentModal::where('code', $code)->firstOrFail();
        $equipmentTypes = Equipment_types::all();
        $suppliers = Suppliers::all();
        $units = Units::all();

        // Truyền currentEquipment sang view với tên 'equipment'
        return view("equipments.form_equipment", compact('title', 'action', 'title_form', 'currentEquipment', 'equipmentTypes', 'suppliers', 'units', 'AllSuppiler'))
            ->with('equipment', $currentEquipment);
    }

    public function edit_equipment(UpdateEquipmentRequest $request, $code)
    {
        $data = $request->validated();

        $equipment = $this->equipmentModal::find($code);

        if (!empty($request->file('equipment_image'))) {

            if ($equipment->image) {

                Storage::disk('public')->delete($equipment->image);
            }

            $data['image'] = $request->file('equipment_image')->store('uploads', 'public');
        }

        $data['expiry_date'] = $request->expiry_date ?? null;

        $data['updated_at'] = now();

        $equipment->update($data);

        toastr()->success('Thiết bị đã được cập nhật thành công!');

        return redirect()->route('equipments.index');
    }

    public function delete_equipment($code)
    {
        $checkDelete = $this->checkRelatedTables([$code]);

        if (!$checkDelete) {

            $this->equipmentModal::where('code', $code)->delete();

            toastr()->success('Đã xóa thiết bị');
            return redirect()->back();
        }

        toastr()->error('Thiết bị này tồn tại trong giao dịch của hệ thống, không thể xóa');

        return redirect()->back();
    }

    public function delete_permanently($code)
    {
        // Tìm thiết bị đã bị xóa mềm
        $equipment = $this->equipmentModal::withTrashed()->where('code', $code)->firstOrFail();

        // Xóa vĩnh viễn thiết bị
        $equipment->forceDelete();

        // Xóa ảnh nếu cần thiết
        if ($equipment->image) {
            $imagePath = public_path('images/equipments/' . $equipment->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        toastr()->success('Xóa vĩnh viễn thiết bị thành công');

        return redirect()->route('equipments.equipments_trash');
    }

    public function restore_equipment($code)
    {
        $this->equipmentModal::withTrashed()->where('code', $code)->restore();

        toastr()->success('Khôi phục thiết bị thành công');

        return redirect()->route('equipments.equipments_trash');
    }

    public function create_equipment_group_modal(Request $request)
    {
        if (!empty($request->name)) {
            $equipment_type = Equipment_types::create([
                'code' => 'NTB' . $this->generateRandomString(7),
                'name' => $request->name,
            ]);

            if ($equipment_type) {
                return response()->json([
                    'success' => true,
                    'code' => $equipment_type->code,
                    'name' => $equipment_type->name,
                ]);
            }
        }
    }

    public function delete_equipment_group_modal($code)
    {
        $checkExists = Equipments::where('equipment_type_code', $code)->exists();

        if ($checkExists) {
            return response()->json([
                'success' => false,
                'messages' => 'Không thể xóa nhóm thiết bị này vì đã tồn tại liên quan đến thiết bị'
            ]);
        }

        $equipment_group = Equipment_types::where('code', $code)->whereNull('deleted_at')->first();

        $equipment_group->delete();

        return response()->json([
            'success' => true,
            'equipment_group' => $equipment_group,
            'messages' => 'Đã xóa nhóm thiết bị'
        ]);
    }

    public function create_unit_modal(Request $request)
    {
        if (!empty($request->unit_name_conversion)) {
            $units = Units::create([
                'code' => 'UNIT' . $this->generateRandomString(6),
                'name' => $request->unit_name_conversion,
            ]);

            if ($units) {
                return response()->json([
                    'success' => true,
                    'code' => $units->code,
                    'name' => $units->name,
                ]);
            }
        }
    }

    public function delete_unit_modal($code)
    {
        $checkExists = Equipments::where('unit_code', $code)->exists();

        if ($checkExists) {
            return response()->json([
                'success' => false,
                'messages' => 'Không thể xóa đơn vị tính này vì đã tồn tại liên quan đến thiết bị'
            ]);
        }

        $units = Units::where('code', $code)->whereNull('deleted_at')->first();

        $units->delete();

        return response()->json([
            'success' => true,
            'units' => $units,
            'messages' => 'Đã xóa đơn vị tính'
        ]);
    }

    // Nhóm Thiết Bị

    public function equipment_group(Request $request)
    {
        $title = 'Nhóm Thiết Bị';

        $query = Equipment_types::whereNull('deleted_at');

        if (isset($request->kw)) {
            $query = $query->where(function ($query) use ($request) {
                $query->where('code', 'LIKE', '%' . $request->kw . '%')
                    ->orWhere('name', 'LIKE', '%' . $request->kw . '%');
            });
        }

        if (isset($request->stt)) {
            $query->where('status', $request->stt);
        }

        $AllEquipmentGroup = $query->orderBy('created_at', 'DESC')->paginate(10);

        if (!empty($request->equipment_group_codes)) {

            $checkExists = Equipments::whereIn('equipment_type_code', $request->equipment_group_codes)
                ->pluck('equipment_type_code')
                ->toArray();

            $nonExistingET = array_diff($request->equipment_group_codes, $checkExists);

            if ($nonExistingET) {

                Equipment_types::whereIn('code', $nonExistingET)->delete();

                toastr()->success('Đã xóa nhóm thiết bị không có dữ liệu liên quan đến trang thiết bị');

                return redirect()->back();
            }

            toastr()->error('Nhóm thiết bị không thể xóa vì đã có dữ liệu liên quan đến trang thiết bị');

            return redirect()->back();
        }

        return view("equipments.equipment_group", compact('title', 'AllEquipmentGroup'));
    }

    public function equipment_group_trash(Request $request)
    {
        $title = 'Nhóm Thiết Bị';

        $AllEquipmentGroupTrash = Equipment_types::onlyTrashed()->orderBy('deleted_at', 'DESC')->paginate();

        if (!empty($request->equipment_group_codes)) {

            if ($request->action_type === 'restore') {

                Equipment_types::whereIn('code', $request->equipment_group_codes)->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                Equipment_types::withTrashed()->whereIn('code', $request->equipment_group_codes)->forceDelete();

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (!empty($request->restore_value)) {

            Equipment_types::where('code', $request->restore_value)->restore();

            toastr()->success('Khôi phục thành công');

            return redirect()->back();
        }

        if (!empty($request->delete_value)) {

            $notification = Equipment_types::withTrashed()->where('code', $request->delete_value)->forceDelete();

            toastr()->success('Xóa vĩnh viễn thành công');

            return redirect()->back();
        }

        return view("equipments.equipment_group_trash", compact('title', 'AllEquipmentGroupTrash'));
    }

    public function showCreateForm()
    {
        $title = 'Nhóm Thiết Bị';

        $action = 'create';

        $linkedEquipments = 0;

        return view('equipments.form_group', compact('title', 'action', 'linkedEquipments'));
    }

    public function create_equipment_group(CreateEquipmentType $request)
    {
        $data = $request->validated();

        Equipment_types::create([
            'code' => 'NTB' . $this->generateRandomString(7),
            'name' => $data['name'],
            'description' => $request->description,
            'status' => $request->status ?? 0,
        ]);

        toastr()->success('Đã thêm nhóm thiết bị');

        return redirect()->route('equipments.equipments_group');
    }

    public function update_equipment_group($code)
    {
        $title = 'Nhóm Thiết Bị';

        $equipmentGroup = Equipment_types::where('code', $code)->firstOrFail();

        $action = 'edit';

        $linkedEquipments = $this->equipmentModal::where('equipment_type_code', $code)->count();

        return view('equipments.form_group', compact('title', 'action', 'equipmentGroup', 'linkedEquipments'));
    }

    public function edit_equipment_group(UpdateEquipmentType $request, $code)
    {
        $data = $request->validated();

        $record = Equipment_types::where('code', $code)->first();

        if ($record) {
            $record->update([
                'name' => $data['name'],
                'description' => $request->description,
                'status' => $request->status == null ? 0 : 1,
            ]);

            toastr()->success('Đã cập nhật nhóm thiết bị');

            return redirect()->route('equipments.equipments_group');
        }

        toastr()->success('Xảy ra lỗi');

        return redirect()->route('equipments.equipments_group');
    }

    public function delete_equipment_group($code)
    {
        // Tìm nhóm thiết bị theo mã
        $group = Equipment_types::where('code', $code)->firstOrFail();

        // Kiểm tra xem nhóm thiết bị có liên kết với thiết bị nào không
        $linkedEquipments = $this->equipmentModal::where('equipment_type_code', $code)->count();

        if ($linkedEquipments > 0) {
            // Nếu có thiết bị liên kết, không cho phép xóa và trả về thông báo lỗi
            toastr()->error('Không thể xóa nhóm vì nó đang được liên kết với thiết bị.');
            return redirect()->route('equipments.equipments_group');
        }

        // Nếu không có liên kết, cho phép xóa mềm
        $group->delete();

        toastr()->success('Nhóm thiết bị đã được xóa thành công!');
        return redirect()->route('equipments.equipments_group');
    }

    public function restore_equipment_group($code)
    {
        // Tìm nhóm thiết bị đã bị xóa mềm bằng code
        $group = Equipment_types::withTrashed()->where('code', $code)->firstOrFail();

        // Khôi phục
        $group->restore();

        return redirect()->route('equipments.equipments_group_trash')->with('success', 'Nhóm thiết bị đã được khôi phục thành công!');
    }

    public function delete_permanently_group($code)
    {
        // Tìm nhóm thiết bị đã bị xóa mềm
        $group = Equipment_types::withTrashed()->where('code', $code)->firstOrFail();

        // Xóa vĩnh viễn
        $group->forceDelete();

        return redirect()->route('equipments.equipments_group_trash')->with('success', 'Nhóm thiết bị đã được xóa vĩnh viễn!');
    }

    function generateRandomString($length = 9)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    private function checkRelatedTables($equipmentCodes)
    {
        $result = [];

        $ivr = Inventories::whereIn('equipment_code', $equipmentCodes)->pluck('equipment_code')->toArray();

        $ivrc = Inventory_check_details::whereIn('equipment_code', $equipmentCodes)->pluck('equipment_code')->toArray();

        $rd = Receipt_details::whereIn('equipment_code', $equipmentCodes)->pluck('equipment_code')->toArray();

        $ed = Export_details::whereIn('equipment_code', $equipmentCodes)->pluck('equipment_code')->toArray();

        $ierd = Import_equipment_request_details::whereIn('equipment_code', $equipmentCodes)->pluck('equipment_code')->toArray();

        $eerd = Export_equipment_request_details::whereIn('equipment_code', $equipmentCodes)->pluck('equipment_code')->toArray();

        $result = array_unique(array_merge(
            $ivr,
            $ivrc,
            $rd,
            $ed,
            $ierd,
            $eerd,
        ));

        return $result;
    }
}
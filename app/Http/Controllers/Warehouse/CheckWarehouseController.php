<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Equipments;
use App\Models\Inventories;
use App\Models\Inventory_check_details;
use App\Models\Inventory_checks;
use App\Models\Notifications;
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
            $query->select('equipment_code', 'current_quantity', 'batch_number');
        }])->get();


        return view("{$this->route}.form", compact('title', 'action', 'equipmentsWithStock'));
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

        $inventoryCheckData['code'] = "KK" . $this->generateRandomString();

        $inventoryCheck = Inventory_checks::create($inventoryCheckData);

        if (!$inventoryCheck) {
            toastr()->error('Lỗi khi lưu phiếu kiểm kho.');
            return redirect()->back();
        }

        $inventoryCheckCode = $inventoryCheck->code;

        $inventoryCheckDetailData = [];

        foreach ($materialData as $material) {
            $inventoryCheckDetailData[] = [
                'inventory_check_code' => $inventoryCheckCode,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'current_quantity' => $material['current_quantity'],
                'actual_quantity' => $material['actual_quantity'],
                'unequal' => $material['unequal'],
            ];
        }

        try {
            Inventory_check_details::insert($inventoryCheckDetailData);
        } catch (\Exception $e) {
            toastr()->error('Lỗi khi lưu chi tiết phiếu kiểm kho: ' . $e->getMessage());

            return redirect()->back();
        }

        if ($materialData[0]['status'] == 1) {

            foreach ($materialData as $material) {
                $this->updateInventoryByCheck($material);
            }

            toastr()->success('Đã lưu và cập nhật kho thành công với mã ' . $inventoryCheckCode);

            $this->createNotificationAfterUpdateInventory($inventoryCheckCode, $materialData[0]['created_by']);
        } else {
            toastr()->warning('Phiếu kiểm kho tạm đã được lưu với mã ' . $inventoryCheckCode);
        }

        return redirect()->route('check_warehouse.index');
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
                'import_code' => null,
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

    public function approveCheck($code)
    {
        $inventoryCheck = Inventory_checks::where('code', $code)->first();

        if ($inventoryCheck && $inventoryCheck->status == 0) {
            $inventoryCheck->status = 1;
            $inventoryCheck->check_date = now();
            $inventoryCheck->save();

            $inventoryCheckDetails = Inventory_check_details::where('inventory_check_code', $code)->get();

            foreach ($inventoryCheckDetails as $detail) {
                $material = [
                    'equipment_code' => $detail->equipment_code,
                    'batch_number' => $detail->batch_number,
                    'actual_quantity' => $detail->actual_quantity,
                    'unequal' => $detail->unequal
                ];

                $this->updateInventoryByCheck($material);
            }

            // Gọi hàm tạo thông báo sau khi duyệt phiếu
            $this->createNotificationAfterUpdateInventory($inventoryCheck->code, $inventoryCheck->user_code);

            toastr()->success('Đã duyệt phiếu kiểm kho thành công với mã ' . $inventoryCheck->code);
            return redirect()->back();
        }

        toastr()->success('Phiếu kiểm kho đã được duyệt trước đó.');
        return redirect()->back();
    }

    public function cancelCheck($code)
    {
        $inventoryCheck = Inventory_checks::where('code', $code)->first();
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        if ($inventoryCheck && $inventoryCheck->status == 1) {
            $checkDate = Carbon::parse($inventoryCheck->check_date)->setTimezone('Asia/Ho_Chi_Minh');

            $daysPassed = $checkDate->diffInDays($now);

            if ($daysPassed > 1) {
                toastr()->error('Không thể hủy phiếu kiểm kho vì đã quá thời gian cho phép (1 ngày).');
                return redirect()->back();
            }

            // Thực hiện cập nhật số lượng tồn kho
            $inventoryCheckDetails = Inventory_check_details::where('inventory_check_code', $code)->get();

            foreach ($inventoryCheckDetails as $detail) {
                $inventory = Inventories::where('equipment_code', $detail->equipment_code)
                    ->where('batch_number', $detail->batch_number)
                    ->first();

                if ($inventory) {
                    $inventory->current_quantity = $detail->current_quantity;
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
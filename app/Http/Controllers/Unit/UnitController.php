<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\CreateUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Models\Equipments;
use Illuminate\Http\Request;
use App\Models\Units;
use App\Models\Users;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Đơn Vị';

        $allUnit = Units::with(['users'])->whereNull('deleted_at');

        if ($request->kw) {

            $allUnit->where('name', 'LIKE', '%' . $request->kw . '%')
                ->orWhere('code', '%' . $request->kw . '%')
                ->orWhere('description', 'LIKE', '%' . $request->kw . '%');
        }

        if ($request->us) {

            $allUnit->where('created_by', $request->us);
        }

        if (!empty($request->unit_codes)) {

            $checkExists = Equipments::whereIn('unit_code', $request->unit_codes)
                ->pluck('unit_code')
                ->toArray();

            $nonExistingUnit = array_diff($request->unit_codes, $checkExists);

            if ($nonExistingUnit) {

                Units::whereIn('code', $nonExistingUnit)->delete();

                toastr()->success('Đã xóa đơn vị không có dữ liệu liên quan đến trang thiết bị');

                return redirect()->route('units.index');
            }

            toastr()->error('Đơn vị không thể xóa vì đã có dữ liệu liên quan đến trang thiết bị');

            return redirect()->back();
        }

        $allUnits = $allUnit->paginate(10);

        $allUser = Users::orderBy('first_name', 'DESC')->get();

        return view('units.index', compact('title', 'allUnits', 'allUser'));
    }

    public function unit_trash(Request $request)
    {
        $title = 'Đơn Vị';

        $allUnitTrash = Units::onlyTrashed()->orderBy('deleted_at', 'DESC');

        if (!empty($request->restore_trash)) {

            Units::where('code', $request->restore_trash)->restore();

            toastr()->success('Đã khôi phục đơn vị');

            return redirect()->back();
        }

        if (!empty($request->delete_trash)) {

            Units::where('code', $request->delete_trash)->forceDelete();

            toastr()->success('Đã xóa vĩnh viễn đơn vị');

            return redirect()->back();
        }

        if (!empty($request->unit_codes)) {

            if ($request->action_type === 'restore') {

                Units::whereIn('code', $request->unit_codes)->restore();

                toastr()->success('Đã khôi phục đơn vị');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                Units::whereIn('code', $request->unit_codes)->forceDelete();

                toastr()->success('Đã xóa vĩnh viễn đơn vị');

                return redirect()->back();
            }
        }

        $allUnitTrash = $allUnitTrash->paginate(10);

        return view('units.trash', compact('title', 'allUnitTrash'));
    }

    public function create()
    {
        $title = 'Đơn Vị';

        $action = 'create';

        return view('units.form', compact('title', 'action'));
    }

    public function store(CreateUnitRequest $request)
    {
        $data = $request->validated();

        Units::create([
            'code' => 'DVT' . $this->generateRandomString(7),
            'name' => $data['name'],
            'description' => $request->description,
            'created_by' => session('user_code'),
        ]);

        toastr()->success('Đã thêm đơn vị');

        return redirect()->route('units.index');
    }

    public function edit($code)
    {
        $title = 'Đơn Vị';

        $unit = Units::find($code);

        $action = 'edit';

        return view('units.form', compact('title', 'unit', 'action'));
    }

    public function update(UpdateUnitRequest $request, $code)
    {
        $data = $request->validated();

        $record = Units::find($code);

        $record->update([
            'name' => $data['name'],
            'description' => $request->description,
        ]);

        toastr()->success('Đã cập nhật đơn vị');

        return redirect()->route('units.index');
    }

    public function destroy($code)
    {
        $unit = Equipments::where('unit_code', $code);

        if ($unit->count() > 0) {

            toastr()->error('Không thể xóa đơn vị này vì có dữ liệu liên quan đến trang thiết bị');

            return redirect()->route('units.index');
        }

        Units::find($code)->delete();

        toastr()->success('Đơn vị đã được xóa thành công');

        return redirect()->route('units.index');
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

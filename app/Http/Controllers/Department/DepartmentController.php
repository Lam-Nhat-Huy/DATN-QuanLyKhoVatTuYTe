<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Departments;
use App\Models\Export_equipment_requests;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $title = 'Phòng Ban';

    protected $route = 'department';

    protected $departmentModal;

    public function __construct()
    {
        $this->departmentModal = new Departments();
    }
    public function index(Request $request)
    {
        $title = $this->title;

        $allDepartment = $this->departmentModal::orderBy('created_at', 'DESC')->whereNull('deleted_at');

        if (isset($request->kw)) {
            $allDepartment = $allDepartment->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->kw . '%')
                    ->orWhere('location', 'like', '%' . $request->kw . '%')
                    ->orWhere('description', 'like', '%' . $request->kw . '%');
            });
        }

        $department = $allDepartment->paginate(10);

        if (isset($request->department_codes)) {

            $existingDepartments = Export_equipment_requests::whereIn('department_code', $request->department_codes)
                ->pluck('department_code')
                ->toArray();

            $nonExistingDepartments = array_diff($request->department_codes, $existingDepartments);

            if (!empty($nonExistingDepartments)) {

                $this->departmentModal::whereIn('code', $nonExistingDepartments)->delete();

                toastr()->success('Đã xóa phòng ban không tồn tại trong giao dịch của hệ thống.');

                return redirect()->back();
            }

            toastr()->error('Không thể xóa phòng ban vì đã tồn tại trong giao dịch của hệ thống.');

            return redirect()->back();
        }

        if (isset($request->department_code_delete)) {

            $existsDepartment = Export_equipment_requests::where('department_code', $request->department_code_delete)->first();

            if ($existsDepartment) {

                toastr()->error('Phòng ban này tồn tại giao dịch trong hệ thống, không thể xóa');

                return redirect()->back();
            }

            $this->departmentModal::where('code', $request->department_code_delete)->delete();

            toastr()->success('Xóa thành công');

            return redirect()->back();
        }

        return view("{$this->route}.list", compact("department", 'title'));
    }
    public function trash(Request $request)
    {
        $title = 'Phòng Ban';

        if (isset($request->department_codes)) {
            if ($request->action_type === 'restore') {
                $this->departmentModal::whereIn('code', $request->department_codes)->restore();
                toastr()->success('Khôi phục thành công');
            } elseif ($request->action_type === 'delete') {
                $this->departmentModal::whereIn('code', $request->department_codes)->forceDelete();
                toastr()->success('Xóa vĩnh viễn thành công');
            }
            return redirect()->back();
        }

        if (isset($request->department_code_restore)) {
            $this->departmentModal::where('code', $request->department_code_restore)->restore();
            toastr()->success('Khôi phục thành công');
            return redirect()->back();
        }

        if (isset($request->department_code_delete)) {
            $this->departmentModal::where('code', $request->department_code_delete)->forceDelete();
            toastr()->success('Xóa vĩnh viễn thành công');
            return redirect()->back();
        }

        $allDepartmentTrash = $this->departmentModal::onlyTrashed()->orderBy('deleted_at', 'DESC')->paginate(10);

        return view("{$this->route}.trash", compact('title', 'allDepartmentTrash'));
    }

    public function add()
    {
        $title = 'Phòng Ban';

        $title_form = 'Thêm Phòng Ban';

        $config = 'create';

        return view("{$this->route}.form", compact('title', 'title_form', 'config'));
    }

    public function create(CreateDepartmentRequest $request)
    {
        $data = $request->validated();

        $data['code'] = 'DEP' . $this->generateRandomString(9);

        $data['name'] = $request->name;

        $data['description'] = $request->description;

        $data['location'] = $request->location;

        $data['created_at'] = now();

        $data['updated_at'] = null;

        $this->departmentModal::create($data);

        toastr()->success('Thêm thành công');

        return redirect()->route('department.index');
    }

    public function edit(Request $request, $code)
    {
        $firstDepartment = $this->departmentModal::where('code', $code)->first();
        if (!$firstDepartment) {
            toastr()->error('Không tìm thấy Phong Ban với mã ' . $code);
            return redirect()->route('department.index');
        }

        session()->put('department_code', $firstDepartment->code);

        $title = 'Phong Ban';

        $title_form = "Sửa Phong Ban \"{$firstDepartment->name}\"";

        $config = 'edit';

        $display_none = 'display_none';

        return view("{$this->route}.form", compact('title', 'title_form', 'config', 'display_none', 'firstDepartment'));
    }

    public function update(UpdateDepartmentRequest $request)
    {
        $data = $request->validated();

        $data['name'] = $request->name;

        $data['description'] = $request->description;

        $data['location'] = $request->location;

        $data['updated_at'] = now();

        $record = $this->departmentModal::where('code', session('department_code'));
        if ($record) {
            $record->update($data);
        }

        $nameDepartment = $this->departmentModal::where('code', session('department_code'))->first();

        $nameDepartment = $nameDepartment->name;

        toastr()->success('Cập nhật Phong Ban ' . $nameDepartment . ' thành công');

        session()->forget(['department_code']);

        return redirect()->route('department.index');
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

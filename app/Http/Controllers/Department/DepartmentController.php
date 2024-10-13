<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Departments;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $title = 'Phòng Ban';
    protected $route = 'department';
    protected $DepartmentModel = 'Department';
    public function __construct()
    {
        $this->DepartmentModel = new Departments();
    }
    public function index(Request $request)
{
    $title = $this->title;

    // Khởi tạo truy vấn cơ sở dữ liệu
    $allDepartment = $this->DepartmentModel::orderBy('created_at', 'DESC')->whereNull('deleted_at');

    // Áp dụng các điều kiện lọc
    if ($request->filled('name')) {
        $allDepartment = $allDepartment->where("name", $request->name);
    }

    if ($request->filled('location')) {
        $allDepartment = $allDepartment->where("location", $request->location);
    }

    if ($request->filled('keyword')) {
        $allDepartment = $allDepartment->where(function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('location', 'like', '%' . $request->keyword . '%');
        });
    }

    // Phân trang và lấy danh sách phòng ban
    $department = $allDepartment->paginate(10);

    return view("{$this->route}.list", compact("department", 'title'));
}
public function trash(Request $request)
    {
        $title = 'Nhà Cung Cấp';

        if (isset($request->department_code)) {
            if ($request->action_type === 'restore') {
                $this->DepartmentModel::whereIn('code', $request->department_code)->restore();
                toastr()->success('Khôi phục thành công');
            } elseif ($request->action_type === 'delete') {
                $this->DepartmentModel::whereIn('code', $request->department_code)->forceDelete();
                toastr()->success('Xóa vĩnh viễn thành công');
            }
            return redirect()->back();
        }

        if (isset($request->department_code_restore)) {
            $this->DepartmentModel::where('code', $request->department_code_restore)->restore();
            toastr()->success('Khôi phục thành công');
            return redirect()->back();
        }

        if (isset($request->department_code_delete)) {
            $this->DepartmentModel::where('code', $request->department_code_delete)->forceDelete();
            toastr()->success('Xóa vĩnh viễn thành công');
            return redirect()->back();
        }

        $allDepartmentTrash = $this->DepartmentModel::onlyTrashed()->orderBy('deleted_at', 'DESC')->paginate(10);

        return view("{$this->route}.trash", compact('title', 'allDepartmentTrash'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function add()
    {
        $title = 'Phong Ban';

        $title_form = 'Thêm Phong Ban';

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

        $this->DepartmentModel::create($data);

        toastr()->success('Thêm thành công');

        return redirect()->route('department.index');
    }
    public function edit(Request $request, $code)
    {
        $firstDepartment = $this->DepartmentModel::where('code', $code)->first();
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

        $record = $this->DepartmentModel::where('code', session('department_code'));
        if ($record) {
            $record->update($data);
        }

        $nameDepartment = $this->DepartmentModel::where('code', session('department_code'))->first();

        $nameDepartment = $nameDepartment->name;

        toastr()->success('Cập nhật Phong Ban ' . $nameDepartment . ' thành công');

        session()->forget(['department_code']);

        return redirect()->route('department.index');
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
}

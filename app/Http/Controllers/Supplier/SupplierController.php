<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\CreateSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Import_equipment_requests;
use App\Models\Suppliers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $route = 'supplier';

    protected $SupplierModel = 'supplier';

    public function __construct()
    {
        $this->SupplierModel = new Suppliers();
    }

    public function index(Request $request)
    {
        $title = 'Nhà Cung Cấp';

        if ($request->has('supplier_codes')) {
            // Lấy danh sách các mã nhà cung cấp trùng với supplier_code trong Import_equipment_requests
            $existingSuppliers = Import_equipment_requests::whereIn('supplier_code', $request->supplier_codes)
                ->pluck('supplier_code')
                ->toArray();

            // Tìm mã nhà cung cấp trong $request->supplier_codes không có trong danh sách $existingSuppliers
            $nonExistingSuppliers = array_diff($request->supplier_codes, $existingSuppliers);

            // Nếu có mã nhà cung cấp không tồn tại trong Import_equipment_requests, thì xóa chúng
            if (!empty($nonExistingSuppliers)) {
                // Xóa các nhà cung cấp không trùng
                $this->SupplierModel::whereIn('code', $nonExistingSuppliers)->delete();

                toastr()->success('Đã xóa nhà cung cấp không tồn tại trong giao dịch của hệ thống.');

                return redirect()->back();
            }

            toastr()->error('Không thể xóa, nhà cung cấp này đã tồn tại trong giao dịch của hệ thống');

            return redirect()->back();
        }

        if ($request->has('supplier_code_delete')) {

            $checkExists = Import_equipment_requests::where('supplier_code', $request->supplier_code_delete)->first();

            if ($checkExists) {

                toastr()->error('Không thể xóa, nhà cung cấp này đã tồn tại trong giao dịch của hệ thống');

                return redirect()->back();
            }

            $this->SupplierModel::where('code', $request->supplier_code_delete)->delete();

            toastr()->success('Xóa nhà cung cấp thành công');

            return redirect()->back();
        }

        $allSupplier = $this->SupplierModel::orderBy('created_at', 'DESC')
            ->whereNull('deleted_at');

        if (isset($request->name)) {
            $allSupplier = $allSupplier->where("name", $request->name);
        }

        if (isset($request->contact_name)) {
            $allSupplier = $allSupplier->where("contact_name", $request->contact_name);
        }

        if (isset($request->tax_code)) {
            $allSupplier = $allSupplier->where("tax_code", $request->tax_code);
        }

        if (isset($request->email)) {
            $allSupplier = $allSupplier->where("email", $request->email);
        }

        if (isset($request->phone)) {
            $allSupplier = $allSupplier->where("phone", $request->phone);
        }

        if (isset($request->address)) {
            $allSupplier = $allSupplier->where("address", $request->address);
        }

        if (isset($request->keyword)) {
            $allSupplier = $allSupplier->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('contact_name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('tax_code', 'like', '%' . $request->keyword . '%')
                    ->orWhere('email', 'like', '%' . $request->keyword . '%')
                    ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                    ->orWhere('address', 'like', '%' . $request->keyword . '%');
            });
        }

        $allSupplier = $allSupplier->paginate(10);

        return view("{$this->route}.list", compact('title', 'allSupplier'));
    }


    public function trash(Request $request)
    {
        $title = 'Nhà Cung Cấp';

        if (isset($request->supplier_codes)) {
            if ($request->action_type === 'restore') {
                $this->SupplierModel::whereIn('code', $request->supplier_codes)->restore();
                toastr()->success('Khôi phục thành công');
            } elseif ($request->action_type === 'delete') {
                $this->SupplierModel::whereIn('code', $request->supplier_codes)->forceDelete();
                toastr()->success('Xóa vĩnh viễn thành công');
            }
            return redirect()->back();
        }

        if (isset($request->supplier_code_restore)) {
            $this->SupplierModel::where('code', $request->supplier_code_restore)->restore();
            toastr()->success('Khôi phục thành công');
            return redirect()->back();
        }

        if (isset($request->supplier_code_delete)) {
            $this->SupplierModel::where('code', $request->supplier_code_delete)->forceDelete();
            toastr()->success('Xóa vĩnh viễn thành công');
            return redirect()->back();
        }

        $allSupplierTrash = $this->SupplierModel::onlyTrashed()->orderBy('deleted_at', 'DESC')->paginate(10);

        return view("{$this->route}.trash", compact('title', 'allSupplierTrash'));
    }

    public function add()
    {
        $title = 'Nhà Cung Cấp';

        $title_form = 'Thêm Nhà Cung Cấp';

        $config = 'create';

        return view("{$this->route}.form", compact('title', 'title_form', 'config'));
    }
    public function create(CreateSupplierRequest $request)
    {
        $data = $request->validated();

        $data['code'] = 'SP' . $this->generateRandomString(9);

        $data['name'] = $request->name;

        $data['contact_name'] = $request->contact_name;

        $data['tax_code'] = $request->tax_code;

        $data['email'] = $request->email;

        $data['phone'] = $request->phone;

        $data['address'] = $request->address;

        $data['created_at'] = now();

        $data['updated_at'] = null;

        $this->SupplierModel::create($data);

        toastr()->success('Thêm thành công');

        return redirect()->route('supplier.list');
    }
    public function edit(Request $request, $code)
    {
        $firstSupplier = $this->SupplierModel::where('code', $code)->first();
        if (!$firstSupplier) {
            toastr()->error('Không tìm thấy nhà cung cấp với mã ' . $code);
            return redirect()->route('supplier.list');
        }
        session()->put('supplier_code', $firstSupplier->code);

        $title = 'Nhà Cung Cấp';

        $title_form = "Sửa Nhà Cung Cấp \"{$firstSupplier->name}\"";

        $config = 'edit';

        $display_none = 'display_none';

        return view("{$this->route}.form", compact('title', 'title_form', 'config', 'display_none', 'firstSupplier'));
    }

    public function update(UpdateSupplierRequest $request)
    {
        $data = $request->validated();

        $data['name'] = $request->name;

        $data['contact_name'] = $request->contact_name;

        $data['tax_code'] = $request->tax_code;

        $data['email'] = $request->email;

        $data['phone'] = $request->phone;

        $data['address'] = $request->address;

        $data['updated_at'] = now();

        $record = $this->SupplierModel::where('code', session('supplier_code'));
        if ($record) {
            $record->update($data);
        }

        $nameSupplier = $this->SupplierModel::where('code', session('supplier_code'))->first();

        $nameSupplier = $nameSupplier->name;

        toastr()->success('Cập nhật nhà cung cấp ' . $nameSupplier . ' thành công');

        session()->forget(['supplier_code']);

        return redirect()->route('supplier.list');
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
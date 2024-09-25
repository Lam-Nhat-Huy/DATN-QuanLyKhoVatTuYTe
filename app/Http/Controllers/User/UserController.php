<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $route = 'user';

    protected $callModel = 'user';

    public function __construct()
    {
        $this->callModel = new Users();
    }

    public function index(Request $request)
    {
        $title = 'Người Dùng';

        if (isset($request->user_codes)) {

            if (in_array(session('user_code'), $request->user_codes)) {

                toastr()->error('Bạn không thể tự xóa chính mình');

                return redirect()->back();
            } else {

                $this->callModel::whereIn('code', $request->user_codes)->delete();

                toastr()->success('Xóa người dùng đã chọn thành công');

                return redirect()->back();
            }
        }

        if (isset($request->user_code_delete)) {

            if (session('user_code') == $request->user_code_delete) {

                toastr()->error('Bạn không thể tự xóa chính mình');

                return redirect()->back();
            } else {

                $this->callModel::where('code', $request->user_code_delete)->delete();

                toastr()->success('Xóa người dùng thành công');

                return redirect()->back();
            }
        }

        $allUser = $this->callModel::orderBy('created_at', 'DESC')
            ->where('deleted_at', null);

        if (isset($request->gd)) {
            $allUser = $allUser->where("gender", $request->gd);
        }

        if (isset($request->ps)) {
            $allUser = $allUser->where("isAdmin", $request->ps);
        }

        if (isset($request->st)) {
            $allUser = $allUser->where("status", $request->st);
        }

        if (isset($request->kw)) {
            $allUser = $allUser->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->kw . '%')
                    ->orWhere('email', 'like', '%' . $request->kw . '%')
                    ->orWhere('phone', 'like', '%' . $request->kw . '%')
                    ->orWhere('code', 'like', '%' . $request->kw . '%');
            });
        }

        $allUser = $allUser->paginate(10);

        return view("{$this->route}.index", compact('title', 'allUser'));
    }

    public function user_trash(Request $request)
    {
        $title = 'Người Dùng';

        if (isset($request->user_codes)) {

            if ($request->action_type === 'restore') {

                $this->callModel::whereIn('code', $request->user_codes)->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $this->callModel::whereIn('code', $request->user_codes)->forceDelete();

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (isset($request->user_code_restore)) {

            $this->callModel::where('code', $request->user_code_restore)->restore();

            toastr()->success('Khôi phục thành công');

            return redirect()->back();
        }

        if (isset($request->user_code_delete)) {

            $this->callModel::where('code', $request->user_code_delete)->forceDelete();

            toastr()->success('Xóa vĩnh viễn thành công');

            return redirect()->back();
        }

        $allUserTrash = $this->callModel::orderBy('deleted_at', 'DESC')
            ->onlyTrashed()
            ->paginate(10);

        return view("{$this->route}.user_trash", compact('title', 'allUserTrash'));
    }

    public function add()
    {
        $title = 'Người Dùng';

        $title_form = 'Thêm Người Dùng';

        $action = 'create';

        return view("{$this->route}.form", compact('title', 'title_form', 'action'));
    }

    public function create(CreateUserRequest $request)
    {
        $data = $request->validated();

        $data['code'] = 'U' . $this->generateRandomString(9);

        $data['password'] = Hash::make($data['password']);

        $data['position'] = $request->isAdmin == 1 ? 'Admin' : 'Nhân Viên';

        $data['isAdmin'] = $request->isAdmin == 1 ? 1 : 0;

        $data['status'] = $request->status == 1 ? 1 : 0;

        $data['created_at'] = now();

        $data['updated_at'] = null;

        if ($request->file('avatar')) {

            $data['avatar'] = $request->file('avatar')->store('uploads', 'public');
        }

        $this->callModel::create($data);

        toastr()->success('Thêm thành công');

        return redirect()->route('user.index');
    }

    public function edit(Request $request, $code)
    {
        $firstUser = $this->callModel::where('code', $code)->first();

        session()->put('user_code_request', $firstUser->code);

        $title = 'Người Dùng';

        $title_form = "Cập Nhật Người Dùng \"{$firstUser->last_name} {$firstUser->first_name}\"";

        $action = 'edit';

        $display_none = 'display_none';

        return view("{$this->route}.form", compact('title', 'title_form', 'action', 'display_none', 'firstUser'));
    }

    public function update(UpdateUserRequest $request)
    {
        $data = $request->validated();

        if (!empty($request->password)) {

            $data['password'] = Hash::make($data['password']);
        } else {

            unset($data['password']);
        }

        if (!empty($request->file('avatar'))) {

            $data['avatar'] = $request->file('avatar')->store('uploads', 'public');
        }

        $data['position'] = $request->isAdmin == 1 ? 'Admin' : 'Nhân Viên';

        $data['isAdmin'] = $request->isAdmin == 1 ? 1 : 0;

        $data['status'] = $request->status == 1 ? 1 : 0;

        $data['updated_at'] = now();

        $record = $this->callModel::where('code', session('user_code_request'));

        if ($record) {

            $record->update($data);
        }

        $nameUser = $this->callModel::where('code', session('user_code_request'))->first();

        $nameUser = $nameUser->last_name . ' ' . $nameUser->first_name;

        toastr()->success("Cập nhật người dùng " . $nameUser . " thành công");

        session()->forget(['user_code_request']);

        return redirect()->route('user.index');
    }

    function generateRandomString($length = 9)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

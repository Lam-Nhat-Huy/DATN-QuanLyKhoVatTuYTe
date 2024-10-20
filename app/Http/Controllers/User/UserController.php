<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Import_equipment_requests;
use App\Models\Inventory_checks;
use App\Models\Notifications;
use App\Models\Receipts;
use App\Models\Reports;
use App\Models\Units;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $route = 'user';

    protected $callModel;

    public function __construct()
    {
        $this->callModel = new Users();
    }

    public function index(Request $request)
    {
        $title = 'Người Dùng';

        if (isset($request->user_codes)) {

            $checkDeleteMe = Users::whereIn('code', $request->user_codes)
                ->pluck('code')
                ->toArray();

            $nonExistingUsers = array_diff($checkDeleteMe, [session('user_code')]);

            if (!empty($nonExistingUsers)) {

                $usersWithRelatedData = $this->checkRelatedTables($nonExistingUsers);

                $nonExistingUsers_2 = array_diff($nonExistingUsers, $usersWithRelatedData);

                Users::whereIn('code', $nonExistingUsers_2)->delete();

                toastr()->success('Đã xóa người dùng');

                return redirect()->back();
            }
        }

        if (isset($request->user_code_delete)) {

            if (session('user_code') == $request->user_code_delete) {

                toastr()->error('Không thể tự xóa chính mình');

                return redirect()->back();
            } else {
                $usersWithRelatedData = $this->checkRelatedTables([$request->user_code_delete]);

                if ($usersWithRelatedData) {

                    toastr()->error('Không thể xóa người dùng này');

                    return redirect()->back();
                }

                $rs = $this->callModel::where('code', $request->user_code_delete)->delete();

                if ($rs) {

                    toastr()->success('Xóa người dùng thành công');

                    return redirect()->back();
                }

                toastr()->success('Đã xảy ra lỗi, hãy thử lại');

                return redirect()->back();
            }
        }

        $allUser = $this->callModel::orderBy('created_at', 'DESC')
            ->whereNull('deleted_at');

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
                    ->orWhere('last_name', 'like', '%' . $request->kw . '%')
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

                $rs = $this->callModel::whereIn('code', $request->user_codes)->restore();

                if ($rs) {

                    toastr()->success('Khôi phục thành công');

                    return redirect()->back();
                }

                toastr()->success('Đã xảy ra lỗi, hãy thử lại');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $users = $this->callModel::onlyTrashed()->whereIn('code', $request->user_codes)->get();

                foreach ($users as $user) {

                    if ($user->avatar) {

                        Storage::disk('public')->delete($user->avatar);
                    }

                    $user->forceDelete();
                }

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (isset($request->user_code_restore)) {

            $rs = $this->callModel::where('code', $request->user_code_restore)->restore();

            if ($rs) {

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            }

            toastr()->success('Đã xảy ra lỗi, hãy thử lại');

            return redirect()->back();
        }

        if (isset($request->user_code_delete)) {

            $user = $this->callModel::onlyTrashed()->where('code', $request->user_code_delete)->first();

            if ($user->avatar) {

                Storage::disk('public')->delete($user->avatar);
            }

            $user->forceDelete();

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

        // $data['position'] = $request->isAdmin == 1 ? 'Admin' : 'Nhân Viên';

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

        if (!$firstUser) {

            toastr()->error('Đã xảy ra lỗi, hãy thử lại');

            return redirect()->back();
        }

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

        $record = $this->callModel::where('code', session('user_code_request'));

        if (!empty($request->password)) {

            $data['password'] = Hash::make($data['password']);
        } else {

            unset($data['password']);
        }

        if (!empty($request->file('avatar'))) {

            $user = $record->first();

            if ($user->avatar) {

                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('uploads', 'public');
        }

        // $data['position'] = $request->isAdmin == 1 ? 'Admin' : 'Nhân Viên';

        $data['isAdmin'] = $request->isAdmin == 1 ? 1 : 0;

        $data['status'] = $request->status == 1 ? 1 : 0;

        $data['updated_at'] = now();

        $rs = $record->update($data);

        if ($rs) {

            $nameUser = $this->callModel::where('code', session('user_code_request'))->first();

            if ($nameUser->code == session('user_code')) {
                session()->put('avatar', $nameUser->avatar);
            }

            $nameUser = $nameUser->last_name . ' ' . $nameUser->first_name;

            toastr()->success("Cập nhật người dùng " . $nameUser . " thành công");

            session()->forget(['user_code_request']);

            return redirect()->route('user.index');
        }

        toastr()->error('Không thể cập nhật, thử lại sau');

        return redirect()->route('user.index');
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

    private function checkRelatedTables($userCodes)
    {
        $usersWithRelatedData = [];

        $reportUsers = Reports::whereIn('user_code', $userCodes)->pluck('user_code')->toArray();

        $notificationUsers = Notifications::whereIn('user_code', $userCodes)->pluck('user_code')->toArray();

        $inventoryCheckUsers = Inventory_checks::whereIn('user_code', $userCodes)->pluck('user_code')->toArray();

        $importEquipmentRequestUsers = Import_equipment_requests::whereIn('user_code', $userCodes)->pluck('user_code')->toArray();

        $receiptUsers = Receipts::whereIn('created_by', $userCodes)->pluck('created_by')->toArray();

        // $exportUsers = Exports::whereIn('user_code', $userCodes)->pluck('user_code')->toArray();

        $unitUsers = Units::whereIn('created_by', $userCodes)->pluck('created_by')->toArray();

        $usersWithRelatedData = array_unique(array_merge(
            $reportUsers,
            $notificationUsers,
            $inventoryCheckUsers,
            $importEquipmentRequestUsers,
            $receiptUsers,
            // $exportUsers,
            $unitUsers
        ));

        return $usersWithRelatedData;
    }
}
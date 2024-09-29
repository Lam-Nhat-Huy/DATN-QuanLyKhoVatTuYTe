<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\CreateNotificationRequest;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use App\Models\Notification_types;
use App\Models\Notifications;
use App\Models\Users;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $route = 'notification';

    protected $callModel;

    public function __construct()
    {
        $this->callModel = new Notifications();
    }

    public function index(Request $request)
    {
        $title = 'Thông Báo';

        $AllNotificationType = Notification_types::all();

        $AllUser = Users::all();

        $AllNotification = $this->callModel::with(['notification_types', 'users'])
            ->orderBy('created_at', 'DESC')
            ->where('deleted_at', null);

        if (isset($request->ur)) {
            $AllNotification = $AllNotification->where("user_code", $request->ur);
        }

        if (isset($request->rt)) {
            $AllNotification = $AllNotification->where("notification_type", $request->rt);
        }

        if (isset($request->st)) {
            $AllNotification = $AllNotification->where("status", $request->st);
        }

        if (isset($request->kw)) {
            $AllNotification = $AllNotification->where(function ($query) use ($request) {
                $query->where('content', 'like', '%' . $request->kw . '%')
                    ->orWhere('code', 'like', '%' . $request->kw . '%');
            });
        }

        $AllNotification = $AllNotification->paginate(10);

        if (isset($request->notification_codes)) {

            if ($request->action_type === 'browse') {

                $this->callModel::whereIn('code', $request->notification_codes)->update(['status' => 1]);

                toastr()->success('Duyệt thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $this->callModel::whereIn('code', $request->notification_codes)->delete();

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (!empty($request->browse_notification)) {

            $this->callModel::where('code', $request->browse_notification)->update(['status' => 1]);

            toastr()->success('Đã duyệt');

            return redirect()->route('notification.index');
        }

        if (!empty($request->delete_notification)) {

            $this->callModel::where('code', $request->delete_notification)->delete();

            toastr()->success('Đã xóa');

            return redirect()->route('notification.index');
        }

        return view("{$this->route}.notification", compact('title', 'AllNotification', 'AllUser', 'AllNotificationType'));
    }

    public function notification_trash(Request $request)
    {
        $title = 'Thông Báo';

        $AllNotificationTrash = $this->callModel::with(['notification_types', 'users'])
            ->orderBy('deleted_at', 'DESC')
            ->onlyTrashed()
            ->paginate(10);

        if (isset($request->notification_codes)) {

            if ($request->action_type === 'restore') {

                $this->callModel::whereIn('code', $request->notification_codes)->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $this->callModel::withTrashed()->whereIn('code', $request->notification_codes)->forceDelete();

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (isset($request->restore_notification)) {

            $this->callModel::where('code', $request->restore_notification)->restore();

            toastr()->success('Khôi phục thành công');

            return redirect()->back();
        }

        if (isset($request->delete_notification)) {

            $notification = $this->callModel::withTrashed()->where('code', $request->delete_notification)->forceDelete();

            toastr()->success('Xóa vĩnh viễn thành công');

            return redirect()->back();
        }

        return view("{$this->route}.notification_trash", compact('title', 'AllNotificationTrash'));
    }

    public function notification_add()
    {
        $title = 'Thông Báo';

        $title_form = 'Thêm Thông Báo';

        $action = 'create';

        $allNotificationType = Notification_types::all();

        return view("{$this->route}.notification_form", compact('title', 'action', 'title_form', 'allNotificationType'));
    }

    public function notification_create(CreateNotificationRequest $request)
    {
        $data = $request->validated();

        if ($data) {

            $data['code'] = 'TB' . $this->generateRandomString(8);

            $data['user_code'] = session('user_code');

            $data['created_at'] = now();

            $data['updated_at'] = null;

            $this->callModel::create($data);

            toastr()->success('Đã thêm thông báo');

            return redirect()->route('notification.index');
        }
    }

    public function notification_edit($code)
    {
        $title = 'Thông Báo';

        $title_form = 'Cập Nhật Thông Báo';

        $action = 'edit';

        $allNotificationType = Notification_types::all();

        $firstNotification = $this->callModel::where('code', $code)->first();

        return view("{$this->route}.notification_form", compact('title', 'action', 'title_form', 'allNotificationType', 'firstNotification'));
    }

    public function notification_update(UpdateNotificationRequest $request, $code)
    {
        $data = $request->validated();

        if ($data) {

            $data['updated_at'] = now();

            $rs = $this->callModel::where('code', $code)->update($data);

            if ($rs) {

                toastr()->success('Đã cập nhật thông báo');

                return redirect()->route('notification.index');
            }

            toastr()->error('Không thể cập nhật, thử lại sau');

            return redirect()->route('notification.index');
        }
    }

    public function create_notification_type(Request $request)
    {
        if (!empty($request->notification_type_name)) {

            $rs = Notification_types::create([
                'name' => $request->notification_type_name,
            ]);

            if ($rs) {

                toastr()->success('Đã thêm loại thông báo');

                return redirect()->back();
            }

            toastr()->error('Xảy ra lỗi, thử lại sau');

            return redirect()->back();
        }
    }

    public function delete_notification_type($id)
    {
        if (!empty($id)) {

            $rs = Notification_types::find($id)->delete();

            if ($rs) {

                toastr()->success('Đã xóa loại thông báo');

                return redirect()->back();
            }

            toastr()->error('Xảy ra lỗi, thử lại sau');

            return redirect()->back();
        }
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

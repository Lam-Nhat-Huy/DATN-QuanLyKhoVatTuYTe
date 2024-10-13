<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Home\ForgotRequest;
use App\Http\Requests\Home\LoginRequest;
use App\Http\Requests\Home\ResetPasswordRequest;
use App\Mail\PasswordResetMail;
use App\Models\Notifications;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    protected $route = 'home';

    public function index()
    {
        return view("$this->route");
    }

    public function handleLogin(LoginRequest $request)
    {
        $request->validated();

        $userFind = Users::where('phone', $request->phone)
            ->where('status', 1)
            ->first();

        if ($userFind && Hash::check($request->password, $userFind->password)) {

            session()->put('user_code', $userFind->code);

            session()->put('fullname', $userFind->last_name . ' ' . $userFind->first_name);

            session()->put('email', $userFind->email);

            session()->put('avatar', $userFind->avatar);

            session()->put('address', $userFind->address);

            session()->put('dob', $userFind->birth_day);

            session()->put('isAdmin', $userFind->isAdmin);

            $importantNotification = Notifications::where('important', 1)
                ->where('status', 1)
                ->latest()
                ->first();

            toastr()->success('Đăng Nhập Thành Công');

            if ($importantNotification) {
                session()->flash('important_notification', $importantNotification->content);
            }

            return redirect()->route('system.index');
        } else {

            toastr()->error('Tài Khoản Không Tồn Tại Hoặc Bị Khóa');

            return back();
        }
    }

    public function logout()
    {
        session()->forget(['user_code', 'isAdmin']);

        toastr()->success('Đăng Xuất Thành Công');

        return redirect()->route('home');
    }

    public function showForgotForm()
    {
        return view("forgot/forgot");
    }
    public function processForgot(ForgotRequest $request)
    {
        $validated = $request->validated();

        $user = Users::where('phone', $validated['phone_forgot'])->first();

        if ($user) {
            Mail::to($user->email)->send(new PasswordResetMail());

            session()->put('phone', $user->phone);

            toastr()->success('Đã gửi đường dẫn đặt lại mật khẩu về email mà bạn đăng ký số điện thoại này.');

            return redirect()->route('home');
        } else {
            toastr()->error('Số điện thoại không tồn tại.');
            
            return redirect()->back();
        }
    }
    public function resetPassword()
    {
        if (empty(session('phone'))) {
            return abort(404);
        }

        return view("forgot/reset_password");
    }
    public function updatePassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $user = Users::where('phone', session('phone'))->first();

        if ($user) {

            if (!empty($data['new_password'])) {

                $data['password'] = Hash::make($data['new_password']);

                $user->update($data);

                toastr()->success('Mật khẩu đã được thay đổi');
            }

        } else {

            toastr()->error('Số điện thoại không tồn tại.');
        }

        session()->forget('phone');

        return redirect()->route('home');
    }
}

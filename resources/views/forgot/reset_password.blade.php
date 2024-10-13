<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Kho Y Tế Nhà Bee')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <link rel="stylesheet" href="{{ asset('css/fullcalendar.bundle.css') }}">

    <link rel="stylesheet" href="{{ asset('css/plugins.bundle.css') }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel="shortcut icon" href="{{ asset('image/logo_warehouse.png') }}" />
</head>

<body class="d-flex flex-column flex-root">
    <div
        class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed w-100">
        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
            <a href="{{ route('home') }}" class="mb-7">
                <img alt="Logo" src="{{ asset('image/logo_warehouse.png') }}" class="h-100px" />
            </a>
            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                <form method="POST" action="{{ route('home.reset_password') }}" class="form w-100"
                    novalidate="novalidate" id="kt_password_reset_form">
                    @csrf
                    <div class="text-center mb-10">
                        <h2 class="text-dark mb-3">Nhập mật khẩu mới</h2>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required form-label fw-bolder text-gray-900 fs-6">Mật khẩu mới</label>
                        <input class="form-control form-control-sm border border-success" type="password"
                            name="new_password" placeholder="Nhập mật khẩu mới..." />
                        @error('new_password')
                            <div class="message_error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required form-label fw-bolder text-gray-900 fs-6">Xác nhận mật khẩu mới</label>
                        <input class="form-control form-control-sm border border-success" type="password"
                            name="new_password_confirmation" placeholder="Xác nhận mật khẩu mới..." />
                        @error('new_password_confirmation')
                            <div class="message_error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex flex-wrap justify-content-center">
                        <button type="submit" class="btn btn-dark btn-sm fw-bolder load_animation">
                            <span>Thay đổi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="loading">
        <div aria-live="assertive" role="alert" class="loader"></div>
    </div>

    <div id="loading-overlay" class="loading-overlay"></div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>

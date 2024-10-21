<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'BeeSoft')</title>

    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <link rel="stylesheet" href="{{ asset('css/fullcalendar.bundle.css') }}">

    <link rel="stylesheet" href="{{ asset('css/plugins.bundle.css') }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel="shortcut icon" href="{{ asset('image/logo_warehouse.png') }}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">

    {{-- Link css riêng cho mỗi view blade --}}
    @yield('styles')

    <style>
        body,
        input,
        select,
        textarea,
        button {
            font-family: 'Noto Sans', sans-serif;
        }
    </style>
</head>

<body id="kt_body" data-is-admin="{{ session('isAdmin', false) ? 'true' : 'false' }}"
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
    style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">

    @if (
        $firstLockWarehouse == 1 &&
            (Route::currentRouteName() == 'warehouse.import' ||
                Route::currentRouteName() == 'warehouse.export' ||
                Route::currentRouteName() == 'warehouse.trash' ||
                Route::currentRouteName() == 'warehouse.create_import' ||
                Route::currentRouteName() == 'warehouse.create_export' ||
                Route::currentRouteName() == 'equipment_request.import' ||
                Route::currentRouteName() == 'equipment_request.export' ||
                Route::currentRouteName() == 'equipment_request.equipments_trash' ||
                Route::currentRouteName() == 'equipment_request.insert_equipments' ||
                Route::currentRouteName() == 'equipment_request.update_equipments'))
        {{ abort(404) }}
    @else
        <div class="d-flex flex-column flex-root">

            <div class="page d-flex flex-row flex-column-fluid">

                <navbar>
                    @include('master_layout.components.navbar')
                </navbar>

                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

                    <sidebar>
                        @include('master_layout.components.sidebar')
                    </sidebar>

                    <main>
                        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                            <div class="toolbar" id="kt_toolbar">
                                <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                                    <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                                        data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                                        class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                                            @yield('title', 'BeeSoft')
                                        </h1>
                                    </div>
                                </div>
                            </div>
                            <div class="post d-flex flex-column-fluid" id="kt_post">
                                <div id="kt_content_container" class="container-xxl">
                                    <div class="row gy-5 g-xl-8">
                                        <div class="col-xxl-12">
                                            @yield('content')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>

                    <footer>
                        @include('master_layout.components.footer')
                    </footer>

                    <notification>
                        @include('master_layout.components.notification_modal')
                    </notification>

                    <scrolltop>
                        @include('master_layout.components.scroll_top')
                    </scrolltop>

                </div>

            </div>

        </div>

        <div id="loading">
            <div aria-live="assertive" role="alert" class="loader"></div>
        </div>

        <div id="loading-overlay" class="loading-overlay"></div>

        <div class="chatbox" id="chatbox">
            <div class="chatbox-header">
                <h3 class="text-white">Nhắn Tin</h3>
                <button id="chatbox-close" style="font-size: 20px;">&times;</button>
            </div>
            <div class="chatbox-body">
                <div class="messages">
                    <div class="d-flex justify-content-start">
                        <div class="me-3">
                            <img class="border-dark border rounded-circle " width="35" height="35"
                                src="https://images.g2crowd.com/uploads/product/image/large_detail/large_detail_b541e326e0acd44b1ef931c92154c6b9/ai-chat.png"
                                alt="">
                        </div>
                        <div class="text-left">
                            <h6 class="mb-1">Chatbox</h6>
                            <span>Chào Bạn Nghe, Có Cặc Gì Mới Hong??</span>
                        </div>
                    </div>
                    <div class="mt-5 d-flex justify-content-end">
                        <div class="text-end">
                            <h6 class="mb-1">{{ session('fullname') }}</h6>
                            <span>Đéo</span>
                        </div>
                        <div class="ms-3">
                            <img class="border-dark border rounded-circle " width="35" height="35"
                                src="{{ !empty(session('avatar')) ? asset('storage/' . session('avatar')) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="chatbox-footer">
                <input type="text" id="chatbox-input" placeholder="Nhập tin nhắn...">
                <button id="chatbox-send">Gửi</button>
            </div>
        </div>

        <img class="open-chatbox-btn rounded-circle shadow" style="width: 50px; height: 50px;" id="open-chatbox-btn"
            src="https://images.g2crowd.com/uploads/product/image/large_detail/large_detail_b541e326e0acd44b1ef931c92154c6b9/ai-chat.png"
            alt="">

        <script>
            // Lấy các phần tử chatbox và nút mở chatbox
            const chatbox = document.getElementById('chatbox');
            const openChatboxBtn = document.getElementById('open-chatbox-btn');

            // Hiển thị chatbox khi nhấn vào nút và ẩn nút mở chatbox
            openChatboxBtn.addEventListener('click', function() {
                chatbox.style.display = 'block'; // Hiển thị chatbox
                openChatboxBtn.style.display = 'none'; // Ẩn nút mở chatbox
            });

            // Đóng chatbox khi nhấn vào nút close và hiển thị lại nút mở chatbox
            document.getElementById('chatbox-close').addEventListener('click', function() {
                chatbox.style.display = 'none'; // Ẩn chatbox
                openChatboxBtn.style.display = 'block'; // Hiển thị lại nút mở chatbox
            });

            // Gửi tin nhắn
            document.querySelector('#chatbox-send').addEventListener('click', function() {
                const input = document.querySelector('#chatbox-input');
                const message = input.value.trim();
                if (message) {
                    const messageElement = document.createElement('div');
                    messageElement.innerHTML = `
                        <div class="mt-5 d-flex justify-content-end">
                            <div class="text-end">
                                <h6 class="mb-1">{{ session('fullname') }}</h6>
                                <span>${message}</span>
                            </div>
                            <div class="ms-3">
                                <img class="border-dark border rounded-circle " width="35" height="35"
                                    src="{{ !empty(session('avatar')) ? asset('storage/' . session('avatar')) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                    alt="">
                            </div>
                        </div>
                    `;
                    document.querySelector('.messages').appendChild(messageElement);
                    input.value = ''; // Clear input field
                    document.querySelector('.chatbox-body').scrollTop = document.querySelector('.chatbox-body')
                        .scrollHeight;
                }
            });
        </script>
    @endif

    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}

    <script src="{{ asset('js/cancelVoice.js') }}"></script>

    <script src="{{ asset('js/main.js') }}"></script>

    <script src="{{ asset('js/plugins.bundle.js') }}"></script>

    <script src="{{ asset('js/scripts.bundle.js') }}"></script>

    <script src="{{ asset('js/fullcalender.bundle.js') }}"></script>

    <script src="{{ asset('js/widgets.js') }}"></script>

    <script src="{{ asset('js/chat.js') }}"></script>

    <script src="{{ asset('js/create-app.js') }}"></script>

    <script src="{{ asset('js/upgrade-plan.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('lib/library.js') }}"></script>

    {{-- Link js riêng cho mỗi view blade --}}
    @yield('scripts')
</body>

</html>

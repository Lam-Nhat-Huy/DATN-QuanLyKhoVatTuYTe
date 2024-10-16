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

<body id="kt_body"
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

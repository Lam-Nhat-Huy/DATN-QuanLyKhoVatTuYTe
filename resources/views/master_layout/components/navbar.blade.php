<style>
    /* Bell styling based on Uiverse.io */
    .bell {
        border: 2.17px solid white;
        border-radius: 10px 10px 0 0;
        width: 15px;
        height: 17px;
        background: transparent;
        display: block;
        position: relative;
        top: -3px;
    }

    .bell::before,
    .bell::after {
        content: "";
        background: white;
        display: block;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        height: 2.17px;
    }

    .bell::before {
        top: 100%;
        width: 20px;
    }

    .bell::after {
        top: calc(100% + 4px);
        width: 7px;
    }

    /* Container styling based on Uiverse.io */
    .notification {
        background: transparent;
        border: none;
        padding: 15px 15px;
        border-radius: 50px;
        cursor: pointer;
        transition: 300ms;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification::before {
        content: "";
        display: none;
    }

    .notification:hover {
        background: rgba(170, 170, 170, 0.062);
    }

    .notification:hover>.bell-container {
        animation: bell-animation 650ms ease-out 0s 1 normal both;
    }

    /* Bell animation */
    @keyframes bell-animation {
        20% {
            transform: rotate(15deg);
        }

        40% {
            transform: rotate(-15deg);
            scale: 1.1;
        }

        60% {
            transform: rotate(10deg);
            scale: 1.1;
        }

        80% {
            transform: rotate(-10deg);
        }

        0%,
        100% {
            transform: rotate(0deg);
        }
    }

    /* Badge styling */
    #notification-count {
        color: white;
        font-size: 10px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: red;
        position: absolute;
        right: 8px;
        top: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.3);
    }

    #kt_activities_toggle {
        transition: transform 0.2s ease-in-out;
    }

    #kt_activities_toggle:hover {
        transform: scale(1.1);
    }
</style>
<div id="kt_header" style="" class="header align-items-stretch">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                id="kt_aside_mobile_toggle">
                <span class="svg-icon svg-icon-2x mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                            fill="black" />
                        <path opacity="0.3"
                            d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                            fill="black" />
                    </svg>
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="{{ route('system.index') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset('image/logo_warehouse.png') }}" class="h-50px" />
            </a>
        </div>
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-stretch" id="kt_header_nav">
                <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu"
                    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end"
                    data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
                    data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                    <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                        id="#kt_header_menu" data-kt-menu="true">
                        @foreach (config('apps.module') as $value)
                            @foreach ($value as $key => $item)
                                <div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start"
                                    class="menu-item menu-lg-down-accordion me-lg-1">
                                    @if (in_array(session('isAdmin'), (array) $item['user_role']))
                                        <span
                                            class="menu-link py-3 {{ in_array(Route::currentRouteName(), (array) $item['route']) ? 'active_navbar' : '' }}">
                                            <span class="menu-title">{{ $item['title'] }}</span>
                                            <span class="menu-arrow d-lg-none"></span>
                                        </span>
                                        <div
                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg py-lg-4">
                                            @foreach ($item['subModule'] as $sub)
                                                @if (in_array(session('isAdmin'), (array) $sub['user_role']))
                                                    @if (Route::has($sub['route']))
                                                        @if ($firstLockWarehouse == 1 && ($sub['route'] == 'warehouse.import' || $sub['route'] == 'warehouse.export'))
                                                        @else
                                                            <div class="menu-item">
                                                                <a class="menu-link py-3 {{ in_array(Route::currentRouteName(), (array) $sub['route']) || in_array(Route::currentRouteName(), (array) $sub['route_action']) ? 'active' : '' }}"
                                                                    href="{{ route($sub['route']) }}">
                                                                    <i class="{{ $sub['icon'] }} me-2"></i>
                                                                    <span class="menu-title">{{ $sub['title'] }}</span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="menu-item">
                                                            <strong class="menu-link py-3">
                                                                <i class="fa fa-x me-2 text-danger"></i>
                                                                <s class="menu-title">Không Tìm Thấy Đường Dẫn</s>
                                                            </strong>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-stretch flex-shrink-0">
                <div class="d-flex align-items-stretch flex-shrink-0">
                    <div class="d-flex align-items-stretch ms-1 ms-lg-3">
                        <div id="kt_header_search" class="d-flex align-items-stretch" data-kt-search-keypress="true"
                            data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu"
                            data-kt-menu-trigger="auto" data-kt-menu-overflow="false" data-kt-menu-permanent="true"
                            data-kt-menu-placement="bottom-end">
                            <div class="d-flex align-items-center" data-kt-search-element="toggle"
                                id="kt_header_search_toggle">
                                <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px">
                                    <span class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                                rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="black" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div data-kt-search-element="content"
                                class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px">
                                <div data-kt-search-element="wrapper">
                                    <div class="" style="position: relative;">
                                        <i class="fa fa-search position-absolute start-0 top-50 translate-middle-y"></i>
                                        <input type="text" class="form-control ps-8 border-0" id="searchInput"
                                            name="search" placeholder="Tìm kiếm chức năng..."
                                            data-kt-search-element="input" />
                                        <span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-1"
                                            data-kt-search-element="spinner">
                                            <span
                                                class="spinner-border h-15px w-15px align-middle text-gray-400"></span>
                                        </span>
                                    </div>
                                    <div class="scroll-y mh-200px mh-lg-325px">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex flex-column">
                                                <div id="functionList" style="display: none;">
                                                    @foreach (config('apps.function_list') as $value)
                                                        @foreach ($value as $key => $item)
                                                            <a href="{{ route($item['route']) }}">
                                                                <div class="mt-3 text-dark">{{ $item['name'] }}
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                                <div id="noResults" class="mt-3 text-danger" style="display: none;">
                                                    Không tìm thấy chức năng
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center ms-1 ms-lg-3 me-3">
                        <div class="notification position-relative" id="kt_activities_toggle">
                            <div class="bell-container">
                                <i class="fa fa-bell" style="font-size: 18px;"></i>
                            </div>
                            <span id="notification-count" class="badge badge-danger position-absolute d-none"
                                style="font-size: 8px;"></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                        <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click"
                            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            <img class="rounded-circle border border-dark"
                                src="{{ !empty(session('avatar')) ? asset('storage/' . session('avatar')) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                alt="user" />
                        </div>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold fs-6 w-300px"
                            data-kt-menu="true">
                            <div class="menu-item">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <div class="symbol symbol-50px me-5">
                                        <img class="rounded-circle border border-dark"
                                            src="{{ !empty(session('avatar')) ? asset('storage/' . session('avatar')) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                            alt="user" />
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="fw-bolder d-flex align-items-center fs-5">
                                            {{ session('fullname') }}

                                            <span
                                                class="badge {{ session('isAdmin') == 1 ? 'bg-success' : 'bg-primary' }} text-white fs-8 py-1 px-3 mx-2">
                                                {{ session('isAdmin') == 1 ? 'Admin' : 'Nhân viên' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="separator"></div>
                            <div class="menu-item">
                                <a href="{{ route('profile.index') }}" class="menu-link rounded-0">Hồ Sơ</a>
                            </div>
                            <div class="separator"></div>
                            <div class="menu-item">
                                <a href="{{ route('home.logout') }}" class="menu-link rounded-0">Đăng Xuất</a>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
                        <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                            id="kt_header_menu_mobile_toggle">
                            <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z"
                                        fill="black" />
                                    <path opacity="0.3"
                                        d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z"
                                        fill="black" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var input = this.value.toLowerCase();
        var items = document.querySelectorAll('#functionList a div');
        var functionList = document.getElementById('functionList');
        var noResults = document.getElementById('noResults');
        var hasResults = false;

        if (input === '') {
            functionList.style.display = 'none';
            noResults.style.display = 'none';
        } else {
            functionList.style.display = 'block';
            items.forEach(function(item) {
                if (item.textContent.toLowerCase().includes(input)) {
                    item.style.display = '';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            noResults.style.display = hasResults ? 'none' : 'block';
        }
    });
</script>

<script>
    const notificationCountElement = document.querySelector(
        '#notification-count');

    function updateNotificationCount() {
        fetch('{{ route('notifications.count') }}')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    notificationCountElement.classList.remove('d-none');
                    notificationCountElement.textContent = data.count;
                } else {
                    notificationCountElement.classList.add('d-none');
                    notificationCountElement.textContent = '';
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    }

    // Xử lý khi nhấp vào biểu tượng thông báo
    document.querySelector('#kt_activities_toggle').addEventListener('click', function() {
        fetch('{{ route('notifications.markAsRead') }}')
            .then(response => response.json())
            .then(() => {
                const notificationCountElement = document.querySelector('#notification-count');
                notificationCountElement.classList.add('d-none');
                notificationCountElement.textContent = '';
            })
            .catch(error => console.error('Error marking notifications as read:', error));
    });

    setInterval(updateNotificationCount, 5000);

    updateNotificationCount();
</script>

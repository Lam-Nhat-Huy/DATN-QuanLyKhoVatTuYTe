@extends('master_layout.layout')

@section('styles')
    <style>
        .hover-table:hover {
            background: #ccc;
        }

        .selected-row {
            background: #ddd;
        }

        .active-row {
            background: #d1c4e9;
            /* Màu nền khi hàng được nhấp vào */
        }
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
    <script>
        // Đổi biểu tượng khi bấm vào td có chứa chevron
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(td) {
            td.addEventListener('click', function(event) {
                // Tìm phần tử <i> bên trong <td>
                var icon = this.querySelector('i');

                // Kiểm tra nếu có <i> thì thực hiện đổi biểu tượng
                if (icon) {
                    // Đổi icon khi click
                    if (icon.classList.contains('fa-chevron-right')) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    } else {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                }

                // Ngăn chặn việc click ảnh hưởng đến hàng (row)
                event.stopPropagation();
            });
        });

        document.getElementById('selectAll').addEventListener('change', function() {
            var isChecked = this.checked;
            var checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
                var row = checkbox.closest('tr');
                if (isChecked) {
                    row.classList.add('selected-row');
                } else {
                    row.classList.remove('selected-row');
                }
            });
        });

        document.querySelectorAll('.row-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var row = this.closest('tr');
                if (this.checked) {
                    row.classList.add('selected-row');
                } else {
                    row.classList.remove('selected-row');
                }

                var allChecked = true;
                document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                    if (!cb.checked) {
                        allChecked = false;
                    }
                });
                document.getElementById('selectAll').checked = allChecked;
            });
        });

        document.querySelectorAll('tbody tr').forEach(function(row) {
            row.addEventListener('click', function() {
                var checkbox = this.querySelector('.row-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    if (checkbox.checked) {
                        this.classList.add('selected-row');
                    } else {
                        this.classList.remove('selected-row');
                    }

                    var allChecked = true;
                    document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                        if (!cb.checked) {
                            allChecked = false;
                        }
                    });
                    document.getElementById('selectAll').checked = allChecked;
                }
            });
        });

        document.querySelectorAll('.printPdfBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var printContents = document.querySelector('.printArea')
                    .innerHTML; // Use querySelector to select a single element
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            });
        });
    </script>
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Yêu Cầu Nhập kho</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('material_request.import_trash') }}" class="btn btn-sm btn-danger me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng Rác
                    </span>
                </a>
                <a href="{{ route('material_request.create_import') }}" class="btn btn-success btn-sm">
                    <i class="fa fa-plus me-1"></i>Tạo Phiếu
                </a>
            </div>
        </div>
        <div class="card-body py-1 me-9">
            <form action="" class="row align-items-center">
                <div class="col-3">
                    <select name="ur" class="mt-2 mb-2 form-select form-select-sm form-select-solid setupSelect2">
                        <option value="" selected>--Theo Nhà Cung Cấp--</option>
                        <option value="a">A</option>
                        <option value="b">B</option>
                    </select>
                </div>
                <div class="col-3">
                    <select name="ur" class="mt-2 mb-2 form-select form-select-sm form-select-solid setupSelect2">
                        <option value="" selected>--Theo Người Tạo--</option>
                        <option value="a">A</option>
                        <option value="b">B</option>
                    </select>
                </div>
                <div class="col-3">
                    <select name="stt" class="mt-2 mb-2 form-select form-select-sm form-select-solid setupSelect2">
                        <option value="" selected>--Theo Trạng Thái--</option>
                        <option value="1" {{ request()->stt == 1 ? 'selected' : '' }}>Chưa Duyệt</option>
                        <option value="2" {{ request()->stt == 2 ? 'selected' : '' }}>Đã Duyệt</option>
                    </select>
                </div>
                <div class="col-3">
                    <div class="row">
                        <div class="col-10">
                            <input type="search" name="kw" placeholder="Tìm Kiếm Mã Yêu Cầu.."
                                class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success"
                                value="{{ request()->kw }}">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-dark btn-sm mt-2 mb-2" type="submit">Tìm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bolder bg-success">
                            <th class="ps-4">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th class="">Mã Yêu Cầu</th>
                            <th class="">Nhà Cung Cấp</th>
                            <th class="">Người Tạo</th>
                            <th class="">Ngày Tạo</th>
                            <th class="pe-3" style="width: 60px !important;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($AllMaterialRequest as $item)
                            <tr class="text-center hover-table pointer">
                                <td>
                                    <input type="checkbox" class="row-checkbox" />
                                </td>
                                <td>
                                    #{{ $item['material_request_code'] }}
                                </td>
                                <td>
                                    {{ $item['supplier_id'] }}
                                </td>
                                <td>
                                    {{ $item['user_create'] }}
                                </td>
                                <td>
                                    {{ $item['date_of_entry'] }}
                                </td>
                                <td class="text-center" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $item['id'] }}" id="toggleIcon{{ $item['id'] }}">
                                    <i class="fa fa-chevron-right pointer">
                                    </i>
                                </td>
                            </tr>

                            <!-- Collapse content -->
                            <tr class="collapse multi-collapse" id="collapse{{ $item['id'] }}">
                                <td class="p-0" colspan="12"
                                    style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
                                    <div class="flex-lg-row-fluid border-2 border-lg-1">
                                        <div class="card card-flush p-2"
                                            style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                            <div class="card-header d-flex justify-content-between align-items-center p-2"
                                                style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                <h4 class="fw-bold m-0">Danh Sách Thiết Bị</h4>
                                                <div class="card-toolbar">
                                                    @if ($item['status'] == 1)
                                                        <div class="rounded px-2 py-1 text-white bg-danger">Chưa Duyệt
                                                        </div>
                                                    @else
                                                        <div class="rounded px-2 py-1 text-white bg-success">Đã Duyệt</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-body p-2" style="padding-top: 0px !important">
                                                <!-- Begin::Receipt Items (Right column) -->
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-sm table-hover">
                                                            <thead class="fw-bolder bg-danger">
                                                                <tr>
                                                                    <th class="ps-4">Tên vật tư</th>
                                                                    <th>Đơn Vị Tính</th>
                                                                    <th class="pe-3">Số lượng</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="text-center">
                                                                    <td>Băng gạc</td>
                                                                    <td>Bịch</td>
                                                                    <td>100</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- End::Receipt Items -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body py-3 text-end">
                                        <div class="button-group">
                                            @if ($item['status'] == 1)
                                                <!-- Nút Duyệt đơn -->
                                                <button class="btn btn-sm btn-success me-2" data-bs-toggle="modal"
                                                    data-bs-target="#browse_{{ $item['id'] }}"
                                                    type="btn btn-sm btn-success">
                                                    <i class="fas fa-clipboard-check"></i>Duyệt Phiếu
                                                </button>

                                                <!-- Nút Sửa đơn -->
                                                <a href="{{ route('material_request.update_import') }}"
                                                    class="btn btn-sm btn-dark me-2">
                                                    <i class="fa fa-edit"></i>Sửa Phiếu
                                                </a>

                                                <!-- Nút Xóa đơn -->
                                                <button class="btn btn-sm btn-danger me-2" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal_{{ $item['id'] }}" type="button">
                                                    <i class="fa fa-trash"></i>Xóa Phiếu
                                                </button>
                                            @else
                                                <!-- Nút In Phiếu -->
                                                <button class="btn btn-sm btn-dark me-2" type="button">
                                                    <i class="fas fa-file-import"></i>Tạo Phiếu Nhập Nhanh
                                                </button>

                                                <!-- Nút In Phiếu -->
                                                <button class="btn btn-sm btn-twitter me-2 printPdfBtn" type="button">
                                                    <i class="fa fa-print"></i>In Phiếu
                                                </button>

                                                <!-- Nút Xóa đơn -->
                                                <button class="btn btn-sm btn-danger me-2" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal_{{ $item['id'] }}" type="button">
                                                    <i class="fa fa-trash"></i>Xóa Phiếu
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Duyệt --}}
                                    <div class="modal fade" id="browse_{{ $item['id'] }}" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="checkModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="checkModalLabel">Duyệt Yêu Cầu Nhập Kho
                                                    </h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="">
                                                        @csrf
                                                        <h4 class="text-danger text-center">Duyệt Yêu Cầu Nhập Kho Này?
                                                        </h4>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-sm btn-twitter">Duyệt</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- In --}}
                                    <div class="fade modal printArea">
                                        <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                            <div class="d-flex mb-5">
                                                <img src="https://i.pinimg.com/originals/36/b0/a0/36b0a084544360c807d7c778358f762d.png"
                                                    width="100" alt="">
                                                <div class="text-left mt-3">
                                                    <h6 class="mb-0 pb-0">PHÒNG KHÁM ĐA KHOA BEESOFT</h6>
                                                    <div>307C Nguyễn Văn Linh, An Khánh, Ninh Kiều, Cần Thơ
                                                    </div>
                                                    <div>Hotline: 0900900999</div>
                                                </div>
                                            </div>
                                            <form action="" method="post">
                                                <div class="text-center mb-13">
                                                    <h1 class="mb-3 text-uppercase text-primary">Phiếu Yêu Cầu
                                                        Đặt Mua Thiết Bị
                                                    </h1>
                                                    <div class="text-muted fw-bold fs-6">Thông Tin Chi Tiết Về
                                                        Phiếu Yêu Cầu Đặt Mua Thiết Bị
                                                        <span class="link-primary fw-bolder">#MaYeuCauMuaHang</span>.
                                                    </div>
                                                    <div class="text-muted fs-30">
                                                        Ngày Lập 2-9-2024
                                                    </div>
                                                </div>
                                                <div class="mb-15 text-left">
                                                    <!-- Begin::Receipt Info -->
                                                    <div class="mb-4">
                                                        <h4 class="text-primary border-bottom border-dark pb-4">
                                                            Thông Tin Đặt Mua Thiết Bị</h4>
                                                        <div class="pt-2">
                                                            <p><strong>Người Yêu Cầu:</strong> <span id="modalSupplier">Lữ
                                                                    Phát Huy</span>
                                                            </p>
                                                            <p><strong>Địa Chỉ:</strong> <span id="modalSupplier">24, Trần
                                                                    Chiên, Lê Bình,
                                                                    Cái Răng, Cần Thơ</span>
                                                            </p>
                                                            <p><strong>Số Điện Thoại:</strong> <span
                                                                    id="modalSupplier">0945 567 048</span>
                                                            </p>
                                                            <h6><span id="modalSupplier">Công Ty <span
                                                                        class="text-success">BeeSoft</span> Có
                                                                    Nhu
                                                                    Cầu Đặt Mua Thiết Bị Tại
                                                                    <span class="text-danger">#TenNhaCungCap</span>
                                                                    theo mẫu yêu
                                                                    cầu như sau:</span>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <!-- End::Receipt Info -->

                                                    <!-- Begin::Receipt Items -->
                                                    <div class="mb-4">
                                                        <h4 class="text-primary border-bottom border-dark pb-4 mb-4">
                                                            Danh Sách Vật Tư</h4>
                                                        <div class="table-responsive">
                                                            <table class="table table-striped align-middle gs-0 gy-4">
                                                                <thead>
                                                                    <tr class="fw-bolder bg-success">
                                                                        <th style="width: 33%;">Vật Tư</th>
                                                                        <th style="width: 33%;">Đơn Vị</th>
                                                                        <th style="width: 33%;">Số Lượng
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="text-center">
                                                                        <td>
                                                                            Bình Oxy Y Tế - (Bình 5 Lít)
                                                                        </td>
                                                                        <td>
                                                                            Bình
                                                                        </td>
                                                                        <td>
                                                                            100
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div>
                                                            <p><strong>Ghi Chú: </strong><span>Giao Lẹ</span>
                                                            </p>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-7"></div>
                                                            <div class="col-5 text-center">
                                                                <p class="m-0 p-0">
                                                                    Cần Thơ, ngày
                                                                    {{ \Carbon\Carbon::now()->day }}
                                                                    tháng
                                                                    {{ \Carbon\Carbon::now()->month }} năm
                                                                    {{ \Carbon\Carbon::now()->year }}
                                                                </p>
                                                                <p class="m-0 p-0">
                                                                    <strong>Người Lập</strong>
                                                                </p>
                                                                <p style="margin-top: 70px !important;">
                                                                    Lữ Phát Huy
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Xóa --}}
                                    <div class="modal fade" id="deleteModal_{{ $item['id'] }}"
                                        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                        aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="deleteModalLabel">Xóa Yêu Cầu Nhập Kho
                                                    </h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="">
                                                        @csrf
                                                        <h4 class="text-danger text-center">Xóa Yêu Cầu Nhập Kho Này?</h4>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-sm btn-danger">Xóa</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-body py-3">
            <div class="dropdown">
                <span class="btn btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span>Chọn Thao Tác</span>
                </span>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#confirmAll">
                            <i class="fas fa-clipboard-check me-2 text-success"></i>Duyệt Tất Cả</a>
                    </li>
                    <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                            <i class="fas fa-trash me-2 text-danger"></i>Xóa Tất Cả</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Modal Duyệt Tất Cả --}}
    <div class="modal fade" id="confirmAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="confirmAll" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white" id="confirmAll">Duyệt Tất Cả Phiếu Nhập Kho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt tất cả Phiếu Nhập Kho đã chọn?</p>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary btn-sm px-4"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-success px-4">
                        Duyệt</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Xác Nhận Xóa Tất Cả --}}
    <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="deleteAllLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Tất Cả Phiếu Nhập Kho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả Phiếu Nhập Kho đã chọn?</p>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-success px-4"> Xóa</button>
                </div>
            </div>
        </div>
    </div>
@endsection

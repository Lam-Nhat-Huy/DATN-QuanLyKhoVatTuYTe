@extends('master_layout.layout')

@section('styles')
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
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipment_request.export') }}" class="btn btn-sm btn-dark me-2 rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive rounded">
                <table class="table align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bolder bg-success">
                            <th class="ps-3">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th class="">Mã Yêu Cầu</th>
                            <th class="">Lý Do Xuất</th>
                            <th class="">Phòng Ban</th>
                            <th class="">Người Tạo</th>
                            <th class="">Ngày Tạo</th>
                            <th class="pe-3" style="width: 60px !important;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center hover-table pointer">
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>
                                #YCX113
                            </td>
                            <td>
                                Cấp Phát Cho Phòng Ban
                            </td>
                            <td>
                                Khoa xét nghiệm
                            </td>
                            <td>
                                Lữ Phát Huy
                            </td>
                            <td>
                                12-09-2024
                            </td>
                            <td class="text-center" data-bs-toggle="collapse" data-bs-target="#collapse" id="toggleIcon">
                                <i class="fa fa-caret-right pointer">
                                </i>
                            </td>
                        </tr>

                        <!-- Collapse content -->
                        <tr class="collapse multi-collapse" id="collapse">
                            <td class="p-0" colspan="12"
                                style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
                                <div class="flex-lg-row-fluid border-2 border-lg-1">
                                    <div class="card card-flush p-2"
                                        style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                        <div class="card-header d-flex justify-content-between align-items-center p-2"
                                            style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                            <h4 class="fw-bold m-0">Danh Sách Thiết Bị</h4>
                                            <div class="card-toolbar">
                                                @if (1 == 1)
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
                                                <div class="table-responsive rounded">
                                                    <table class="table table-striped table-sm table-hover">
                                                        <thead class="fw-bolder bg-danger">
                                                            <tr>
                                                                <th class="ps-3">Tên thiết bị</th>
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
                                        <button class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal"
                                            data-bs-target="#restoreModal_"><i class="fa fa-rotate-left"></i>Khôi
                                            Phục</button>

                                        <button class="btn btn-sm btn-danger rounded-pill" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal_"><i class="fa fa-trash"></i>Xóa
                                            Vĩnh Viễn</button>
                                    </div>
                                </div>

                                {{-- Khôi phục --}}
                                <div class="modal fade" id="restoreModal_" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="restoreModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title" id="restoreModalLabel">Khôi Phục Yêu Cầu Xuất
                                                    Kho
                                                </h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="">
                                                    @csrf
                                                    <h4 class="text-success text-center">Khôi Phục Yêu Cầu Xuất Kho Này?
                                                    </h4>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Đóng</button>
                                                <button type="button" class="btn btn-sm btn-success">Khôi
                                                    Phục</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Xóa vĩnh viễn --}}
                                <div class="modal fade" id="deleteModal_" data-bs-backdrop="static" data-bs-keyboard="false"
                                    tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title" id="deleteModalLabel">Xóa Vĩnh Viễn Yêu Cầu Xuất
                                                    Kho
                                                </h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="">
                                                    @csrf
                                                    <h4 class="text-danger text-center">Xóa Vĩnh Viễn Yêu Cầu Xuất Kho
                                                        Này?</h4>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Đóng</button>
                                                <button type="button" class="btn btn-sm btn-danger">Xóa Vĩnh
                                                    Viễn</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-body py-3">
            <div class="dropdown">
                <span class="btn btn-info btn-sm dropdown-toggle rounded-pill" id="dropdownMenuButton1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span>Chọn Thao Tác</span>
                </span>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#confirmAll">
                            <i class="fas fa-rotate-right me-2 text-success"></i>Khôi Phục Tất Cả
                        </a>
                    </li>
                    <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                            <i class="fas fa-trash me-2 text-danger"></i>Xóa Vĩnh Viễn Tất Cả
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Modal Khôi Phục Tất Cả --}}
    <div class="modal fade" id="confirmAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="confirmAll" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white" id="confirmAll">Khôi Phục Tất Cả Yêu Cầu Xuất Kho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn khôi phục tất cả Yêu Cầu Xuất Kho đã chọn?</p>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary btn-sm px-4"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-success px-4">
                        Khôi Phục</button>
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
                    <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Tất Cả Yêu Cầu Xuất kho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả Yêu Cầu Xuất kho đã chọn?</p>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-danger px-4"> Xóa</button>
                </div>
            </div>
        </div>
    </div>
@endsection

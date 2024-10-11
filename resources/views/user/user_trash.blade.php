@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
    <script>
        // Hàm kiểm tra và ẩn/hiện nút xóa tất cả
        function toggleDeleteAction() {
            var anyChecked = false;
            document.querySelectorAll('.row-checkbox').forEach(function(checkbox) {
                if (checkbox.checked) {
                    anyChecked = true;
                }
            });

            if (anyChecked) {
                document.getElementById('action_delete_all').style.display = 'block';
            } else {
                document.getElementById('action_delete_all').style.display = 'none';
            }
        }

        // Khi click vào checkbox "Select All"
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
            toggleDeleteAction();
        });

        // Khi checkbox của từng hàng thay đổi
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
                toggleDeleteAction(); // Gọi hàm kiểm tra nút xóa tất cả
            });
        });

        // Khi người dùng click vào hàng
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
                    toggleDeleteAction(); // Gọi hàm kiểm tra nút xóa tất cả
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Khi nhấn nút "Khôi Phục Tất Cả"
            document.querySelector('#restoreAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'restore';
            });

            // Khi nhấn nút "Xóa Tất Cả"
            document.querySelector('#deleteAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'delete';
            });
        });

        // Kiểm tra trạng thái ban đầu khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            toggleDeleteAction();
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
                <a href="{{ route('user.index') }}" class="btn rounded-pill btn-sm btn-dark me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <form action="{{ route('user.user_trash') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder bg-success">
                                <th class="ps-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="" style="width: 7% !important;">Mã</th>
                                <th class="" style="width: 10% !important;">Ảnh</th>
                                <th class="" style="width: 15% !important;">Họ Tên</th>
                                <th class="" style="width: 25% !important;">Email</th>
                                <th class="" style="width: 12% !important;">Số Điện Thoại</th>
                                <th class="" style="width: 8% !important;">Giới Tính</th>
                                <th class="" style="width: 10% !important;">Trạng Thái</th>
                                <th class="pe-3 text-center" style="width: 20% !important;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allUserTrash as $item)
                                <tr class="hover-table pointer">
                                    <td>
                                        <input type="checkbox" name="user_codes[]" value="{{ $item->code }}"
                                            class="row-checkbox" />
                                    </td>
                                    <td>
                                        #{{ $item->code }}
                                    </td>
                                    <td>
                                        <img class="rounded-circle border border-dark"
                                            src="{{ $item->avatar ? asset('storage/' . $item->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                            style="width: 70px !important; height: 70px !important;" alt="">
                                    </td>
                                    <td>
                                        {{ $item->last_name . ' ' . $item->first_name }}
                                    </td>
                                    <td>
                                        {{ $item->email }}
                                    </td>
                                    <td>
                                        {{ $item->phone }}
                                    </td>
                                    <td>
                                        {{ $item->gender }}
                                    </td>
                                    <td>
                                        <div class="checkbox-wrapper-6">
                                            <input class="tgl tgl-light" id="cb1-6" type="checkbox"
                                                {{ !empty($item->status) == 1 ? 'checked' : '' }} disabled />
                                            <label class="tgl-btn" for="cb1-6"></label>
                                        </div>
                                    </td>
                                    <td class="text-center" data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $item['code'] }}" id="toggleIcon{{ $item['code'] }}">
                                        Chi Tiết<i class="fa fa-chevron-right pointer ms-2"></i>
                                    </td>
                                </tr>

                                <!-- Collapse content -->
                                <tr class="collapse multi-collapse" id="collapse_{{ $item['code'] }}">
                                    <td class="p-0" colspan="12">
                                        <div class="flex-lg-row-fluid border-2">
                                            <div class="card card-flush p-2"
                                                style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                                <div class="card-header justify-content-center p-2"
                                                    style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                    <div class="row px-5 w-100">
                                                        <div class="col-md-12 my-3">
                                                            <h4 class="fw-bold mt-3">Thông Tin Chi Tiết</h4>
                                                        </div>

                                                        <div class="row mb-5 justify-content-center">
                                                            <div class="col-md-3">
                                                                <img src="{{ $item->avatar ? asset('storage/' . $item->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                                                    class="rounded border border-dark"
                                                                    style="width: 175px !important; height: 175px !important;"
                                                                    alt="">
                                                            </div>
                                                            <div class="col-md-9">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <table class="table table-borderless">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Họ Và Tên:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->last_name . ' ' . $item->first_name }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Email:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->email }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Phone:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->phone }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Địa Chỉ:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->address }}
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <table class="table table-borderless">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Năm Sinh:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->birth_day }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Giới Tính:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->gender }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Vai Trò:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->position }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="fw-semibold">Ngày Xóa Tài
                                                                                        Khoản:</td>
                                                                                    <td class="text-dark">
                                                                                        {{ $item->deleted_at->format('d-m-Y') }}
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body py-3 border-top-0 border-2 text-end">
                                            <div class="button-group">
                                                <button type="button" class="btn btn-sm btn-twitter rounded-pill me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#restoreModal_{{ $item->code }}">
                                                    <i class="fa fa-rotate-right" style="margin-bottom: 2px;"></i> Khôi
                                                    Phục
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal_{{ $item->code }}">
                                                    <i class="fa fa-trash" style="margin-bottom: 2px;"></i> Xóa
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataAlert">
                                    <td colspan="11" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fas fa-ban" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có dữ
                                                    liệu</h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($allUserTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fas fa-rotate-right me-2 text-primary"></i>Khôi Phục
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa Vĩnh Viễn
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $allUserTrash->links('pagination::bootstrap-4') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Khôi Phục Tất Cả --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllLabel">Xác Nhận khôi phục người dùng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục người dùng đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Khôi
                                Phục</button>
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
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa người dùng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa người dùng đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @foreach ($allUserTrash as $item)
        {{-- Khôi phục --}}
        <div class="modal fade" id="restoreModal_{{ $item->code }}" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('user.user_trash') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_code_restore" value="{{ $item->code }}">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white" id="restoreModalLabel">Khôi Phục Người Dùng
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body pb-0 text-center">
                            <p class="text-primary text-center">Khôi Phục Người Dùng Này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Khôi
                                Phục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Xóa --}}
        <div class="modal fade" id="deleteModal_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('user.user_trash') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_code_delete" value="{{ $item->code }}">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Vĩnh Viễn Người Dùng
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body pb-0 text-center">
                            <p class="text-danger text-center">Xóa Vĩnh Viễn Người Dùng Này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

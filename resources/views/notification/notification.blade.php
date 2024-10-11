@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
    <script>
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
            document.querySelector('#browseAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'browse';
            });

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
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Thông Báo</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('notification.notification_trash') }}" class="btn rounded-pill btn-sm btn-danger me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng Rác
                    </span>
                </a>
                <a href="{{ route('notification.notification_add') }}" class="btn rounded-pill btn-sm btn-twitter">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-plus me-1"></i>
                        Thêm Thông Báo
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body py-1">
            <form action="{{ route('notification.index') }}" method="GET" class="row align-items-center">
                <div class="col-lg-2 col-md-4 col-sm-12">
                    <select name="ur" id="ur"
                        class="mt-2 mb-2 form-select form-select-sm rounded-pill border border-success setupSelect2 w-100">
                        <option value="" selected>--Theo Người Tạo--</option>
                        @foreach ($AllUser as $item)
                            <option value={{ $item->code }} {{ request()->ur == $item->code ? 'selected' : '' }}>
                                {{ $item->last_name }} {{ $item->first_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12">
                    <select name="rt" id="rt"
                        class="mt-2 mb-2 form-select form-select-sm rounded-pill border border-success setupSelect2 w-100">
                        <option value="" selected>--Theo Loại Báo Cáo--</option>
                        @foreach ($AllNotificationType as $item)
                            <option value={{ $item->id }} {{ request()->rt == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12">
                    <select name="st" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                        <option value="" {{ request()->st == '' ? 'selected' : '' }}>--Theo Trạng Thái--</option>
                        <option value="1" {{ request()->st == '1' ? 'selected' : '' }}>Hiển Thị</option>
                        <option value="0" {{ request()->st == '0' ? 'selected' : '' }}>Không Hiển Thị</option>
                    </select>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <input type="search" name="kw" placeholder="Tìm kiếm mã thông báo.."
                                class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success w-100"
                                value="{{ request()->kw }}">
                        </div>
                        <div class="col-5 d-flex justify-content-between">
                            <a class="btn rounded-pill btn-info btn-sm mt-2 mb-2 w-100 me-2"
                                href="{{ route('notification.index') }}"><i class="fas fa-times-circle"
                                    style="margin-bottom: 2px;"></i>Bỏ Lọc</a>
                            <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 w-100 load_animation"
                                type="submit"><i class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <form action="{{ route('notification.index') }}" id="form-2" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4">
                        <thead>
                            <tr class="bg-success text-center">
                                <th class="ps-4">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="" style="width: 10%">Mã</th>
                                <th class="" style="width: 10%">Người Tạo</th>
                                <th class="" style="width: 8%">Nội Dung</th>
                                <th class="" style="width: 12%">Loại</th>
                                <th class="" style="width: 10%">Ngày Tạo</th>
                                <th class="text-center" style="width: 12%">Trạng Thái</th>
                                <th class="text-center" style="width: 9%">Quan Trọng</th>
                                <th class="text-center" style="width: 9%">Khóa Kho</th>
                                <th class="pe-3 text-center" style="width: 20%">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($AllNotification as $item)
                                <tr class="hover-table pointer text-center">
                                    <td>
                                        <input type="checkbox" name="notification_codes[]" value="{{ $item->code }}"
                                            class="row-checkbox" />
                                    </td>
                                    <td>
                                        #{{ $item->code }}
                                    </td>
                                    <td>
                                        {{ !empty($item->users->last_name && $item->users->first_name) ? $item->users->last_name . ' ' . $item->users->first_name : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="text-primary pointer" data-bs-toggle="modal"
                                            data-bs-target="#detail_{{ $item->code }}">Xem
                                        </span>
                                    </td>
                                    <td>
                                        {{ $item->notification_types->name ?? 'Kiểm Kho' }}
                                    </td>
                                    <td>
                                        {{ $item->created_at->format('d-m-Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status == 1)
                                            <span
                                                style="font-size: 9px; font-weight: 500; padding: 4px 8px; border-radius: 6px; 
                                                         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); background-color: #45ff07; color: white;">
                                                Hiển thị
                                            </span>
                                        @else
                                            <span
                                                style="font-size: 9px; font-weight: 500; padding: 4px 8px; border-radius: 6px; 
                                                         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); background-color: #dc3545; color: white;">
                                                Không hiển thị
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($item->important == 1)
                                            <span
                                                style="font-size: 9px; font-weight: 500; padding: 4px 8px; border-radius: 6px; 
                                                         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); background-color: #45ff07; color: #ffffff;">
                                                Có
                                            </span>
                                        @else
                                            <span
                                                style="font-size: 9px; font-weight: 500; padding: 4px 8px; border-radius: 6px; 
                                                         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); background-color: #dc3545; color: white;">
                                                Không
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($item->lock_warehouse == 1)
                                            <span
                                                style="font-size: 9px; font-weight: 500; padding: 4px 8px; border-radius: 6px; 
                                                         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); background-color: #45ff07; color: #ffffff;">
                                                Có
                                            </span>
                                        @else
                                            <span
                                                style="font-size: 9px; font-weight: 500; padding: 4px 8px; border-radius: 6px; 
                                                         box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); background-color: #dc3545; color: white;">
                                                Không
                                            </span>
                                        @endif
                                    </td>


                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('notification.notification_edit', $item->code) }}"
                                                class="btn btn-sm btn-info me-2 rounded-pill">
                                                <i class="fa fa-edit" style="margin-bottom: 2px;"></i> Sửa
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $item->code }}">
                                                <i class="fa fa-trash" style="margin-bottom: 2px;"></i> Xóa
                                            </button>
                                        </div>
                                    </td>


                                    {{-- Chi Tiết --}}
                                    <div class="modal fade" id="detail_{{ $item->code }}" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="DetailModal"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="DetailModal">Nội Dung Thông Báo
                                                        #{{ $item->code }}
                                                    </h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body pb-0">
                                                    <strong>
                                                        {!! $item->content !!}
                                                    </strong>
                                                </div>
                                                <div class="modal-footer pt-0">
                                                    <button type="button" class="btn rounded-pill btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Duyệt Thông Báo -->
                                    {{-- <div class="modal fade" id="browse_{{ $item->code }}" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="checkModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title text-white" id="checkModalLabel">Duyệt
                                                        Thông Báo</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('notification.index') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="browse_notification"
                                                        value="{{ $item->code }}">
                                                    <div class="modal-body text-center pb-0">
                                                        <p class="text-dark mb-4">Bạn có chắc chắn muốn duyệt thông báo
                                                            này?
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer justify-content-center border-0">
                                                        <button type="button"
                                                            class="btn rounded-pill btn-sm btn-secondary px-4"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit"
                                                            class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Duyệt</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}


                                    {{-- Xóa --}}
                                    <div class="modal fade" id="deleteModal_{{ $item->code }}"
                                        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                        aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Thông
                                                        Báo
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('notification.index') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="delete_notification"
                                                        value="{{ $item->code }}">
                                                    <div class="modal-body pb-0 text-center pb-0">
                                                        <p class="text-danger mb-4">Bạn chắc chắn xóa thông báo này?</p>
                                                    </div>
                                                    <div class="modal-footer justify-content-center border-0 pt-0">
                                                        <button type="button"
                                                            class="btn rounded-pill btn-sm btn-secondary px-4"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit"
                                                            class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataAlert">
                                    <td colspan="10" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fas fa-ban" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không Có Dữ
                                                    Liệu</h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($AllNotification->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#browseAll">
                                    <i class="fas fa-clipboard-check me-2 text-success"></i>Duyệt
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $AllNotification->links('pagination::bootstrap-4') }}
                    </ul>
                </div>
            @endif
            {{-- Modal Duyệt Tất Cả --}}
            <div class="modal fade" id="browseAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="browseAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="browseAllLabel">Duyệt Thông Báo</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-dark mb-4">Bạn có chắc chắn muốn duyệt thông báo đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pt-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary btn-sm px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">
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
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Thông Báo</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center pb-0" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa thông báo đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pt-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-danger px-4 load_animation">
                                Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

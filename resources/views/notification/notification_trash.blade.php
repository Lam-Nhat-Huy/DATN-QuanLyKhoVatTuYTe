@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Thông Báo</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('notification.index') }}" class="btn rounded-pill btn-sm btn-dark me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>

        <form action="{{ route('notification.notification_trash') }}" id="form-1" method="POST">
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
                                <th class="" style="width: 18%">Mã Thông Báo</th>
                                <th class="" style="width: 18%">Người Tạo</th>
                                <th class="" style="width: 18%">Nội Dung</th>
                                <th class="" style="width: 18%">Loại Thông Báo</th>
                                <th class="" style="width: 18%">Ngày Xóa</th>
                                <th class="pe-3 text-center" style="width: 10%">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($AllNotificationTrash as $item)
                                <tr class="hover-table pointer">
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
                                            data-bs-target="#detail_{{ $item->code }}">Xem Nội Dung
                                        </span>
                                    </td>
                                    <td>
                                        {{ $item->notification_types->name }}
                                    </td>
                                    <td>
                                        {{ $item->deleted_at->format('d-m-Y') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-h me-2"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#restore_{{ $item->code }}">
                                                        <i class="fa fa-rotate-right me-1"></i>Khôi Phục
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item pointer" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal_{{ $item->code }}">
                                                        <i class="fa fa-trash me-1"></i>Xóa Vĩnh Viễn
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        {{-- Chi Tiết --}}
                                        <div class="modal fade" id="detail_{{ $item->code }}" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="DetailModal"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="DetailModal">Nội Dung Thông Báo
                                                            #{{ $item->code }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong>
                                                            {!! $item->content !!}
                                                        </strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn rounded-pill btn-sm btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Khôi Phục Thông Báo -->
                                        <div class="modal fade" id="restore_{{ $item->code }}" data-bs-backdrop="static"
                                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="checkModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white" id="checkModalLabel">Khôi Phục
                                                            Thông Báo</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('notification.notification_trash') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="restore_notification"
                                                            value="{{ $item->code }}">
                                                        <div class="modal-body text-center pb-0">
                                                            <p class="text-dark mb-4">Bạn có chắc chắn muốn khôi phục thông
                                                                báo này?
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer justify-content-center border-0">
                                                            <button type="button"
                                                                class="btn rounded-pill btn-sm btn-secondary px-4"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit"
                                                                class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Khôi
                                                                Phục</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        {{-- Xóa --}}
                                        <div class="modal fade" id="deleteModal_{{ $item->code }}"
                                            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                            aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Vĩnh
                                                            Viễn Thông
                                                            Báo
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('notification.notification_trash') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="delete_notification"
                                                            value="{{ $item->code }}">
                                                        <div class="modal-body pb-0 text-center">
                                                            <p class="text-danger mb-4">Xóa Vĩnh Viễn Thông Báo Này?</p>
                                                        </div>
                                                        <div class="modal-footer justify-content-center border-0">
                                                            <button type="button"
                                                                class="btn rounded-pill btn-sm btn-secondary px-4"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit"
                                                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa
                                                                Vĩnh Viễn</button>
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
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($AllNotificationTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fas fa-clipboard-check me-2 text-success"></i>Khôi Phục
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
                        {{ $AllNotificationTrash->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Khôi Phục Tất Cả --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllLabel">Xác Nhận khôi phục thông báo</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục thông báo đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Khôi
                                phục</button>
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
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa thông báo</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả thông báo đã chọn?</p>
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
            document.querySelector('#restoreAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'restore';
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

@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Đơn Vị</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('units.index') }}" class="btn btn-sm btn-dark rounded-pill">
                    <i class="fa fa-arrow-left" style="margin-bottom: 2px;"></i> Trở lại
                </a>
            </div>
        </div>

        <form action="{{ route('units.unit_trash') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4">
                        <thead>
                            <tr class="bg-success">
                                <th class="ps-3" style="width: 5%;"><input type="checkbox" id="selectAll" /></th>
                                <th class="" style="width: 10%;">Mã</th>
                                <th style="width: 15%;">Tên</th>
                                <th style="width: 40%;">Mô Tả</th>
                                <th class="pe-3 text-center" style="width: 30%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody id="unitTableBody">
                            @forelse ($allUnitTrash as $unit)
                                <tr class="hover-table pointer">
                                    <td class="text-xl-start">
                                        <input type="checkbox" class="row-checkbox" name="unit_codes[]"
                                            value="{{ $unit->code }}" />
                                    </td>
                                    <td>{{ $unit->code }}</td>
                                    <td>{{ $unit->name }}</td>
                                    <td>{{ $unit->description ?? 'Không có mô tả' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-twitter me-2 rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#restore_{{ $unit->code }}">
                                                <i class="fa fa-rotate-right"></i> Khôi Phục
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#delete_{{ $unit->code }}">
                                                <i class="fa fa-trash"></i> Xóa Vĩnh Viễn
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataAlert">
                                    <td colspan="12" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fas fa-medkit" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center mt-1">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có dữ
                                                    liệu về đơn vị </h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($allUnitTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fas fa-rotate-right me-2 text-primary"></i>Khôi Phục</a>
                            </li>
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa Vĩnh Viễn</a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $allUnitTrash->links('pagination::4') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận KP --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllLabel">Xác Nhận Khôi Phục Đơn Vị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục đơn vị đã chọn?</p>
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

            {{-- Modal Xác Nhận Xóa --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Vĩnh Viễn Đơn Vị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa vĩnh viễn đơn vị đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa Vĩnh
                                Viễn</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @foreach ($allUnitTrash as $item)
        {{-- KP --}}
        <div class="modal fade" id="restore_{{ $item->code }}" tabindex="-1" aria-labelledby="deleteModal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="deleteModal">Xác Nhận Khôi Phục
                            Đơn Vị</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pb-0">
                        <p class="text-primary">Bạn có chắc chắn muốn khôi phục đơn vị này?</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <form action="{{ route('units.unit_trash') }}" method="POST">
                            @csrf
                            <input type="hidden" name="restore_trash" value="{{ $item->code }}">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-sm btn-twitter rounded-pill load_animation">Khôi
                                Phục</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Xóa --}}
        <div class="modal fade" id="delete_{{ $item->code }}" tabindex="-1" aria-labelledby="deleteModal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModal">Xác Nhận Xóa Vĩnh Viễn
                            Đơn Vị</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pb-0">
                        <p class="text-danger">Bạn có chắc chắn muốn xóa vĩnh viễn đơn vị này?</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <form action="{{ route('units.unit_trash') }}" method="POST">
                            @csrf
                            <input type="hidden" name="delete_trash" value="{{ $item->code }}">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill load_animation">Xóa Vĩnh
                                Viễn</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
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

        // Kiểm tra trạng thái ban đầu khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            toggleDeleteAction();
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#restoreAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'restore';
            });

            document.querySelector('#deleteAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'delete';
            });
        });
    </script>
@endsection

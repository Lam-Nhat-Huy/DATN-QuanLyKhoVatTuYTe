@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipments.index') }}" class="btn btn-sm btn-dark me-2 rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>

        {{-- Danh sách thiết bị --}}
        <form action="{{ route('equipments.equipments_trash') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div id="equipmentList">
                    @if ($AllEquipmentTrash->isEmpty())
                        {{-- Thông báo khi danh sách trống mà không có tìm kiếm --}}
                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                            role="alert" style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                            <div class="mb-3">
                                <i class="fa-regular fa-trash-can" style="font-size: 36px; color: #6c757d;"></i>
                            </div>
                            <div class="text-center">
                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thùng Rác Rỗng</h5>
                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                    Không Có Thiết Bị Nào Bị Xóa
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-end mb-3 me-3">
                            <label class="me-2 pointer" for="selectAll">Chọn Tất Cả</label>
                            <input type="checkbox" class="p-2 pointer" id="selectAll">
                        </div>
                        @foreach ($AllEquipmentTrash as $item)
                            <div class="col-xl-12 mb-3 shadow" style="background: #ffffff !important; z-index: 1;">
                                <div class="position-relative">
                                    <div class="d-flex align-items-center position-absolute top-0 right-0 me-3 mt-2"
                                        style="z-index: 2;">
                                        <label for="input_checkbox_{{ $item->code }}" class="me-2 pointer">Chọn</label>
                                        <input type="checkbox" class="p-2 row-checkbox pointer"
                                            id="input_checkbox_{{ $item->code }}" name="equipment_codes[]"
                                            value="{{ $item->code }}">
                                    </div>
                                    <div class="card mb-1 card-body p-2 pointer rounded-top-0" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $item->code }}" aria-expanded="false"
                                        aria-controls="collapse{{ $item->code }}">
                                        <div class="row align-items-center p-2">
                                            <div class="col-auto pe-0">
                                                <a href="#">
                                                    <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://st4.depositphotos.com/14953852/24787/v/380/depositphotos_247872612-stock-illustration-no-image-available-icon-vector.jpg' }}"
                                                        class="rounded-3" width="80" alt="Medical Supply Image">
                                                </a>
                                            </div>
                                            <div class="col">
                                                <div class="overflow-hidden flex-nowrap">
                                                    <h6 class="mb-1">
                                                        <a href="#" class="text-reset">{{ $item->name }}</a>
                                                    </h6>
                                                    <span class="text-muted d-block mb-1 small">
                                                        Nhóm: {{ $item->equipmentType->name ?? 'Không có dữ liệu' }}
                                                    </span>
                                                    <div class="row align-items-center g-1">
                                                        <div class="col">
                                                            <p class="mb-1 small text-muted">Mã Thiết Bị:
                                                                #{{ $item->code }}
                                                            </p>
                                                            <p class="mb-1 small text-muted">
                                                                Tồn kho: {{ $item->inventories->sum('current_quantity') }}
                                                                {{ optional($item->units)->name }}
                                                            </p>
                                                        </div>
                                                        <div class="col-auto">
                                                            <span class="fw-bold text-success">Còn Hàng</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Collapsible Content -->
                                    <div class="collapse multi-collapse" id="collapse{{ $item->code }}">
                                        <div class="card card-body p-6 border-0"
                                            style="border: 1px solid #dcdcdc; background-color: #f8f9fa;">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://st4.depositphotos.com/14953852/24787/v/380/depositphotos_247872612-stock-illustration-no-image-available-icon-vector.jpg' }}"
                                                        alt="Medical Supply Image" width="100%"
                                                        class="img-fluid rounded shadow">
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="card card-body border-0 shadow">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <h6 class="card-title fw-bold">Chi Tiết</h6>
                                                            <span class="badge bg-success rounded-pill">Còn hàng</span>
                                                        </div>
                                                        <!-- Chi tiết Thiết Bị -->
                                                        <div class="row mt-3">
                                                            <div class="col-md-9">
                                                                <table class="table table-borderless">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Mã:</strong></td>
                                                                            <td class="text-gray-800">#{{ $item->code }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Nhóm:</strong></td>
                                                                            <td class="text-gray-800">
                                                                                {{ $item->equipmentType->name ?? 'Không có dữ liệu' }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Nhà cung cấp:</strong></td>
                                                                            <td class="text-gray-800">
                                                                                {{ $item->supplier->name ?? 'Không có dữ liệu' }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Ngày hết hạn:</strong></td>
                                                                            <td class="text-gray-800">
                                                                                {{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d/m/Y') : 'Không Có' }}
                                                                                {{ $item->time_remaining ? '- ' . $item->time_remaining : '' }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <table class="table table-borderless">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Giá:</strong></td>
                                                                            <td class="text-gray-800">
                                                                                {{ number_format($item->price) }} VNĐ</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Đơn vị:</strong></td>
                                                                            <td class="text-gray-800">
                                                                                {{ $item->units->name }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Mô tả:</strong></td>
                                                                            <td class="text-gray-800">
                                                                                {{ $item->description }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="text-end mt-4">
                                                            <div class="button-group">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-twitter me-2 rounded-pill"
                                                                    data-bs-toggle="modal" style="font-size: 10px;"
                                                                    data-bs-target="#restoreConfirm{{ $item->code }}">
                                                                    <i class="fa fa-rotate-right mb-1"></i> Khôi Phục
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger me-2 rounded-pill"
                                                                    data-bs-toggle="modal" style="font-size: 10px;"
                                                                    data-bs-target="#deleteConfirm{{ $item->code }}">
                                                                    <i class="fa fa-trash mb-1"></i> Xóa
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            @if ($AllEquipmentTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fa fa-rotate-right me-2 text-primary"></i>Khôi Phục
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
                        {{ $AllEquipmentTrash->links('pagination::bootstrap-4') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Khôi Phục --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllLabel">Xác Nhận Khôi Phục Thiết Bị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục thiết bị đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">
                                Khôi Phục</button>
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
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Thiết Bị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa thiết bị đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-danger px-4 load_animation">
                                Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Các Modal Xác Nhận Xóa Thiết Bị --}}
        @foreach ($AllEquipmentTrash as $item)
            {{-- Khôi Phục --}}
            <div class="modal fade" id="restoreConfirm{{ $item->code }}" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="restoreConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreConfirmLabel">Xác Nhận Khôi Phục Thiết Bị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <form action="{{ route('equipments.restore_equipment', $item->code) }}" method="POST">
                                @csrf
                                <p class="text-primary mb-4">Bạn có chắc chắn muốn xóa thiết bị này?</p>
                                <div class="modal-footer border-0 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill"
                                        data-bs-dismiss="modal" style="font-size: 10px;">Đóng</button>
                                    <button type="submit" class="btn btn-sm btn-twitter rounded-pill load_animation"
                                        style="font-size: 10px;">
                                        Khôi Phục</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Xóa --}}
            <div class="modal fade" id="deleteConfirm{{ $item->code }}" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteConfirmLabel">Xác Nhận Xóa Thiết Bị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <form action="{{ route('equipments.delete_permanently', $item->code) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa thiết bị này?</p>
                                <div class="modal-footer border-0 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill"
                                        data-bs-dismiss="modal" style="font-size: 10px;">Đóng</button>
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill load_animation"
                                        style="font-size: 10px;">
                                        Xóa</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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

        // Khi click vào checkbox "Chọn Tất Cả"
        document.getElementById('selectAll').addEventListener('change', function() {
            var isChecked = this.checked;
            var checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
                var parentDiv = checkbox.closest('.col-xl-12');
                if (isChecked) {
                    parentDiv.classList.add('selected-row');
                } else {
                    parentDiv.classList.remove('selected-row');
                }
            });
            toggleDeleteAction();
        });

        // Khi checkbox của từng hàng thay đổi
        document.querySelectorAll('.row-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var parentDiv = this.closest('.col-xl-12');
                if (this.checked) {
                    parentDiv.classList.add('selected-row');
                } else {
                    parentDiv.classList.remove('selected-row');
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

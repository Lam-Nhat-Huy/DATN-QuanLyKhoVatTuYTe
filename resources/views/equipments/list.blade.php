@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        {{-- Phần nút thêm thiết bị --}}
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Thiết Bị</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipments.equipments_trash') }}" class="btn btn-sm btn-danger me-2 rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng Rác
                    </span>
                </a>
                <a href="{{ route('equipments.insert_equipments') }}" class="btn btn-sm btn-success rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-plus"></i>
                        Thêm Thiết Bị
                    </span>
                </a>
            </div>
        </div>

        {{-- Bộ lọc thiết bị --}}
        <div class="card-body py-1">
            <form action="" method="GET" class="row align-items-center">
                <div class="col-md-3">
                    <select name="et" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" selected>--Theo Nhóm Thiết Bị--</option>
                        @foreach ($equipmentTypes as $item)
                            <option value="{{ $item->code }}" {{ request()->et == $item->code ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="un" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" selected>--Theo Đơn Vị--</option>
                        @foreach ($units as $item)
                            <option value="{{ $item->code }}" {{ request()->un == $item->code ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="sp" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" selected>--Theo Nhà Cung Cấp--</option>
                        @foreach ($suppliers as $item)
                            <option value="{{ $item->code }}" {{ request()->sp == $item->code ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="ct" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" selected>--Theo Quốc Gia--</option>
                        @foreach (config('apps.country') as $value)
                            <option value="{{ $value }}" {{ request()->ct == $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <input type="search" name="kw" placeholder="Tìm Kiếm Theo Mã, Tên.."
                                class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success"
                                value="{{ request()->kw }}">
                        </div>
                        <div class="col-3 d-flex justify-content-between">
                            <a class="btn rounded-pill btn-info btn-sm mt-2 mb-2 w-100 me-2"
                                href="{{ route('equipments.index') }}"><i class="fas fa-times-circle"
                                    style="margin-bottom: 2px;"></i>Bỏ Lọc</a>
                            <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 w-100 load_animation"
                                type="submit"><i class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Danh sách thiết bị --}}
        <form action="{{ route('equipments.index') }}" method="POST">
            @csrf
            <div class="card-body py-3">
                <div id="equipmentList">
                    @if ($AllEquipment->isEmpty() && request()->has('kw'))
                        {{-- Thông báo khi không tìm thấy kết quả tìm kiếm --}}
                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                            role="alert" style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                            <div class="mb-3">
                                <i class="fas fa-search" style="font-size: 36px; color: #6c757d;"></i>
                            </div>
                            <div class="text-center">
                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không tìm thấy kết quả phù
                                    hợp
                                </h5>
                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                    Vui lòng thử lại với từ khóa khác hoặc thay đổi bộ lọc tìm kiếm.
                                </p>
                            </div>
                        </div>
                    @elseif ($AllEquipment->isEmpty())
                        {{-- Thông báo khi danh sách trống mà không có tìm kiếm --}}
                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                            role="alert" style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                            <div class="mb-3">
                                <i class="fas fa-clipboard-check" style="font-size: 36px; color: #6c757d;"></i>
                            </div>
                            <div class="text-center">
                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Danh sách thiết bị trống</h5>
                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                    Hiện tại chưa có thiết bị nào được tạo. Vui lòng thêm mới thiết bị.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-end mb-3 me-3">
                            <label class="me-2 pointer" for="selectAll">Chọn Tất Cả</label>
                            <input type="checkbox" class="p-2 pointer" id="selectAll">
                        </div>
                        @foreach ($AllEquipment as $item)
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
                                                            @if ($item->inventories->sum('current_quantity') > 25)
                                                                <span
                                                                    class="fw-bolder text-white bg-success py-1 px-2 rounded-pill"
                                                                    style="font-size: 10px;">Còn Hàng</span>
                                                            @elseif ($item->inventories->sum('current_quantity') <= 25 && $item->inventories->sum('current_quantity') > 0)
                                                                <span
                                                                    class="fw-bolder bg-warning py-1 text-white px-2 rounded-pill"
                                                                    style="font-size: 10px;">Sắp Hết Hàng</span>
                                                            @else
                                                                <span
                                                                    class="fw-bolder text-white bg-danger py-1 px-2 rounded-pill"
                                                                    style="font-size: 10px;">Hết Hàng</span>
                                                            @endif
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
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <h6 class="card-title fw-bold">Chi Tiết</h6>
                                                            @if ($item->inventories->sum('current_quantity') > 25)
                                                                <span
                                                                    class="fw-bolder text-white bg-success py-1 px-2 rounded-pill"
                                                                    style="font-size: 10px;">Còn Hàng</span>
                                                            @elseif ($item->inventories->sum('current_quantity') <= 25 && $item->inventories->sum('current_quantity') > 0)
                                                                <span class="fw-bolder bg-warning py-1 px-2 rounded-pill"
                                                                    style="font-size: 10px;">Sắp Hết Hàng</span>
                                                            @else
                                                                <span
                                                                    class="fw-bolder text-white bg-danger py-1 px-2 rounded-pill"
                                                                    style="font-size: 10px;">Hết Hàng</span>
                                                            @endif
                                                        </div>
                                                        <!-- Chi tiết Thiết Bị -->
                                                        <div class="row mt-3">
                                                            <div class="col-md-7">
                                                                <table class="table table-borderless">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Mã:</strong></td>
                                                                            <td class="text-dark">#{{ $item->code }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Nhóm:</strong></td>
                                                                            <td class="text-dark">
                                                                                {{ $item->equipmentType->name ?? 'Không có dữ liệu' }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Nhà cung cấp:</strong></td>
                                                                            <td class="text-dark">
                                                                                {{ $item->supplier->name ?? 'Không có dữ liệu' }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Mã vạch:</strong></td>
                                                                            <td class="text-dark">
                                                                                @if ($item->barcode)
                                                                                    <img src="{{ asset('storage/' . $item->barcode) }}"
                                                                                        alt="Barcode" width="100%">
                                                                                    <div class="text-center mt-2">
                                                                                        {{ $item->code }}
                                                                                    </div>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <table class="table table-borderless">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Giá:</strong></td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($item->price) }} VNĐ</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Đơn vị:</strong></td>
                                                                            <td class="text-dark">
                                                                                {{ $item->units->name }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Mô tả:</strong></td>
                                                                            <td class="text-dark">
                                                                                {{ $item->description }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Ngày hết hạn:</strong></td>
                                                                            <td class="text-dark">
                                                                                {{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d/m/Y') : 'Không Có' }}
                                                                                {{ $item->time_remaining ? '- ' . $item->time_remaining : '' }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="text-end mt-4">
                                                            <div class="button-group">
                                                                <a href="{{ route('equipments.update_equipments', $item->code) }}"
                                                                    class="btn btn-sm btn-success me-2 rounded-pill">
                                                                    <i class="fa fa-edit mb-1"></i>Cập Nhật
                                                                </a>

                                                                @php
                                                                    $linkedCheck = \App\Models\Import_equipment_request_details::where(
                                                                        'equipment_code',
                                                                        $item->code,
                                                                    )->count();
                                                                @endphp

                                                                @if ($linkedCheck == 0)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger rounded-pill"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteConfirm{{ $item->code }}">
                                                                        <i class="fa fa-trash"
                                                                            style="margin-bottom: 2px;"></i> Xóa
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-secondary rounded-pill"
                                                                        disabled
                                                                        title="Thiết bị này tồn tại trong giao dịch của hệ thống.">
                                                                        <i class="fa fa-lock"
                                                                            style="margin-bottom: 2px;"></i> Xóa
                                                                    </button>
                                                                @endif
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

            @if ($AllEquipment->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $AllEquipment->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

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
        @foreach ($AllEquipment as $item)
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
                            <form action="{{ route('equipments.delete_equipments', $item->code) }}" method="POST">
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
@endsection

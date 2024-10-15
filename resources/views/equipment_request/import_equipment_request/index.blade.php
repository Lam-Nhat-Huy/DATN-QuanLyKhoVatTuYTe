@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Yêu Cầu Mua Hàng</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipment_request.import_trash') }}"
                    class="btn btn-sm rounded-pill btn-danger me-2 rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng Rác
                    </span>
                </a>
                <a href="{{ route('equipment_request.create_import') }}" class="btn btn-success btn-sm rounded-pill">
                    <i class="fa fa-plus me-1"></i>Tạo Phiếu
                </a>
            </div>
        </div>
        <div class="card-body py-1">
            <form action="{{ route('equipment_request.import') }}" class="row align-items-center">
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <select name="spr" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                        <option value="" selected>--Theo Nhà Cung Cấp--</option>
                        @foreach ($AllSuppiler as $item)
                            <option value="{{ $item->code }}" {{ request()->spr == $item->code ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12">
                    <select name="us" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                        <option value="" selected>--Theo Người Tạo--</option>
                        @foreach ($AllUser as $item)
                            <option value="{{ $item->code }}" {{ request()->us == $item->code ? 'selected' : '' }}>
                                {{ $item->last_name . ' ' . $item->first_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12">
                    <select name="stt" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                        <option value="" {{ request()->stt == '' ? 'selected' : '' }}>--Theo Trạng Thái--</option>
                        <option value="0" {{ request()->stt == '0' ? 'selected' : '' }}>Chờ Duyệt</option>
                        <option value="1" {{ request()->stt == '1' ? 'selected' : '' }}>Đang Nhập Kho</option>
                        <option value="2" {{ request()->stt == '2' ? 'selected' : '' }}>Hết Hạn</option>
                        <option value="3" {{ request()->stt == '3' ? 'selected' : '' }}>Lưu Tạm</option>
                        <option value="4" {{ request()->stt == '4' ? 'selected' : '' }}>Hoàn Thành</option>
                    </select>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <input type="search" name="kw" placeholder="Tìm kiếm mã phiếu nhập.."
                                class="mt-2 mb-2 form-control form-control-sm form-control-solid rounded-pill border border-success w-100"
                                value="{{ request()->kw }}">
                        </div>
                        <div class="col-md-6 d-flex">
                            <a class="btn rounded-pill btn-info btn-sm mt-2 mb-2 w-100 me-2"
                                href="{{ route('equipment_request.import') }}"><i class="fas fa-times-circle"
                                    style="margin-bottom: 2px;"></i> Bỏ
                                Lọc</a>
                            <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 w-100 load_animation"
                                type="submit"><i class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <form action="{{ route('equipment_request.import') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $AllEquipmentRequest->count() == 0 ? 'd-none' : '' }}">
                            <tr class="bg-success">
                                <th class="ps-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="" style="width: 10%;">Mã Yêu Cầu</th>
                                <th class="" style="width: 43%;">Nhà Cung Cấp</th>
                                <th class="" style="width: 12%;">Người Tạo</th>
                                <th class="" style="width: 10%;">Ngày Yêu Cầu</th>
                                <th class="text-center" style="width: 10%;">Trạng Thái</th>
                                <th class="pe-3 text-center" style="width: 15%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($AllEquipmentRequest as $item)
                                <tr class="hover-table pointer">
                                    <td>
                                        {{-- Phiếu tạm => ẩn hết, phiếu chờ duyệt thì hiện, phiếu đã duyệt chưa tạo thì hiện icon, phiếu đã duyệt tạo rồi thì ẩn --}}
                                        @if (($item->status == 3 || $item->status == 0) && $item->user_code == session('user_code'))
                                            <input type="checkbox" name="import_reqest_codes[]" value="{{ $item->code }}"
                                                class="row-checkbox" />
                                        @elseif ($item->status == 4)
                                            <i class="fa fa-check text-success" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Hoàn Thành"></i>
                                        @elseif ($item->status == 1)
                                            <i class="fa-solid fa-circle-exclamation text-danger" style="font-size: 13px;"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Phiếu Yêu Cầu Chưa Được Nhập Kho"></i>
                                        @endif
                                    </td>
                                    <td>
                                        #{{ $item->code }}
                                    </td>
                                    <td>
                                        {{ $item->suppliers->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $item->users->last_name . ' ' . $item->users->first_name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->request_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if (($item->status == 0 || $item->status == 3) && now()->gt(\Carbon\Carbon::parse($item->request_date)->addDays(3)))
                                            <div class="rounded-pill px-2 py-1 text-dark bg-warning">
                                                Hết Hạn
                                            </div>
                                        @elseif ($item->status == 3)
                                            <div class="rounded-pill px-2 py-1 text-white bg-info">
                                                Lưu Tạm
                                            </div>
                                        @elseif ($item->status == 0)
                                            <div class="rounded-pill px-2 py-1 text-white bg-danger">
                                                Chờ Duyệt
                                            </div>
                                        @elseif ($item->status == 1)
                                            <div class="rounded-pill px-2 py-1 text-white bg-primary">
                                                Đang Nhập
                                            </div>
                                        @elseif ($item->status == 4)
                                            <div class="rounded-pill px-2 py-1 text-white bg-success">
                                                Hoàn Thành
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center" data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $item->code }}" aria-expanded="false"
                                        aria-controls="collapse_{{ $item->code }}">
                                        Chi Tiết<i class="fa fa-caret-right pointer ms-2"></i>
                                    </td>
                                </tr>

                                <!-- Collapse content -->
                                <tr>
                                    <td class="p-0" colspan="12"
                                        style="background-color: #fafafa; padding-top: 0 !important;">
                                        <div class="flex-lg-row-fluid border-2 border-lg-1 collapse multi-collapse"
                                            id="collapse_{{ $item->code }}">
                                            <div class="flex-lg-row-fluid border-lg-1">
                                                <div class="card card-flush px-5" style="padding-top: 0px !important;">
                                                    <div class="card-header d-flex justify-content-between align-items-center px-2"
                                                        style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                        <h4 class="fw-bold m-0 text-uppercase fw-bolder">Danh Sách
                                                            Thiết Bị
                                                            Yêu
                                                            Cầu
                                                        </h4>
                                                        <div class="card-toolbar">
                                                            @if (($item->status == 0 || $item->status == 3) && now()->gt(\Carbon\Carbon::parse($item->request_date)->addDays(3)))
                                                                <div class="rounded-pill px-2 py-1 text-dark bg-warning">
                                                                    Hết Hạn
                                                                </div>
                                                            @elseif ($item->status == 3)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-info">
                                                                    Lưu Tạm
                                                                </div>
                                                            @elseif ($item->status == 0)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-danger">
                                                                    Chờ Duyệt
                                                                </div>
                                                            @elseif ($item->status == 1)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-primary">
                                                                    Đang Nhập
                                                                </div>
                                                            @elseif ($item->status == 4)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-success">
                                                                    Hoàn Thành
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-0" style="padding-top: 0px !important">
                                                        <!-- Begin::Receipt Items (Right column) -->
                                                        <div class="col-md-12">
                                                            <div class="table-responsive rounded">
                                                                <table
                                                                    class="table table-striped table-sm table-hover mb-0">
                                                                    <thead class="bg-dark">
                                                                        <tr class="text-center">
                                                                            <th class="ps-3">STT</th>
                                                                            <th class="ps-3">Tên thiết bị</th>
                                                                            <th>Đơn Vị Tính</th>
                                                                            <th class="pe-3">Số lượng</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($item->import_equipment_request_details as $key => $detail)
                                                                            <tr class="text-center">
                                                                                <td>{{ $key + 1 }}</td>
                                                                                <td>{{ $detail->equipments->name }}
                                                                                </td>
                                                                                <td>{{ $detail->equipments->units->name }}
                                                                                </td>
                                                                                <td>{{ $detail->quantity }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body py-5 text-end bg-white">
                                                <div
                                                    class="button-group">
                                                    @if (($item->status == 0 || $item->status == 3) && now()->gt(\Carbon\Carbon::parse($item->request_date)->addDays(3)))
                                                        {{-- Quá hạn yêu cầu 3 ngày --}}
                                                        <!-- Nút Hủy đơn -->
                                                        <button class="btn btn-sm rounded-pill btn-danger me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal_{{ $item->code }}"
                                                            type="button">
                                                            <i class="fa fa-trash" style="margin-bottom: 2px;"></i>Hủy
                                                            Phiếu
                                                        </button>
                                                    @elseif ($item->status == 0 && now()->lt(\Carbon\Carbon::parse($item->request_date)->addDays(3)))
                                                        {{-- Chưa duyệt và ngày yêu cầu trong 3 ngày gần đây --}}

                                                        <!-- Nút Duyệt đơn -->
                                                        <button class="btn btn-sm rounded-pill btn-twitter me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#browse_{{ $item->code }}" type="button">
                                                            <i class="fas fa-clipboard-check"
                                                                style="margin-bottom: 2px;"></i>Duyệt Phiếu
                                                        </button>

                                                        <!-- Nút Sửa đơn -->
                                                        <a href="{{ route('equipment_request.update_import', $item->code) }}"
                                                            class="btn btn-sm rounded-pill btn-dark me-2">
                                                            <i class="fa fa-edit" style="margin-bottom: 2px;"></i>Sửa
                                                            Phiếu
                                                        </a>

                                                        <!-- Nút Hủy đơn -->
                                                        <button class="btn btn-sm rounded-pill btn-danger me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal_{{ $item->code }}"
                                                            type="button">
                                                            <i class="fa fa-trash" style="margin-bottom: 2px;"></i>Hủy
                                                            Phiếu
                                                        </button>
                                                    @elseif (
                                                        $item->status == 3 &&
                                                            now()->lt(\Carbon\Carbon::parse($item->request_date)->addDays(3)) &&
                                                            $item->user_code == session('user_code'))
                                                        {{-- Lưu tạm và ngày yêu cầu trong 3 ngày gần nhất --}}

                                                        <!-- Nút lưu phiếu -->
                                                        <button class="btn btn-sm rounded-pill btn-twitter me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#save_{{ $item->code }}" type="button">
                                                            <i class="fa fa-save" style="margin-bottom: 2px;"></i>Tạo
                                                            Phiếu
                                                        </button>

                                                        <!-- Nút Sửa đơn -->
                                                        <a href="{{ route('equipment_request.update_import', $item->code) }}"
                                                            class="btn btn-sm rounded-pill btn-dark me-2">
                                                            <i class="fa fa-edit" style="margin-bottom: 2px;"></i>Sửa
                                                            Phiếu
                                                        </a>

                                                        <!-- Nút Hủy đơn -->
                                                        <button class="btn btn-sm rounded-pill btn-danger me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal_{{ $item->code }}"
                                                            type="button">
                                                            <i class="fa fa-trash" style="margin-bottom: 2px;"></i>Hủy
                                                            Phiếu
                                                        </button>
                                                    @elseif ($item->status == 1)
                                                        {{-- Đang chuẩn bị --}}
                                                        <!-- Nút Tạo Phiếu Nhập -->
                                                        <a href="{{ route('warehouse.create_import') }}?cd={{ $item->code }}"
                                                            class="btn btn-sm rounded-pill btn-dark me-2">
                                                            <i class="fas fa-file-import" style="margin-bottom: 2px;"></i>
                                                            Tạo Phiếu Nhập
                                                        </a>

                                                        <!-- Nút In Phiếu -->
                                                        <button class="btn btn-sm rounded-pill btn-twitter me-2"
                                                            onclick="printInvoice('{{ $item->code }}')" type="button">
                                                            <i class="fa fa-print" style="margin-bottom: 2px;"></i> In
                                                            Phiếu
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- In --}}
                                            <div class="fade modal" id="printArea_{{ $item->code }}">
                                                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                                    <div class="d-flex mb-5">
                                                        <img src="{{ asset('image/logo_warehouse.png') }}" width="100"
                                                            alt="">
                                                        <div class="text-left mt-3">
                                                            <h6 class="mb-0 pb-0">BỆNH VIỆN ĐA KHOA BEESOFT</h6>
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
                                                            </div>
                                                            <div class="text-muted fs-30">
                                                                Ngày Lập
                                                                {{ \Carbon\Carbon::parse($item->request_date)->format('d-m-Y') }}
                                                            </div>
                                                        </div>
                                                        <div class="mb-15 text-left">
                                                            <!-- Begin::Receipt Info -->
                                                            <div class="mb-4">
                                                                <div class="pt-2">
                                                                    <p><strong>Người Giao:</strong>
                                                                        .................................................................................................................................................................................
                                                                    </p>
                                                                    <p><strong>Theo Số Hóa Đơn:</strong>
                                                                        <span id="modalSupplier">
                                                                            {{ $item->code }}
                                                                        </span>
                                                                    </p>
                                                                    <h6><span id="modalSupplier"
                                                                            style="line-height: 1.6;">
                                                                            Công Ty <span
                                                                                class="text-success">BeeSoft</span>
                                                                            Có Nhu Cầu Đặt Mua Thiết Bị Tại
                                                                            <span
                                                                                class="text-danger">{{ $item->suppliers->name }}</span>
                                                                            theo mẫu yêu
                                                                            cầu như sau:
                                                                        </span>
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                            <!-- End::Receipt Info -->

                                                            <!-- Begin::Receipt Items -->
                                                            <div class="mb-4 mt-3">
                                                                <h4 class="text-primary mb-3">
                                                                    Danh Sách Thiết Bị
                                                                </h4>
                                                                <div class="table-responsive rounded">
                                                                    <table
                                                                        class="table border border-dark align-middle gs-0 gy-4">
                                                                        <thead>
                                                                            <tr
                                                                                class=" bg-success border border-dark text-center">
                                                                                <th style="width: 5%;"
                                                                                    class="ps-3 text-dark">
                                                                                    STT
                                                                                </th>
                                                                                <th style="width: 35%;" class="text-dark">
                                                                                    Thiết Bị
                                                                                </th>
                                                                                <th style="width: 15%;" class="text-dark">
                                                                                    Đơn
                                                                                    Vị
                                                                                </th>
                                                                                <th style="width: 15%;" class="text-dark">
                                                                                    Số
                                                                                    Lượng
                                                                                </th>
                                                                                <th style="width: 15%;" class="text-dark">
                                                                                    Đơn
                                                                                    giá
                                                                                </th>
                                                                                <th class="pe-3 text-dark"
                                                                                    style="width: 15%;">
                                                                                    Thành tiền
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($item->import_equipment_request_details as $key => $detail_in)
                                                                                <tr class="border border-dark">
                                                                                    <td class="ps-3 text-right">
                                                                                        {{ $key + 1 }}
                                                                                    </td>
                                                                                    <td class="text-left">
                                                                                        {{ $detail_in->equipments->name }}
                                                                                    </td>
                                                                                    <td class="text-left">
                                                                                        {{ $detail_in->equipments->units->name }}
                                                                                    </td>
                                                                                    <td class="pe-3 text-right">
                                                                                        {{ $detail_in->quantity }}
                                                                                    </td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                </tr>
                                                                            @endforeach
                                                                            <tr class=" border border-dark">
                                                                                <td colspan="4">
                                                                                </td>
                                                                                <td colspan="1"
                                                                                    style="height: 30px; min-height: 30px;">
                                                                                </td>
                                                                                <td colspan="1"
                                                                                    style="height: 30px; min-height: 30px;">
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div>
                                                                    <p><strong>Ghi Chú:
                                                                        </strong><span>{{ $item->note }}</span>
                                                                    </p>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-8"></div>
                                                                    <div class="col-4">
                                                                        <p class="m-0 p-0">
                                                                            Cần Thơ, ngày
                                                                            {{ \Carbon\Carbon::now()->day }}
                                                                            tháng
                                                                            {{ \Carbon\Carbon::now()->month }} năm
                                                                            {{ \Carbon\Carbon::now()->year }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-1"></div>
                                                                    <div class="col-4">
                                                                        <p class="m-0 p-0">
                                                                            <strong>Người Lập Phiếu</strong>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <p class="m-0 p-0">
                                                                            <strong>Người Giao Hàng</strong>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-4 text-center">
                                                                        <p class="m-0 p-0">
                                                                            <strong>Trưởng Bộ Phận</strong>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
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
                                                <i class="fas fa-search" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không Có Dữ
                                                    Liệu</h5>
                                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                    Không Có Dữ Liệu Nào Về Phiếu Yêu Cầu Mua Hàng
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

            @if ($AllEquipmentRequest->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <button class="btn btn-info btn-sm dropdown-toggle rounded-pill" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </button>
                        <ul class="dropdown-menu shadow" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer d-flex align-items-center" data-bs-toggle="modal"
                                    data-bs-target="#browseAll">
                                    <i class="fas fa-clipboard-check me-2 text-twitter"></i>
                                    <span>Duyệt phiếu</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item pointer d-flex align-items-center" data-bs-toggle="modal"
                                    data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>
                                    <span class="text-danger">Hủy phiếu</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $AllEquipmentRequest->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Duyệt Tất Cả --}}
            <div class="modal fade" id="browseAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="browseAllModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="browseAllModal">Duyệt Yêu Cầu Mua Hàng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn duyệt tất cả yêu cầu mua hàng đã chọn?
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary btn-sm px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">
                                Duyệt</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Xác Nhận Hủy Tất Cả --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Hủy yêu cầu mua hàng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn hủy tất cả yêu cầu mua hàng đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Hủy</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @foreach ($AllEquipmentRequest as $item)
        <!-- Modal Duyệt Yêu Cầu Mua Hàng -->
        <div class="modal fade" id="browse_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="checkModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="checkModalLabel">Duyệt
                            Yêu Cầu Mua Hàng</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.import') }}" id="form-3" method="POST">
                        @csrf
                        <input type="hidden" name="browse_request" value="{{ $item->code }}">
                        <div class="modal-body text-center pb-0">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn duyệt yêu cầu mua hàng này?
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Duyệt</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Hủy --}}
        <div class="modal fade" id="deleteModal_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModalLabel">Hủy Yêu Cầu Mua Hàng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.import') }}" id="form-4" method="POST">
                        @csrf
                        <input type="hidden" name="delete_request" value="{{ $item->code }}">
                        <div class="modal-body pb-0 text-center">
                            <p class="text-danger mb-4">Hủy Yêu Cầu Mua Hàng Này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Lưu phiếu --}}
        <div class="modal fade" id="save_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="saveModalLabel">Tạo Phiếu Yêu Cầu
                            Mua
                            Hàng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.import') }}" id="form-4" method="POST">
                        @csrf
                        <input type="hidden" name="save_status" value="{{ $item->code }}">
                        <div class="modal-body pb-0 text-center">
                            <p class="text-primary mb-4">Tạo Phiếu Yêu Cầu Mua Hàng
                                Này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Tạo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

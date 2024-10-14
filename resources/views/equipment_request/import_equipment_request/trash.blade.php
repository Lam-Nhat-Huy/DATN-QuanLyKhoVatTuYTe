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
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipment_request.import') }}" class="btn rounded-pill btn-sm btn-dark me-2 rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <form action="{{ route('equipment_request.import_trash') }}" id="form-1" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $AllEquipmentRequestTrash->count() == 0 ? 'd-none' : '' }}">
                            <tr class="bg-success">
                                <th class="ps-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="" style="width: 10%;">Mã Yêu Cầu</th>
                                <th class="" style="width: 45%;">Nhà Cung Cấp</th>
                                <th class="" style="width: 10%;">Người Tạo</th>
                                <th class="" style="width: 10%;">Ngày Yêu Cầu</th>
                                <th class="text-center" style="width: 10%;">Trạng Thái</th>
                                <th class="pe-3 text-center" style="width: 15%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($AllEquipmentRequestTrash as $item)
                                @if ($item->status == 3 && $item->user_code != session('user_code'))
                                    <tr class="hover-table pointer">
                                        <td>
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
                                            @if ($item['status'] == 3)
                                                <div class="label label-temp bg-info rounded-pill text-white px-2 py-1">
                                                    Lưu Tạm
                                                </div>
                                            @elseif ($item->status == 0)
                                                <div class="label label-temp bg-danger rounded-pill text-white px-2 py-1">
                                                    Chờ Duyệt
                                                </div>
                                            @elseif ($item->status == 1)
                                                <div class="label label-final bg-success rounded-pill text-white px-2 py-1">
                                                    Đã duyệt
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
                                                <div class="card card-flush px-5" style="padding-top: 0px !important;">
                                                    <div class="card-header d-flex justify-content-between align-items-center px-2"
                                                        style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                        <h4 class="fw-bold m-0 text-uppercase fw-bolder">Danh Sách Thiết Bị
                                                            Yêu
                                                            Cầu
                                                        </h4>
                                                        <div class="card-toolbar">
                                                            @if (($item->status == 0 || $item->status == 3) && \Carbon\Carbon::parse($item->request_date)->diffInDays(now()) > 3)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-warning">
                                                                    Hết Hạn
                                                                </div>
                                                            @elseif ($item->status == 3)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-info">Lưu
                                                                    Tạm
                                                                </div>
                                                            @elseif ($item->status == 0)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-danger">Chờ
                                                                    Duyệt
                                                                </div>
                                                            @elseif ($item->status == 1)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-success">Đã
                                                                    Duyệt
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body px-0" style="padding-top: 0px !important">
                                                        <!-- Begin::Receipt Items (Right column) -->
                                                        <div class="col-md-12">
                                                            <div class="table-responsive rounded">
                                                                <table
                                                                    class="table table-striped table-sm table-hover mb-0">
                                                                    <thead class=" bg-danger">
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
                                                                                <td>{{ $detail->equipments->name }}</td>
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
                                        </td>
                                    </tr>
                                @else
                                    <tr class="hover-table pointer">
                                        <td>
                                            <input type="checkbox" name="import_reqest_codes[]" value="{{ $item->code }}"
                                                class="row-checkbox" />
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
                                            @if ($item['status'] == 3)
                                                <div class="label label-temp bg-info rounded-pill text-white px-2 py-1">
                                                    Lưu Tạm
                                                </div>
                                            @elseif ($item->status == 0)
                                                <div class="label label-temp bg-danger rounded-pill text-white px-2 py-1">
                                                    Chờ Duyệt
                                                </div>
                                            @elseif ($item->status == 1)
                                                <div class="label label-final bg-success rounded-pill text-white px-2 py-1">
                                                    Đã duyệt
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
                                            <div class="border-2 border-lg-1 collapse multi-collapse"
                                                id="collapse_{{ $item->code }}">
                                                <div class="flex-lg-row-fluid">
                                                    <div class="card card-flush px-5" style="padding-top: 0px !important;">
                                                        <div class="card-header d-flex justify-content-between align-items-center px-2"
                                                            style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                            <h4 class="fw-bold m-0 text-uppercase fw-bolder">Danh Sách Thiết
                                                                Bị
                                                                Yêu Cầu
                                                            </h4>
                                                            <div class="card-toolbar">
                                                                @if (($item->status == 0 || $item->status == 3) && \Carbon\Carbon::parse($item->request_date)->diffInDays(now()) > 3)
                                                                    <div
                                                                        class="rounded-pill px-2 py-1 text-white bg-warning">
                                                                        Hết
                                                                        Hạn
                                                                    </div>
                                                                @elseif ($item->status == 3)
                                                                    <div class="rounded-pill px-2 py-1 text-white bg-info">
                                                                        Lưu
                                                                        Tạm
                                                                    </div>
                                                                @elseif ($item->status == 0)
                                                                    <div
                                                                        class="rounded-pill px-2 py-1 text-white bg-danger">
                                                                        Chờ
                                                                        Duyệt
                                                                    </div>
                                                                @elseif ($item->status == 1)
                                                                    <div
                                                                        class="rounded-pill px-2 py-1 text-white bg-success">
                                                                        Đã
                                                                        Duyệt
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
                                                                        <thead class="fw-bolder bg-danger">
                                                                            <tr>
                                                                                <th class="ps-3">STT</th>
                                                                                <th class="ps-3">Tên thiết bị</th>
                                                                                <th>Đơn Vị Tính</th>
                                                                                <th class="pe-3">Số lượng</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($item->import_equipment_request_details as $key => $detail)
                                                                                <tr class="">
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
                                                    <div class="button-group">
                                                        <!-- Nút khôi phục đơn -->
                                                        <button
                                                            class="btn rounded-pill btn-sm btn-twitter me-2 rounded-pill"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#restore_{{ $item->code }}" type="button">
                                                            <i class="fas fa-rotate-right"></i>Khôi Phục
                                                        </button>

                                                        <!-- Nút xóa vv đơn -->
                                                        <button
                                                            class="btn rounded-pill btn-sm btn-danger me-2 rounded-pill"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal_{{ $item->code }}"
                                                            type="button">
                                                            <i class="fa fa-trash"></i>Xóa
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr id="noDataAlert">
                                    <td colspan="12" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fa-regular fa-trash-can"
                                                    style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thùng Rác
                                                    Rỗng</h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($AllEquipmentRequestTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fas fa-rotate-right me-2 text-twitter"></i>Khôi Phục</a>
                            </li>
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa</a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $AllEquipmentRequestTrash->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Khôi Phục Tất Cả --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllModal">Khôi Phục Yêu Cầu Mua Hàng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn khôi phục yêu cầu mua hàng đã chọn?
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary btn-sm px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">
                                Khôi Phục</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Xác Nhận Xóa Vĩnh Viễn Tất Cả --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Vĩnh Viễn yêu cầu mua hàng
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa yêu cầu mua hàng đã chọn?</p>
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

    @foreach ($AllEquipmentRequestTrash as $item)
        <!-- Modal Duyệt Yêu Cầu Mua Hàng -->
        <div class="modal fade" id="restore_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="checkModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="checkModalLabel">Khôi Phục
                            Yêu Cầu Mua
                            Hàng</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.import_trash') }}" method="POST">
                        @csrf
                        <input type="hidden" name="restore_request" value="{{ $item->code }}">
                        <div class="modal-body text-center pb-0">
                            <p class="text-primary mb-4">Khôi Phục Yêu Cầu Mua Hàng
                                Này?
                            </p>
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
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Vĩnh
                            Viễn Yêu
                            Cầu Mua Hàng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.import_trash') }}" method="POST">
                        @csrf
                        <input type="hidden" name="delete_request" value="{{ $item->code }}">
                        <div class="modal-body pb-0 text-center">
                            <p class="text-danger mb-4">Xóa Vĩnh Viễn Yêu Cầu Mua Hàng Này?
                            </p>
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

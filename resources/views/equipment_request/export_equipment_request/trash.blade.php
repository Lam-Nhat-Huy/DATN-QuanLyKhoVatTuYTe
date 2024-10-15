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
                <a href="{{ route('equipment_request.export') }}" class="btn btn-success btn-sm rounded-pill">
                    <i class="fa fa-arrow-left me-1"></i>Trở Lại
                </a>
            </div>
        </div>
        <form action="{{ route('equipment_request.export_trash') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $AllWarehouseExportRequestTrash->count() == 0 ? 'd-none' : '' }}">
                            <tr class="bg-success">
                                <th class="ps-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="" style="width: 10%;">Mã Yêu Cầu</th>
                                <th class="" style="width: 13%;">Phòng Ban</th>
                                <th class="" style="width: 13%;">Lý Do Xuất</th>
                                <th class="" style="width: 15%;">Người Tạo</th>
                                <th class="" style="width: 12%;">Ngày Yêu Cầu</th>
                                <th class="" style="width: 12%;">Ngày Cần Thiết</th>
                                <th class="text-center" style="width: 10%;">Trạng Thái</th>
                                <th class="pe-3 text-center" style="width: 25%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($AllWarehouseExportRequestTrash as $item)
                                <tr class="hover-table pointer">
                                    <td>
                                        {{-- Phiếu tạm => ẩn hết, phiếu chờ duyệt thì hiện, phiếu đã duyệt chưa tạo thì hiện icon, phiếu đã duyệt tạo rồi thì ẩn --}}
                                        @if (($item->status == 3 || $item->status == 0) && $item->user_code == session('user_code'))
                                            <input type="checkbox" name="export_reqest_codes[]" value="{{ $item->code }}"
                                                class="row-checkbox" />
                                        @elseif ($item->status == 5)
                                            <i class="fa fa-truck-medical" title="Đang vận chuyển"></i>
                                        @elseif ($item->status == 4)
                                            <i class="fa fa-check" title="Hoàn Thành"></i>
                                        @elseif ($item->status == 1)
                                            <i class="fa-solid fa-circle-exclamation"
                                                title="Phiếu Yêu Cầu Xuất Chưa Được Vận Chuyển" style="font-size: 13px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#exclamation_{{ $item->code }}"></i>
                                        @endif
                                    </td>
                                    <td>
                                        #{{ $item->code }}
                                    </td>
                                    <td>
                                        {{ $item->departments->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $item->reason_export }}
                                    </td>
                                    <td>
                                        {{ $item->users->last_name . ' ' . $item->users->first_name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->request_date)->format('d-m-Y') }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->required_date)->format('d-m-Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if (($item->status == 0 || $item->status == 3) && now()->gt(\Carbon\Carbon::parse($item->required_date)))
                                            <div class="label label-temp bg-warning rounded-pill text-dark px-2 py-1">
                                                Hết Hạn
                                            </div>
                                        @elseif ($item->status == 3)
                                            <div class="label label-temp bg-info rounded-pill text-white px-2 py-1">
                                                Lưu Tạm
                                            </div>
                                        @elseif ($item->status == 0)
                                            <div class="label label-temp bg-danger rounded-pill text-white px-2 py-1">
                                                Chờ Duyệt
                                            </div>
                                        @elseif ($item->status == 1)
                                            <div class="label label-temp bg-primary rounded-pill text-white px-2 py-1">
                                                Chuẩn Bị
                                            </div>
                                        @elseif ($item->status == 5)
                                            <div class="label label-temp bg-dark rounded-pill text-white px-2 py-1">
                                                Vận Chuyển
                                            </div>
                                        @elseif ($item->status == 4)
                                            <div class="label label-temp bg-success rounded-pill text-white px-2 py-1">
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
                                                        <h4 class="fw-bold m-0 text-uppercase fw-bolder">
                                                            Danh Sách Thiết Bị Yêu Cầu
                                                        </h4>
                                                        <div class="card-toolbar">
                                                            @if (($item->status == 0 || $item->status == 3) && now()->gt(\Carbon\Carbon::parse($item->required_date)))
                                                                <div
                                                                    class="label label-temp bg-warning rounded-pill text-dark px-2 py-1">
                                                                    Hết Hạn
                                                                </div>
                                                            @elseif ($item->status == 3)
                                                                <div
                                                                    class="label label-temp bg-info rounded-pill text-white px-2 py-1">
                                                                    Lưu Tạm
                                                                </div>
                                                            @elseif ($item->status == 0)
                                                                <div
                                                                    class="label label-temp bg-danger rounded-pill text-white px-2 py-1">
                                                                    Chờ Duyệt
                                                                </div>
                                                            @elseif ($item->status == 1)
                                                                <div
                                                                    class="label label-temp bg-primary rounded-pill text-white px-2 py-1">
                                                                    Chuẩn Bị
                                                                </div>
                                                            @elseif ($item->status == 5)
                                                                <div
                                                                    class="label label-temp bg-dark rounded-pill text-white px-2 py-1">
                                                                    Vận Chuyển
                                                                </div>
                                                            @elseif ($item->status == 4)
                                                                <div
                                                                    class="label label-temp bg-success rounded-pill text-white px-2 py-1">
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
                                                                        @foreach ($item->export_equipment_request_details as $key => $detail)
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
                                                    class="button-group {{ $item->status == 3 && $item->user_code != session('user_code') ? 'd-none' : '' }}">
                                                    <!-- Nút khôi phục -->
                                                    <button class="btn btn-sm rounded-pill btn-twitter me-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#restore_{{ $item->code }}" type="button">
                                                        <i class="fa fa-rotate-right" style="margin-bottom: 2px;"></i>
                                                        Khôi phục
                                                    </button>

                                                    <!-- Nút xóa vĩnh viễn đơn -->
                                                    <button class="btn btn-sm rounded-pill btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal_{{ $item->code }}" type="button">
                                                        <i class="fa fa-trash" style="margin-bottom: 2px;"></i>
                                                        Xóa vĩnh viễn
                                                    </button>
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
                                                <i class="fa-regular fa-trash-can"
                                                    style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">
                                                    Thùng Rác Rỗng
                                                </h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($AllWarehouseExportRequestTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <button class="btn btn-info btn-sm dropdown-toggle rounded-pill" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </button>
                        <ul class="dropdown-menu shadow" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer d-flex align-items-center" data-bs-toggle="modal"
                                    data-bs-target="#restoreAll">
                                    <i class="fas fa-rotate-right me-2 text-twitter"></i>
                                    <span>Khôi Phục</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item pointer d-flex align-items-center" data-bs-toggle="modal"
                                    data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>
                                    <span class="text-danger">Xóa Vĩnh Viễn</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $AllWarehouseExportRequestTrash->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Duyệt Tất Cả --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllModal">Khôi Phục Yêu Cầu Xuất Kho</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục tất cả yêu cầu xuất kho đã chọn?
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

            {{-- Modal Xác Nhận xóa vĩnh viễn --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác nhận xóa vĩnh viễn yêu cầu xuất kho
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa vĩnh viễn tất cả yêu cầu xuất kho đã
                                chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa vĩnh
                                viễn</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @foreach ($AllWarehouseExportRequestTrash as $item)
        <!-- Modal Khôi Phục Yêu Cầu Xuất Kho -->
        <div class="modal fade" id="restore_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="checkModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="checkModalLabel">Khôi Phục
                            Yêu Cầu Xuất Kho</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.export_trash') }}" id="form-3" method="POST">
                        @csrf
                        <input type="hidden" name="restore_request" value="{{ $item->code }}">
                        <div class="modal-body text-center pb-0">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục yêu cầu xuất kho này?
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

        {{-- Xóa vĩnh viễn --}}
        <div class="modal fade" id="deleteModal_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Vĩnh Viễn Yêu Cầu Xuất Kho
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipment_request.export_trash') }}" id="form-4" method="POST">
                        @csrf
                        <input type="hidden" name="delete_request" value="{{ $item->code }}">
                        <div class="modal-body pb-0 text-center">
                            <p class="text-danger mb-4">Xóa vĩnh viễn Yêu Cầu Xuất Kho Này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa Vĩnh
                                Viễn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Thông Báo Phiếu Yc Chưa Tạo Xuất --}}
        <div class="modal fade" id="exclamation_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="xclamationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-warning" id="xclamationModalLabel"><i
                                class="fa-solid fa-circle-exclamation text-warning"></i> Lưu ý
                        </h5>
                    </div>
                    <div class="modal-body pb-0 text-center">
                        <p class="text-dark mb-4">Phiếu Yêu Cầu Xuất Kho Này Chưa Được Vận Chuyển</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                            data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

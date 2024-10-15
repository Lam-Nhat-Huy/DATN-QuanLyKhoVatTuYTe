@extends('master_layout.layout')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .hover-table:hover {
            background: #ccc;
        }

        .btn-group button {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .active-row {
            background: #d1c4e9;
            /* Màu nền khi hàng được nhấp vào */
        }

        .select2-selection__rendered {
            color: #000 !important;
        }

        .selected-row {
            background: #ccc;
        }

        .batch-row:hover {
            background: green !important;
        }

        .expired>td {
            color: red;
        }
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Xuất Kho</span>
            </h3>

            <div class="card-toolbar">

                <a href="{{ route('warehouse.create_export') }}" style="font-size: 10px;"
                    class="btn btn-sm btn-success rounded-pill">
                    <i style="font-size: 10px;" class="fas fa-plus"></i>Tạo Phiếu Xuất</a>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <div class="card-body py-1">
            <form id="filterForm" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <div class="col-5 pe-0">
                            <input type="date" name="start_date"
                                class="form-control form-control-sm form-control-solid bg-white border-success rounded-pill"
                                value="{{ \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d') }}">
                        </div>
                        <div class="col-2 text-center">Đến</div>
                        <div class="col-5 ps-0">
                            <input type="date" name="end_date"
                                class="form-control form-control-sm form-control-solid bg-white border-success rounded-pill"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-2">

                </div>

                <div class="col-md-2">

                </div>


                <div class="col-md-4">
                    <div class="input-group">
                        <input type="search" id="search" name="search" placeholder="Tìm Kiếm Mã, Số Hóa Đơn.."
                            class="form-control form-control-sm form-control-solid border-success bg-white rounded-pill">
                    </div>
                </div>

                <div id="searchResults"></div>
            </form>

        </div>

        {{-- Danh sách phiếu  --}}
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-center">
                    <thead>
                        <tr class="bg-success text-white">
                            <th class="ps-3">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th class="ps-3">Mã Phiếu Xuất</th>
                            <th class="">Ngày Xuất</th>
                            <th class="">Tạo bởi</th>
                            <th class="pe-3">Lý Do Xuất</th>
                            <th class="">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exports as $export)
                            <tr class="text-center hover-table pointer bg-white" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $export->code }}" aria-expanded="false"
                                aria-controls="collapse{{ $export->code }}">
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" />
                                </td>
                                <td class="text-center">{{ $export->code }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($export->export_date)->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    {{ $export->creator ? $export->creator->last_name . ' ' . $export->creator->first_name : 'Không có' }}
                                </td>
                                <td class="text-center">{{ $export->note ?? 'Không có' }}</td>
                                <td class="text-center">
                                    @if ($export->status < 1)
                                        <span class="badge bg-danger" style="font-size: 10px;">Chưa Duyệt</span>
                                    @else
                                        <span class="badge bg-success" style="font-size: 10px;">Đã Duyệt</span>
                                    @endif
                                </td>
                            </tr>

                            <tr class="collapse multi-collapse" id="collapse{{ $export->code }}">
                                <td class="p-0" colspan="12"
                                    style="border: 1px solid #dcdcdc; background-color: #fafafa;">
                                    <div class="flex-lg-row-fluid border-2 border-lg-1">
                                        <div class="card card-flush p-2 mb-3">
                                            <div class="card-header d-flex justify-content-between align-items-center p-2">
                                                <h4 class="fw-bold m-0">Chi tiết phiếu xuất kho</h4>
                                                <span class="badge {{ $export->status < 1 ? 'bg-danger' : 'bg-success' }}"
                                                    style="font-size: 10px;">
                                                    {{ $export->status < 1 ? 'Chưa Duyệt' : 'Đã Duyệt' }}
                                                </span>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-sm table-hover">
                                                            <thead class="fw-bolder bg-danger text-white">
                                                                <tr>
                                                                    <th class="ps-3">Mã vật tư</th>
                                                                    <th>Tên vật tư</th>
                                                                    <th>Số lô</th>
                                                                    <th>Số lượng</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($export->exportDetail as $detail)
                                                                    <tr>
                                                                        <td>{{ $detail->equipment_code }}</td>
                                                                        <td>{{ $detail->equipments->name ?? 'Không có' }}
                                                                        </td>
                                                                        <td>{{ $detail->batch_number }}</td>
                                                                        <td>{{ $detail->quantity }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body py-3 text-end">
                                            <div class="button-group">
                                                @if ($export->status == 0)
                                                    <button style="font-size: 10px;" class="btn btn-sm btn-success me-2"
                                                        data-bs-toggle="modal" data-bs-target="#browse{{ $export->code }}"
                                                        type="button">
                                                        <i style="font-size: 10px;" class="fas fa-clipboard-check"></i>Duyệt
                                                        Phiếu
                                                    </button>
                                                    <button style="font-size: 10px;" class="btn btn-sm btn-dark me-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editExportReceiptModal{{ $export->code }}"
                                                        type="button">
                                                        <i style="font-size: 10px;" class="fa fa-edit"></i>Sửa Phiếu
                                                    </button>
                                                    <button style="font-size: 10px;" class="btn btn-sm btn-danger me-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteConfirm{{ $export->code }}"
                                                        type="button">
                                                        <i style="font-size: 10px;" class="fa fa-trash"></i>Xóa Phiếu
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-twitter me-2" style="font-size: 10px;"
                                                    id="printPdfBtn" type="button">
                                                    <i style="font-size: 10px;" class="fa fa-print"></i>In Phiếu
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            {{-- Kiểm tra trạng thái của phiếu --}}
                            @if ($export->status !== 1)
                                {{-- Modal Duyệt Phiếu --}}
                                <div class="modal fade" id="browse{{ $export->code }}" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="browseLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title text-white" id="browseLabel">Duyệt Phiếu</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center" style="padding-bottom: 0px;">
                                                <form action="{{ route('warehouse.approve_export', $export->code) }}"
                                                    method="POST">
                                                    @csrf
                                                    <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt phiếu này?</p>
                                                    <input type="hidden" name="export_code"
                                                        value="{{ $export->code }}">
                                                    <div class="modal-footer justify-content-center border-0">
                                                        <button type="button"
                                                            class="btn btn-sm btn-secondary btn-sm px-4"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success px-4">Duyệt</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Xác Nhận Xóa --}}
                                <div class="modal fade" id="deleteConfirm{{ $export->code }}" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteConfirmLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title text-white" id="deleteConfirmLabel">Xác Nhận Xóa
                                                    Phiếu</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center" style="padding-bottom: 0px;">
                                                <form action="" method="POST">
                                                    @csrf
                                                    <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa phiếu này?</p>
                                                    <input type="hidden" name="export_code"
                                                        value="{{ $export->code }}">
                                                </form>
                                            </div>
                                            <div class="modal-footer justify-content-center border-0">
                                                <button type="button" class="btn btn-sm btn-secondary px-4"
                                                    data-bs-dismiss="modal">Đóng</button>
                                                <button type="button" class="btn btn-sm btn-danger px-4"
                                                    form="deleteConfirm{{ $export->code }}">Xóa</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <tr id="noDataAlert">
                                <td colspan="12" class="text-center">
                                    <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                        role="alert"
                                        style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                        <div class="mb-3">
                                            <i class="fas fa-file-invoice" style="font-size: 36px; color: #6c757d;"></i>
                                        </div>
                                        <div class="text-center">
                                            <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông tin phiếu
                                                nhập trống</h5>
                                            <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                Hiện tại chưa có phiếu nhập nào được thêm vào. Vui lòng kiểm tra lại hoặc
                                                tạo mới phiếu nhập để bắt đầu.
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
        {{-- Tất cả hành động  --}}
        <div class="card-body py-3 mb-3">
            <div class="dropdown">
                <span class="btn btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span>Chọn Thao Tác</span>
                </span>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#confirmAll">
                            <i class="fas fa-clipboard-check me-2 text-success"></i>Duyệt Tất Cả</a>
                    </li>
                    <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                            <i class="fas fa-trash me-2 text-danger"></i>Xóa Tất Cả</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/warehouse/export.js') }}"></script>
@endsection

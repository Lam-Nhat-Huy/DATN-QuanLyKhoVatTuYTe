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
                                            <form action="" method="">
                                                @csrf
                                                <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt phiếu này?</p>
                                            </form>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0">
                                            <button type="button" class="btn btn-sm btn-secondary btn-sm px-4"
                                                data-bs-dismiss="modal">Đóng</button>
                                            <button type="button" class="btn btn-sm btn-success px-4">
                                                Duyệt</button>
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
                                            <h5 class="modal-title text-white" id="deleteConfirmLabel">Xác Nhận Xóa Phiếu
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                                            <form action="" method="">
                                                @csrf
                                                <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa phiếu này?</p>
                                            </form>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0">
                                            <button type="button" class="btn btn-sm btn-secondary px-4"
                                                data-bs-dismiss="modal">Đóng</button>
                                            <button type="button" class="btn btn-sm btn-danger px-4"> Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Chỉnh sửa -->
                            <div class="modal fade" id="editExportReceiptModal{{ $export->code }}" tabindex="-1"
                                aria-labelledby="editExportReceiptModalLabel{{ $export->code }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content rounded">
                                        <!-- Modal Header -->
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title" id="editExportReceiptModalLabel{{ $export->code }}">
                                                Chỉnh sửa phiếu xuất</h5>
                                            <button type="button" class="btn btn-sm btn-icon btn-dark"
                                                data-bs-dismiss="modal" aria-label="Close"><i
                                                    class="fa-solid fa-xmark"></i></button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                            <form action="" method="post">
                                                @csrf
                                                <!-- Export Receipt Info -->
                                                <div class="mb-5">
                                                    <h5 class="text-twitter mb-3">Thông tin phiếu xuất</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="editExportCode{{ $export->code }}"
                                                                    class="form-label">Mã phiếu:</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    id="editExportCode{{ $export->code }}"
                                                                    value="{{ $export->code }}" readonly>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="editExportNumber{{ $export->code }}"
                                                                    class="form-label">Phòng ban:</label>
                                                                <select
                                                                    class="form-select setupSelect2 bg-white form-select-sm rounded-pill"
                                                                    id="editExportNumber{{ $export->code }}" name="department_code"
                                                                    style="width: 100%;">
                                                                    <option value="">-- Chọn phòng ban --</option>
                                                                    @foreach ($departments as $department)
                                                                        <option value="{{ $department['code'] }}"
                                                                            {{ $department['code'] === $export->department_code ? 'selected' : '' }}>
                                                                            {{ $department['name'] }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="editExportDate{{ $export->code }}"
                                                                    class="form-label">Ngày:</label>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    id="editExportDate{{ $export->code }}"
                                                                    value="{{ $export->export_date }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="editCreatedBy{{ $export->code }}"
                                                                    class="form-label">Người tạo:</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    id="editCreatedBy{{ $export->code }}"
                                                                    value="{{ $export->created_by }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="editNote{{ $export->code }}"
                                                                    class="form-label">Ghi chú:</label>
                                                                <textarea name="note" class="form-control form-control-sm rounded-3 py-2 px-3" id="note" rows="3">{{ $export->note }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Receipt Items -->
                                                {{-- <div class="mb-5">
                                                    <h5 class="text-twitter">Danh sách vật tư</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr class="fw-bold bg-success">
                                                                    <th class="ps-3">Mã vật tư</th>
                                                                    <th>Số lượng</th>
                                                                    <th>Đơn giá</th>
                                                                    <th>Số lô</th>
                                                                    <th>Chiết khấu (%)</th>
                                                                    <th>VAT (%)</th>
                                                                    <th>Tổng giá</th>
                                                                    <th class="pe-3">Hành động</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="editItemsTableBody{{ $export->code }}">
                                                                <tr>
                                                                    <td><input type="text"
                                                                            class="form-control form-control-sm"
                                                                            value="VT001" disabled></td>
                                                                    <td><input type="number"
                                                                            class="form-control form-control-sm"
                                                                            value="10"></td>
                                                                    <td><input type="text"
                                                                            class="form-control form-control-sm"
                                                                            value="50,000 VND"></td>
                                                                    <td><input type="text"
                                                                            class="form-control form-control-sm"
                                                                            value="L001"></td>
                                                                    <td><input type="number"
                                                                            class="form-control form-control-sm"
                                                                            value="5"></td>
                                                                    <td><input type="number"
                                                                            class="form-control form-control-sm"
                                                                            value="10"></td>
                                                                    <td><input type="text"
                                                                            class="form-control form-control-sm"
                                                                            value="55,000 VND" readonly></td>
                                                                    <td><button type="button"
                                                                            class="btn btn-danger btn-sm">Xóa</button></td>
                                                                </tr>
                                                                <!-- More rows as needed -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="button" class="btn btn-twitter btn-sm">Thêm vật
                                                        tư</button>
                                                </div>

                                                <!-- Summary -->
                                                <div class="card p-3" style="background: #e1e9f4">
                                                    <h5 class="card-title">Tổng kết</h5>
                                                    <hr>
                                                    <p class="mb-1">Tổng tiền hàng: <span class="fw-bold"
                                                            id="editSubtotal{{ $export->code }}">12.000.000 VND</span></p>
                                                    <p class="mb-1">Tổng chiết khấu: <span class="fw-bold"
                                                            id="editTotalDiscount{{ $export->code }}">0 VND</span></p>
                                                    <p class="mb-1">Tổng VAT: <span class="fw-bold"
                                                            id="editTotalVat{{ $export->code }}">0 VND</span></p>
                                                    <p class="mb-1">Chi phí vận chuyển: <span class="fw-bold"
                                                            id="editShippingCost{{ $export->code }}">0 VND</span></p>
                                                    <p class="mb-1">Phí khác: <span class="fw-bold"
                                                            id="editOtherFees{{ $export->code }}">0 VND</span></p>
                                                    <hr>
                                                    <p class="fs-4 fw-bold text-success">Tổng giá: <span
                                                            id="editFinalTotal{{ $export->code }}">12.000.000 VND</span>
                                                    </p>
                                                </div> --}}

                                                <!-- Modal Footer -->
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-sm btn-success">Lưu Thay
                                                        Đổi</button>
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Hủy</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

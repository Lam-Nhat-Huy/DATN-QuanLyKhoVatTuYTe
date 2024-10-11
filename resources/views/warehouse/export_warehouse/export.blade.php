@extends('master_layout.layout')

@section('styles')
    <style>
        .hover-table:hover {
            background: #ccc;
        }

        .btn-group button {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .selected-row {
            background: #ddd;
        }

        .active-row {
            background: #d1c4e9;
            /* Màu nền khi hàng được nhấp vào */
        }

        .select2-selection__rendered {
            color: #000 !important;
        }
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Xuất Kho</span>
            </h3>

            <div class="card-toolbar">
                <a href="{{ route('warehouse.create_export') }}" style="font-size: 10px;" class="btn btn-sm btn-success">
                    Tạo Phiếu Xuất</a>
            </div>
        </div>

        {{-- Bộ lọc --}}
        <div class="card-body py-1">
            <form id="filterForm" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="row align-items-center">
                        <div class="col-5 pe-0">
                            <input type="date" name="start_date"
                                class="form-control form-control-sm form-control-solid border-success"
                                value="{{ \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d') }}">
                        </div>
                        <div class="col-2 text-center">Đến</div>
                        <div class="col-5 ps-0">
                            <input type="date" name="end_date"
                                class="form-control form-control-sm form-control-solid border-success"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="supplier_code" id="supplier_code"
                        class="form-select setupSelect2 form-select-sm border-success">
                        <option value="" selected>--Theo NCC--</option>
                        {{-- @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->code }}">{{ $supplier->name }}</option>
                        @endforeach --}}
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="created_by" id="created_by"
                        class="form-select setupSelect2 form-select-sm border-success">
                        <option value="" selected>--Theo Người Tạo--</option>
                        {{-- @foreach ($users as $user)
                            <option value="{{ $user->code }}">{{ $user->last_name }} {{ $user->first_name }}</option>
                        @endforeach --}}
                    </select>
                </div>


                <div class="col-md-4">
                    <div class="input-group">
                        <input type="search" id="search" name="search" placeholder="Tìm Kiếm Mã, Số Hóa Đơn.."
                            class="form-control form-control-sm form-control-solid border-success">
                    </div>
                </div>

                <div id="searchResults"></div>
            </form>

        </div>

        {{-- Danh sách phiếu  --}}
        <div class="card-body py-3">
            <div class="table-responsive rounded">
                <table class="table align-middle gs-0 gy-4">
                    <thead>
                        <tr class="bg-success">
                            <th class="ps-3">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th class="ps-3">Mã Phiếu Xuất</th>
                            <th class="">Người Tạo</th>
                            <th class="">Ngày Xuất</th>
                            <th class="pe-3">Lý Do Xuất</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 6; $i++)
                            <!-- Table structure -->
                            <tr class="text-center hover-table pointer" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $i }}" aria-expanded="false"
                                aria-controls="collapse{{ $i }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox" />
                                </td>
                                <td>PX199001</td>
                                <td>Nhật Huy</td>
                                <td>26/08/2024</td>
                                <td>Xuất Bán Lẻ</td>
                            </tr>


                            <!-- Dropdown menu -->
                            <tr class="collapse multi-collapse" id="collapse{{ $i }}">
                                <td class="p-0" colspan="12"
                                    style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
                                    <div class="flex-lg-row-fluid border-2 border-lg-1">
                                        <div class="card card-flush p-2 mb-3"
                                            style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                            <div class="card-header d-flex justify-content-between align-items-center p-2"
                                                style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                <h4 class="fw-bold m-0">Chi tiết phiếu xuất kho</h4>
                                                <div class="card-toolbar">
                                                    @if ($i < 1)
                                                        <div style="font-size: 10px;"
                                                            class="rounded px-2 py-1 text-white bg-danger">Chưa Duyệt</div>
                                                    @else
                                                        <div style="font-size: 10px;"
                                                            class="rounded px-2 py-1 text-white bg-success">Đã Duyệt</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-body p-2" style="padding-top: 0px !important">
                                                <div class="row py-5" style="padding-top: 0px !important">
                                                    <!-- Begin::Receipt Info (Left column) -->
                                                    <div class="col-md-4">
                                                        <table class="table table-flush gy-1">
                                                            <tbody>
                                                                <tr>
                                                                    <td class=""><strong>Mã phiếu xuất</strong></td>
                                                                    <td class="text-gray-800">PX00019</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Số phiếu xuất</strong></td>
                                                                    <td class="text-gray-800">025</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Khách hàng</strong></td>
                                                                    <td class="text-gray-800">Công ty ABC</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Ngày xuất</strong></td>
                                                                    <td class="text-gray-800">26/08/2024</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Người tạo</strong></td>
                                                                    <td class="text-gray-800">Nhật Huy</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Ghi chú</td>
                                                                    <td class="text-gray-800">Hàng dễ vỡ</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- End::Receipt Info -->

                                                    <div class="col-md-4">
                                                        <table class="table table-flush gy-1">
                                                            <tbody>
                                                                <tr>
                                                                    <td class=""><strong>Tổng giá trị hàng</strong>
                                                                    </td>
                                                                    <td class="text-gray-800">1,200,000 VNĐ</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Tổng chiết khấu</strong>
                                                                    </td>
                                                                    <td class="text-gray-800">25,000 VNĐ</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Tổng VAT</strong></td>
                                                                    <td class="text-gray-800">12,000 VNĐ</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Tổng cộng</strong></td>
                                                                    <td class="text-gray-800">1,237,000 VNĐ</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- End::Receipt Info -->
                                                </div>

                                                <!-- Begin::Receipt Items (Right column) -->
                                                <div class="col-md-12">
                                                    <div class="table-responsive rounded">
                                                        <table class="table table-striped table-sm table-hover">
                                                            <thead class="fw-bolder bg-danger">
                                                                <tr>
                                                                    <th class="ps-3">Mã thiết bị</th>
                                                                    <th>Tên thiết bị</th>
                                                                    <th>Số lượng</th>
                                                                    <th>Số lô</th>
                                                                    <th>Chiết khấu (%)</th>
                                                                    <th>VAT (%)</th>
                                                                    <th class="pe-3">Thành tiền</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>VT001</td>
                                                                    <td>Băng gạc</td>
                                                                    <td>10 Bịch</td>
                                                                    <td>BG001</td>
                                                                    <td>1</td>
                                                                    <td>1.2</td>
                                                                    <td>55,000 VNĐ</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>VT002</td>
                                                                    <td>Thuốc đỏ</td>
                                                                    <td>10 lọ</td>
                                                                    <td>BG001</td>
                                                                    <td>1</td>
                                                                    <td>1.2</td>
                                                                    <td>55,000 VNĐ</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>VT003</td>
                                                                    <td>Nước muối</td>
                                                                    <td>10 chai</td>
                                                                    <td>BG001</td>
                                                                    <td>1</td>
                                                                    <td>1.2</td>
                                                                    <td>550,000 VNĐ</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- End::Receipt Items -->
                                            </div>
                                        </div>


                                        <div class="card-body py-3 text-end">
                                            <div class="button-group">
                                                <!-- Nút Duyệt đơn -->
                                                <button style="font-size: 10px;" class="btn btn-sm btn-success me-2"
                                                    data-bs-toggle="modal" data-bs-target="#browse"
                                                    type="button">
                                                    <i style="font-size: 10px;" class="fas fa-clipboard-check"></i>Duyệt
                                                    Phiếu
                                                </button>

                                                <!-- Nút In Phiếu -->
                                                <button class="btn btn-sm btn-twitter me-2" style="font-size: 10px;"
                                                    id="printPdfBtn" type="button">
                                                    <i style="font-size: 10px;" class="fa fa-print"></i>In Phiếu
                                                </button>

                                                <!-- Nút Sửa đơn -->
                                                <button style="font-size: 10px;" class="btn btn-sm btn-dark me-2"
                                                    data-bs-toggle="modal" data-bs-target="#editExportReceiptModal"
                                                    type="button">
                                                    <i style="font-size: 10px;" class="fa fa-edit"></i>Sửa Phiếu
                                                </button>

                                                <!-- Nút Xóa đơn -->
                                                <button style="font-size: 10px;" class="btn btn-sm btn-danger me-2"
                                                    data-bs-toggle="modal" data-bs-target="#deleteConfirm"
                                                    type="button">
                                                    <i style="font-size: 10px;" class="fa fa-trash"></i>Xóa Phiếu
                                                </button>

                                            </div>
                                        </div>
                                    </div>


                                </td>

                            </tr>
                        @endfor
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

    {{-- Modal Duyệt Tất Cả --}}
    <div class="modal fade" id="confirmAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="confirmAll" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white" id="confirmAll">Duyệt Tất Cả Phiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt tất cả phiếu đã chọn?</p>
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

    {{-- Modal Xác Nhận Xóa Tất Cả --}}
    <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="deleteAllLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Tất Cả Phiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả phiếu đã chọn?</p>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-success px-4"> Xóa</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Duyệt Phiếu --}}
    <div class="modal fade" id="browse" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="browseLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white" id="browseLabel">Duyệt Phiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
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
    <div class="modal fade" id="deleteConfirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="deleteConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteConfirmLabel">Xác Nhận Xóa Phiếu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <form action="" method="">
                        @csrf
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa phiếu này?</p>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-danger px-4"> Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết  -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded">
                <!-- Modal header -->
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-twitter" data-bs-dismiss="modal"
                        aria-label="Close">
                        X
                    </button>
                </div>
                <!-- Modal body -->
                <div id="printArea">
                    <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                        <form action="" method="post">
                            <div class="text-center mb-13">
                                <h1 class="mb-3">Phiếu Xuất</h1>
                                <div class="text-muted fw-bold fs-6">Thông Tin Chi Tiết Về Phiếu Xuất Kho
                                    <span class="link-twitter fw-bolder">#MaXuatKho</span>.
                                </div>
                            </div>
                            <div class="mb-15">
                                <!-- Begin::Export Info -->
                                <div class="mb-4">
                                    <h4 class="text-twitter border-bottom border-dark pb-4">Thông tin phiếu xuất</h4>
                                    <div class="row pt-3">
                                        <div class="col-4">
                                            <p><strong>Mã Phiếu Xuất:</strong> <span id="modalExportCode">PX00019</span>
                                            </p>
                                            <p><strong>Số Phiếu Xuất:</strong> <span id="modalExportNumber">025</span></p>
                                            <p><strong>Khách Hàng:</strong> <span id="modalCustomer">Nguyễn Văn A</span>
                                            </p>
                                            <p><strong>Ngày Xuất:</strong> <span id="modalExportDate">26/08/2024</span></p>
                                            <p><strong>Người Tạo:</strong> <span id="modalCreatedBy">Nhật Huy</span></p>
                                            <p><strong>Ghi Chú:</strong> <span id="modalNote">Hàng dễ vỡ</span></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- End::Export Info -->

                                <!-- Begin::Export Items -->
                                <div class="mb-4">
                                    <h4 class="text-twitter border-bottom border-dark pb-4 mb-4">Danh sách thiết bị</h4>
                                    <div class="table-responsive rounded">
                                        <table class="table table-striped table-sm table-hover">
                                            <thead class="fw-bolder bg-success">
                                                <tr>
                                                    <th class="ps-3">Mã thiết bị</th>
                                                    <th>Số lượng</th>
                                                    <th>Đơn giá</th>
                                                    <th>Số lô</th>
                                                    <th>Chiết khấu (%)</th>
                                                    <th>VAT (%)</th>
                                                    <th class="pe-3">Tổng giá</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modalItemsTableBody">
                                                <tr>
                                                    <td>VT001</td>
                                                    <td>10</td>
                                                    <td>50,000 VND</td>
                                                    <td>L001</td>
                                                    <td>5</td>
                                                    <td>10</td>
                                                    <td>55,000 VND</td>
                                                </tr>
                                                <tr>
                                                    <td>VT002</td>
                                                    <td>20</td>
                                                    <td>30,000 VND</td>
                                                    <td>L002</td>
                                                    <td>0</td>
                                                    <td>10</td>
                                                    <td>33,000 VND</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- End::Export Items -->

                                <!-- Begin::Summary -->
                                <div class="card p-4" style="background-color: #e1e9f4; border: 1px solid #e3e3e3;">
                                    <h5 class="card-title">Tổng Cộng</h5>
                                    <hr>
                                    <p class="mb-1">Tổng Tiền Hàng:
                                        <span class="fw-bold" id="modalSubtotal">12.000.000 VND</span>
                                    </p>
                                    <p class="mb-1">Tổng Chiết Khấu:
                                        <span class="fw-bold" id="modalTotalDiscount">0 VND</span>
                                    </p>
                                    <p class="mb-1">Tổng VAT:
                                        <span class="fw-bold" id="modalTotalVat">0 VND</span>
                                    </p>
                                    <p class="mb-1">Chi Phí Vận Chuyển:
                                        <span class="fw-bold" id="modalShippingCost">0 VND</span>
                                    </p>
                                    <p class="mb-1">Phí Khác:
                                        <span class="fw-bold" id="modalOtherFees">0 VND</span>
                                    </p>
                                    <hr>
                                    <p class="fs-4 fw-bold text-success">Tổng:
                                        <span id="modalFinalTotal">12.000.000 VND</span>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chỉnh sửa -->
    <div class="modal fade" id="editExportReceiptModal" tabindex="-1" aria-labelledby="editExportReceiptModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded">
                <!-- Modal Header -->
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="editExportReceiptModalLabel">Chỉnh Sửa Phiếu</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-dark" data-bs-dismiss="modal"
                        aria-label="Close">
                        X
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                    <form action="" method="post">
                        <!-- Export Receipt Info -->
                        <div class="mb-5">
                            <h5 class="text-twitter mb-3">Thông tin phiếu</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editExportCode" class="form-label">Mã Phiếu:</label>
                                        <input type="text" class="form-control form-control-sm" id="editExportCode"
                                            value="PX00019">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editExportNumber" class="form-label">Số Phiếu:</label>
                                        <input type="text" class="form-control form-control-sm" id="editExportNumber"
                                            value="025">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editCustomer" class="form-label">Khách Hàng:</label>
                                        <input type="text" class="form-control form-control-sm" id="editCustomer"
                                            value="Nguyễn Văn A">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editExportDate" class="form-label">Ngày:</label>
                                        <input type="date" class="form-control form-control-sm" id="editExportDate"
                                            value="2024-08-26">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editCreatedBy" class="form-label">Người Tạo:</label>
                                        <input type="text" class="form-control form-control-sm" id="editCreatedBy"
                                            value="Nhật Huy">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editNote" class="form-label">Ghi Chú:</label>
                                        <input class="form-control form-control-sm" id="editNote" rows="2"
                                            value="Hàng dễ vỡ">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Receipt Items -->
                        <div class="mb-5">
                            <h5 class="text-twitter">Danh sách thiết bị</h5>
                            <div class="table-responsive rounded">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="fw-bold bg-success">
                                            <th class="ps-3">Mã thiết bị</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Số lô</th>
                                            <th>Chiết khấu (%)</th>
                                            <th>VAT (%)</th>
                                            <th>Tổng giá</th>
                                            <th class="pe-3">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="editItemsTableBody">
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" value="VT001"
                                                    disabled></td>
                                            <td><input type="number" class="form-control form-control-sm"
                                                    value="10"></td>
                                            <td><input type="text" class="form-control form-control-sm"
                                                    value="50,000 VND"></td>
                                            <td><input type="text" class="form-control form-control-sm"
                                                    value="L001"></td>
                                            <td><input type="number" class="form-control form-control-sm"
                                                    value="5"></td>
                                            <td><input type="number" class="form-control form-control-sm"
                                                    value="10"></td>
                                            <td><input type="text" class="form-control form-control-sm"
                                                    value="55,000 VND" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm">Xóa</button>
                                            </td>
                                        </tr>
                                        <!-- More rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-twitter btn-sm">Thêm thiết bị</button>
                        </div>

                        <!-- Summary -->
                        <div class="card p-3" style="background: #e1e9f4">
                            <h5 class="card-title">Tổng kết</h5>
                            <hr>
                            <p class="mb-1">Tổng tiền hàng:
                                <span class="fw-bold" id="editSubtotal">12.000.000 VND</span>
                            </p>
                            <p class="mb-1">Tổng chiết khấu:
                                <span class="fw-bold" id="editTotalDiscount">0 VND</span>
                            </p>
                            <p class="mb-1">Tổng VAT:
                                <span class="fw-bold" id="editTotalVat">0 VND</span>
                            </p>
                            <p class="mb-1">Chi phí vận chuyển:
                                <span class="fw-bold" id="editShippingCost">0 VND</span>
                            </p>
                            <p class="mb-1">Phí khác:
                                <span class="fw-bold" id="editOtherFees">0 VND</span>
                            </p>
                            <hr>
                            <p class="fs-4 fw-bold text-success">Tổng giá:
                                <span id="editFinalTotal">12.000.000 VND</span>
                            </p>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-sm btn-success">Lưu Thay Đổi</button>
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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
        });

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
            });
        });

        // Add click event to rows
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
                }
            });
        });

        document.getElementById('printPdfBtn').addEventListener('click', function() {
            var printContents = document.getElementById('printArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });
        
    </script>
@endsection

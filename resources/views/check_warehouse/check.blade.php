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
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8">

        @include('check_warehouse.filter')

        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table align-middle gs-0 gy-4">
                    <!-- Trong phần <thead> của bảng -->
                    <thead>
                        <tr class="bg-success text-center">
                            <th class="ps-3">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th class="ps-3">Mã kiểm kho</th>
                            <th class="">Thời gian</th>
                            <th class="">Tổng chênh lệch</th>
                            <th class="">Số lượng lệch tăng</th>
                            <th class="">Số lượng lệch giảm</th>
                            <th class="">Trạng Thái</th>
                        </tr>
                    </thead>

                    <!-- Trong phần <tbody> của bảng -->
                    <tbody>
                        @forelse ($inventoryChecks as $item)
                            @php
                                $totalUnequal = collect($item['details'])->sum('unequal');
                            @endphp

                            <tr class="text-center hover-table pointer" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $item['code'] }}" aria-expanded="false"
                                aria-controls="collapse{{ $item['code'] }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox" />
                                </td>
                                <td>
                                    {{ $item['code'] }}
                                </td>
                                <td>
                                    {{ $item['check_date'] }}
                                </td>
                                <td>
                                    {{ $totalUnequal }} <!-- Hiển thị tổng chênh lệch -->
                                </td>
                                <td>
                                    {{ $item['details']->where('unequal', '>', 0)->sum('unequal') }}
                                    <!-- Số lượng lệch tăng -->
                                </td>
                                <td>
                                    {{ $item['details']->where('unequal', '<', 0)->sum('unequal') }}
                                    <!-- Số lượng lệch giảm -->
                                </td>
                                <td>
                                    @if ($item['status'] == 0)
                                        <span class="label label-temp text-danger">Phiếu tạm</span>
                                    @else
                                        <span class="label label-final text-success">Đã cân bằng</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Collapse content -->
                            <tr class="collapse multi-collapse" id="collapse{{ $item['code'] }}">
                                <td class="p-0" colspan="12"
                                    style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
                                    <div class="flex-lg-row-fluid border-2 border-lg-1">
                                        <div class="card card-flush p-2 mb-3"
                                            style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                            <div class="card-header d-flex justify-content-between align-items-center p-2"
                                                style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                <h4 class="fw-bold m-0">Chi tiết phiếu kiểm kho</h4>
                                            </div>
                                            <div class="card-body p-2" style="padding-top: 0px !important">
                                                <div class="row py-5" style="padding-top: 0px !important">
                                                    <!-- Begin::Receipt Info (Left column) -->
                                                    <div class="col-md-4">
                                                        <table class="table table-flush gy-1">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="" style="width: 150px;"><strong>Mã kiểm
                                                                            kho</strong>
                                                                    </td>

                                                                    <td class="text-gray-800">
                                                                        {{ $item['code'] }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Thời gian</strong>
                                                                    </td>
                                                                    <td class="text-gray-800">
                                                                        {{ $item['created_at'] }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Ngày cân bằng</strong>
                                                                    </td>
                                                                    <td class="text-gray-800">
                                                                        {{ \Carbon\Carbon::parse($item['check_date'])->format('d/m/Y') }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class=""><strong>Ghi chú</strong>
                                                                    </td>
                                                                    <td class="text-gray-800">
                                                                        {{ $item['note'] }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <table class="table table-flush gy-1">
                                                            <tbody>
                                                                <tr>
                                                                    <td class=""><strong>Trạng thái</strong></td>
                                                                    <td class="text-gray-800">
                                                                        @if ($item['status'] == 0)
                                                                            <span class="text-danger">Phiếu tạm</span>
                                                                        @else
                                                                            <span class="text-success">Đã cân bằng </span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class=""><strong>Tài khoản tạo</strong>
                                                                    </td>
                                                                    <td class="text-gray-800">
                                                                        {{ $item->user->last_name . ' ' . $item->user->first_name }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-sm table-hover">
                                                            <thead style="background-color: #FFA500;">
                                                                <tr class="text-center">
                                                                    <th class="ps-3">Mã vật tư</th>
                                                                    <th>Tên vật tư</th>
                                                                    <th>Tồn kho</th>
                                                                    <th>Số lượng thực tế</th>
                                                                    <th>Số lượng lệch</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($item['details'] as $detail)
                                                                    <tr class="text-center">
                                                                        <td class="ps-4">
                                                                            {{ $detail['equipment_code'] }}
                                                                        </td>
                                                                        <td>Băng gạc</td>
                                                                        <td>
                                                                            {{ $detail['current_quantity'] }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $detail['actual_quantity'] }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $detail['unequal'] }}
                                                                        </td>
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
                                                <!-- Nút Duyệt đơn, chỉ hiển thị khi là Phiếu Tạm -->
                                                @if ($item['status'] == 0)
                                                    <button style="font-size: 10px;"
                                                        class="btn btn-sm btn-success me-2 rounded-pill"
                                                        data-bs-toggle="modal" data-bs-target="#browse-{{ $item->code }}"
                                                        type="button">
                                                        <i style="font-size: 10px;" class="fas fa-clipboard-check"></i>Duyệt
                                                        Phiếu
                                                    </button>
                                                @endif

                                                <!-- Nút Sửa đơn -->
                                                @if ($item['status'] == 0)
                                                    <a style="font-size: 10px;" href=""
                                                        class="btn btn-dark btn-sm me-2 rounded-pill"><i
                                                            style="font-size: 10px;" class="fa fa-edit"></i>Sửa Phiếu</a>
                                                @endif
                                                @if ($item['status'] == 1)
                                                    <!-- Nút In Phiếu -->
                                                    <button style="font-size: 10px;"
                                                        class="btn btn-sm btn-dark me-2 rounded-pill" id="printPdfBtn"
                                                        type="button">
                                                        <i style="font-size: 10px;" class="fa fa-print"></i>In Phiếu
                                                    </button>
                                                @endif

                                                @if ($item['status'] == 0)
                                                    <!-- Nút xóa, có thể nằm trong danh sách hoặc bảng -->
                                                    <button style="font-size: 10px;"
                                                        class="btn btn-danger btn-sm rounded-pill" data-bs-toggle="modal"
                                                        data-bs-target="#delete-">
                                                        <i style="font-size: 10px;" class="fa fa-trash"></i>Xóa phiếu
                                                    </button>
                                                @endif


                                                <!-- Modal Duyệt Phiếu -->
                                                <div class="modal fade" id="browse-{{ $item['code'] }}"
                                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                    aria-labelledby="browseLabel-{{ $item['code'] }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title text-white"
                                                                    id="browseLabel-{{ $item['code'] }}">Duyệt
                                                                    Phiếu Kiểm Kho</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-center pb-0"
                                                                style="padding-bottom: 0px;">
                                                                <form
                                                                    action="{{ route('check_warehouse.approve', $item['code']) }}"
                                                                    method="POST" id="approveForm-{{ $item['code'] }}">
                                                                    @csrf
                                                                    <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt
                                                                        phiếu kiểm kho này?
                                                                    </p>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer justify-content-center pt-0 border-0">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-secondary px-4 rounded-pill"
                                                                    data-bs-dismiss="modal">Đóng</button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success px-4 rounded-pill"
                                                                    onclick="event.preventDefault(); document.getElementById('approveForm-{{ $item->code }}').submit();">
                                                                    Duyệt
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                            <i class="fas fa-clipboard-check"
                                                style="font-size: 36px; color: #6c757d;"></i>
                                        </div>
                                        <div class="text-center">
                                            <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông tin phiếu
                                                kiểm kho trống</h5>
                                            <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                Hiện tại chưa có phiếu kiểm kho nào được tạo. Vui lòng kiểm tra lại hoặc tạo
                                                mới phiếu kiểm kho để bắt đầu.
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
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                let query = $(this).val();

                // Lấy dữ liệu từ các trường bộ lọc
                let startDate = $('input[name="start_date"]').val();
                let endDate = $('input[name="end_date"]').val();
                let userCode = $('select[name="user_code"]')
                    .val(); // Thay 'supplier_code' bằng 'user_code' nếu bạn muốn tìm theo mã người dùng
                let status = $('select[name="status"]').val();

                if (query.length > 0) {
                    $.ajax({
                        url: "{{ route('check_warehouse.search') }}",
                        type: "GET",
                        data: {
                            'search': query,
                            'start_date': startDate,
                            'end_date': endDate,
                            'user_code': userCode,
                            'status': status
                        },
                        success: function(data) {
                            // Hiển thị kết quả tìm kiếm
                            $('tbody').html(data);
                        },
                        error: function(xhr) {
                            console.error("Error occurred: ", xhr);
                        }
                    });
                } else {
                    location.reload();
                }
            });

            // Thêm sự kiện cho các trường lọc
            $('input[name="start_date"], input[name="end_date"], select[name="user_code"], input[name="note"], select[name="status"]')
                .on('change', function() {
                    let query = $('#search').val();

                    // Lấy dữ liệu từ các trường bộ lọc
                    let startDate = $('input[name="start_date"]').val();
                    let endDate = $('input[name="end_date"]').val();
                    let userCode = $('select[name="user_code"]').val();
                    let status = $('select[name="status"]').val();

                    $.ajax({
                        url: "{{ route('check_warehouse.search') }}",
                        type: "GET",
                        data: {
                            'search': query,
                            'start_date': startDate,
                            'end_date': endDate,
                            'user_code': userCode,
                            'status': status
                        },
                        success: function(data) {
                            $('tbody').html(data);
                        },
                        error: function(xhr) {
                            console.error("Error occurred: ", xhr);
                        }
                    });
                });
        });
    </script>
@endsection

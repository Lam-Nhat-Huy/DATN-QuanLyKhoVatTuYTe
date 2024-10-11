{{-- @forelse ($receipts as $item)
    <tr class="text-center hover-table pointer" data-bs-toggle="collapse" data-bs-target="#collapse{{ $item['code'] }}"
        aria-expanded="false" aria-controls="collapse{{ $item['code'] }}">
        <td>
            <input type="checkbox" class="row-checkbox" />
        </td>
        <td>
            {{ $item->code }}
        </td>
        <td>
            {{ $item->receipt_no }}
        </td>
        <td>
            {{ $item->supplier->name }}
        </td>
        <td>
            {{ $item->user->last_name . ' ' . $item->user->first_name }}
        </td>
        <td>
            {{ $item->receipt_date }}
        </td>
        <td>
            @if ($item['status'] == 1)
                Phiếu Tạm
            @else
                Đã nhập
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
                        <h4 class="fw-bold m-0">Chi tiết phiếu nhập kho</h4>
                        <div class="card-toolbar">
                            @if ($item['status'] == 1)
                                <div style="font-size: 10px;" class="rounded px-2 py-1 text-white bg-danger">Chưa Duyệt
                                </div>
                            @else
                                <div style="font-size: 10px;" class="rounded px-2 py-1 text-white bg-success">Đã Duyệt
                                </div>
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
                                            <td class=""><strong>Mã phiếu nhập</strong>
                                            </td>
                                            <td class="text-gray-800">{{ $item->code }}</td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Số hóa đơn</strong>
                                            </td>
                                            <td class="text-gray-800">{{ $item->receipt_no }}</td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Nhà cung cấp</strong>
                                            </td>
                                            <td class="text-gray-800">
                                                {{ $item->supplier->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Ngày nhập</strong>
                                            </td>
                                            <td class="text-gray-800">
                                                {{ $item->receipt_date }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Người tạo</strong>
                                            </td>
                                            <td class="text-gray-800">
                                                {{ $item->user->last_name . ' ' . $item->user->first_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Ghi chú</td>
                                            <td class="text-gray-800">
                                                {{ $item->note }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- End::Receipt Info -->

                            @php
                                $totalPrice = 0;
                                $totalDiscount = 0;
                                $totalVAT = 0;

                                foreach ($item->details as $detail) {
                                    $price = $detail->price ?? 0;
                                    $quantity = $detail->quantity;
                                    $discount = $detail->discount ?? 0;
                                    $vat = $detail->VAT ?? 0;

                                    $totalPrice += $quantity * $price;

                                    $totalDiscount += $quantity * $discount;

                                    $totalVAT += $quantity * ($price - $discount) * ($vat / 100);
                                }

                                $totalAmount = $totalPrice - $totalDiscount + $totalVAT;
                            @endphp

                            <div class="col-md-4">
                                <table class="table table-flush gy-1">
                                    <tbody>
                                        <tr>
                                            <td class=""><strong>Tổng tiền hàng</strong></td>
                                            <td class="text-gray-800">
                                                {{ number_format($totalPrice, 0) }} VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Tổng chiết khấu</strong>
                                            </td>
                                            <td class="text-gray-800">
                                                {{ number_format($totalDiscount, 0) }} VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Tổng VAT</strong></td>
                                            <td class="text-gray-800">
                                                {{ number_format($totalVAT, 0) }} VNĐ</td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Tổng cộng</strong></td>
                                            <td class="text-gray-800">
                                                {{ number_format($totalAmount, 0) }} VNĐ</td>
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
                                            <th>Giá nhập</th>
                                            <th>Số lô</th>
                                            <th>Chiết khấu (%)</th>
                                            <th>VAT (%)</th>
                                            <th class="pe-3">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->details as $detail)
                                            @php
                                                $price = $detail->price ?? 0;
                                                $quantity = $detail->quantity;
                                                $discount = $detail->discount ?? 0;
                                                $vat = $detail->VAT ?? 0;

                                                $totalPrice = $quantity * ($price - $discount);
                                                $totalPriceWithVAT = $totalPrice * (1 + $vat / 100);
                                            @endphp
                                            <tr class="text-center">
                                                <td>{{ $detail->equipments->code }}</td>
                                                <td>{{ $detail->equipments->name }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>{{ number_format($detail->price) }} VND</td>
                                                <td>{{ $detail->batch_number }}</td>
                                                <td>{{ $detail->discount }}</td>
                                                <td>{{ $detail->VAT }}%</td>
                                                <td>{{ number_format($totalPriceWithVAT) }} VND
                                                </td>
                                            </tr>
                                        @endforeach

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
                        <button style="font-size: 10px;" class="btn btn-sm btn-success me-2 rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#browse" type="button">
                            <i style="font-size: 10px;" class="fas fa-clipboard-check"></i>Duyệt
                            Phiếu
                        </button>

                        <!-- Nút In Phiếu -->
                        <button style="font-size: 10px;" class="btn btn-sm btn-twitter me-2 rounded-pill"
                            id="printPdfBtn" type="button">
                            <i style="font-size: 10px;" class="fa fa-print"></i>In Phiếu
                        </button>

                        <!-- Nút Sửa đơn -->
                        <button style="font-size: 10px;" class="btn btn-sm btn-dark me-2 rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#edit" type="button">
                            <i style="font-size: 10px;" class="fa fa-edit"></i>Sửa Phiếu
                        </button>

                        <!-- Nút Xóa đơn -->
                        <button style="font-size: 10px;" class="btn btn-sm btn-danger me-2 rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#deleteConfirm" type="button">
                            <i style="font-size: 10px;" class="fa fa-trash"></i>Xóa Phiếu
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
                role="alert" style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                <div class="mb-3">
                    <i class="fas fa-search" style="font-size: 36px; color: #6c757d;"></i>
                </div>
                <div class="text-center">
                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có kết quả tìm kiếm</h5>
                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                        Không tìm thấy kết quả phù hợp với yêu cầu tìm kiếm của bạn. Vui lòng thử lại với từ khóa khác.
                    </p>
                </div>
            </div>
        </td>
    </tr>
@endforelse --}}

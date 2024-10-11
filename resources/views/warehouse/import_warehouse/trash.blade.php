@extends('master_layout.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/warehouse/import.css') }}">

    <style>
        .custom-w {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 350px;
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
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('warehouse.import') }}" class="btn btn-sm btn-dark rounded-pill">
                    <i class="fas fa-arrow-left" style="margin-bottom: 2px;"></i> Trở Lại
                </a>
            </div>
        </div>
        <form action="{{ route('warehouse.trash') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <!-- Trong phần <thead> của bảng -->
                        <thead>
                            <tr class="bg-success">
                                <th class="ps-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th style="width: 10%;">Mã</th>
                                <th class="" style="width: 10%;">Số Hóa Đơn</th>
                                <th class="" style="width: 40%;">Nhà Cung Cấp</th>
                                <th class="" style="width: 10%;">Tạo Bởi</th>
                                <th class="" style="width: 10%;">Ngày Nhập</th>
                                <th class="text-center" style="width: 10%;">Trạng Thái</th>
                                <th class="pe-3 text-center" style="width: 10%;">Hành Động</th>
                            </tr>
                        </thead>

                        <!-- Trong phần <tbody> của bảng -->
                        <tbody>
                            @forelse ($receiptTrash as $item)
                                @if ($item->status == 3 && $item->created_by != session('user_code'))
                                    <tr class="hover-table pointer">
                                        <td></td>
                                        <td>#{{ $item->code }}</td>
                                        <td>{{ $item->receipt_no }}</td>
                                        <td class="custom-w">{{ $item->supplier->name }}</td>
                                        <td>{{ $item->user->last_name . ' ' . $item->user->first_name }}</td>
                                        <td>
                                            {{ $item->receipt_date }}
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
                                                <div
                                                    class="label label-final bg-success rounded-pill text-white px-2 py-1">
                                                    Đã duyệt
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $item->code }}"
                                            id="toggleIcon{{ $item->code }}">
                                            Chi Tiết<i class="fa fa-chevron-right pointer ms-2"></i>
                                        </td>
                                    </tr>

                                    <!-- Collapse content -->
                                    <tr class="collapse multi-collapse" id="collapse{{ $item['code'] }}">
                                        <td class="p-0" colspan="12"
                                            style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
                                            <div class="flex-lg-row-fluid border-2 border-lg-1">
                                                <div class="card card-flush p-2"
                                                    style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                                    <div class="card-header d-flex justify-content-between align-items-center p-3 pb-0"
                                                        style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                        <h4 class="fw-bold m-0 text-uppercase fw-bolder">Chi tiết phiếu nhập
                                                            kho
                                                        </h4>
                                                        <div class="card-toolbar">
                                                            @if ($item->status == 3)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-info">
                                                                    Lưu Tạm
                                                                </div>
                                                            @elseif ($item->status == 0)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-danger">Chờ
                                                                    Duyệt
                                                                </div>
                                                            @elseif ($item->status == 1)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-success">
                                                                    Đã Duyệt
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-3 pt-0">
                                                        <div class="row" style="padding-top: 0px !important">
                                                            <div class="col-md-7">
                                                                <table class="table table-flush gy-1">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Mã phiếu nhập</strong>
                                                                            </td>
                                                                            <td style="" class="text-dark">
                                                                                {{ $item->code }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Số hóa đơn</strong>
                                                                            </td>
                                                                            <td class="text-dark">{{ $item->receipt_no }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 250px;"><strong>Nhà cung
                                                                                    cấp</strong>
                                                                            </td>
                                                                            <td class="text-dark" style="width: 550px;">
                                                                                {{ $item->supplier->name }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Ngày nhập</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ \Carbon\Carbon::parse($item->receipt_date)->format('d/m/Y') }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Người tạo</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ $item->user->last_name . ' ' . $item->user->first_name }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Ghi chú</td>
                                                                            <td class="text-dark">
                                                                                {{ $item->note }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
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

                                                                    $totalDiscount += $totalPrice * ($discount / 100);

                                                                    $totalVAT += $totalPrice * ($vat / 100);
                                                                }

                                                                $totalAmount = $totalPrice - $totalDiscount + $totalVAT;
                                                            @endphp

                                                            <div class="col-md-5">
                                                                <table class="table table-flush gy-1">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng tiền
                                                                                    hàng</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalPrice, 0) }} VND
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng chiết
                                                                                    khấu</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalDiscount, 0) }} VND
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng VAT</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalVAT, 0) }} VND</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng cộng</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalAmount, 0) }} VND
                                                                            </td>
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
                                                                        <tr class="text-center">
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

                                                                                $totalPrice =
                                                                                    $quantity * ($price - $discount);
                                                                                $totalPriceWithVAT =
                                                                                    $totalPrice * (1 + $vat / 100);
                                                                            @endphp
                                                                            <tr class="text-center">
                                                                                <td>{{ $detail->equipments->code }}</td>
                                                                                <td>{{ $detail->equipments->name }}</td>
                                                                                <td>{{ $detail->quantity }}</td>
                                                                                <td>{{ number_format($detail->price) }} VND
                                                                                </td>
                                                                                <td>{{ $detail->batch_number }}</td>
                                                                                <td>{{ $detail->discount }}%</td>
                                                                                <td>{{ $detail->VAT }}%</td>
                                                                                <td>{{ number_format($totalPriceWithVAT) }}
                                                                                    VND
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
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="hover-table pointer">
                                        <td>
                                            <input type="checkbox" name="import_codes[]" value="{{ $item->code }}"
                                                class="row-checkbox" />
                                        </td>
                                        <td>
                                            #{{ $item->code }}
                                        </td>
                                        <td>
                                            {{ $item->receipt_no }}
                                        </td>
                                        <td class="custom-w">
                                            {{ $item->supplier->name }}
                                        </td>
                                        <td>
                                            {{ $item->user->last_name . ' ' . $item->user->first_name }}
                                        </td>
                                        <td>
                                            {{ $item->receipt_date }}
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
                                                <div
                                                    class="label label-final bg-success rounded-pill text-white px-2 py-1">
                                                    Đã duyệt
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $item->code }}"
                                            id="toggleIcon{{ $item->code }}">
                                            Chi Tiết<i class="fa fa-chevron-right pointer ms-2"></i>
                                        </td>
                                    </tr>

                                    <!-- Collapse content -->
                                    <tr class="collapse multi-collapse" id="collapse{{ $item['code'] }}">
                                        <td class="p-0" colspan="12"
                                            style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
                                            <div class="flex-lg-row-fluid border-2 border-lg-1">
                                                <div class="card card-flush p-2"
                                                    style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                                    <div class="card-header d-flex justify-content-between align-items-center p-3 pb-0"
                                                        style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                        <h4 class="fw-bold m-0 text-uppercase fw-bolder">Chi tiết phiếu
                                                            nhập
                                                            kho
                                                        </h4>
                                                        <div class="card-toolbar">
                                                            @if ($item->status == 3)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-info">
                                                                    Lưu Tạm
                                                                </div>
                                                            @elseif ($item->status == 0)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-danger">
                                                                    Chờ
                                                                    Duyệt
                                                                </div>
                                                            @elseif ($item->status == 1)
                                                                <div class="rounded-pill px-2 py-1 text-white bg-success">
                                                                    Đã Duyệt
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-3 pt-0">
                                                        <div class="row" style="padding-top: 0px !important">
                                                            <div class="col-md-7">
                                                                <table class="table table-flush gy-1">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><strong>Mã phiếu nhập</strong>
                                                                            </td>
                                                                            <td style="" class="text-dark">
                                                                                {{ $item->code }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Số hóa đơn</strong>
                                                                            </td>
                                                                            <td class="text-dark">{{ $item->receipt_no }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 250px;"><strong>Nhà cung
                                                                                    cấp</strong>
                                                                            </td>
                                                                            <td class="text-dark" style="width: 550px;">
                                                                                {{ $item->supplier->name }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Ngày nhập</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ \Carbon\Carbon::parse($item->receipt_date)->format('d/m/Y') }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Người tạo</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ $item->user->last_name . ' ' . $item->user->first_name }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Ghi chú</td>
                                                                            <td class="text-dark">
                                                                                {{ $item->note }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
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

                                                                    $totalDiscount += $totalPrice * ($discount / 100);

                                                                    $totalVAT += $totalPrice * ($vat / 100);
                                                                }

                                                                $totalAmount = $totalPrice - $totalDiscount + $totalVAT;
                                                            @endphp

                                                            <div class="col-md-5">
                                                                <table class="table table-flush gy-1">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng tiền
                                                                                    hàng</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalPrice, 0) }} VND
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng chiết
                                                                                    khấu</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalDiscount, 0) }} VND
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng VAT</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalVAT, 0) }} VND</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class=""><strong>Tổng cộng</strong>
                                                                            </td>
                                                                            <td class="text-dark">
                                                                                {{ number_format($totalAmount, 0) }} VND
                                                                            </td>
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
                                                                        <tr class="text-center">
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

                                                                                $totalPrice =
                                                                                    $quantity * ($price - $discount);
                                                                                $totalPriceWithVAT =
                                                                                    $totalPrice * (1 + $vat / 100);
                                                                            @endphp
                                                                            <tr class="text-center">
                                                                                <td>{{ $detail->equipments->code }}</td>
                                                                                <td>{{ $detail->equipments->name }}</td>
                                                                                <td>{{ $detail->quantity }}</td>
                                                                                <td>{{ number_format($detail->price) }} VND
                                                                                </td>
                                                                                <td>{{ $detail->batch_number }}</td>
                                                                                <td>{{ $detail->discount }}%</td>
                                                                                <td>{{ $detail->VAT }}%</td>
                                                                                <td>{{ number_format($totalPriceWithVAT) }}
                                                                                    VND
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

                                                <div class="card-body py-1 text-end bg-white pb-5">
                                                    <div class="button-group">
                                                        <button class="btn btn-sm btn-twitter rounded-pill me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#restore-{{ $item->code }}" type="button">
                                                            <i class="fas fa-rotate-right"
                                                                style="margin-bottom: 2px;"></i>Khôi Phục
                                                        </button>

                                                        <button class="btn btn-sm btn-danger rounded-pill me-2"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#delete-{{ $item->code }}" type="button">
                                                            <i class="fas fa-trash" style="margin-bottom: 2px;"></i>Xóa
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
                                                <i class="fas fa-trash" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">
                                                    Thùng Rác Rỗng
                                                </h5>
                                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                    Không Có Phiếu Nhập Nào Bị Hủy
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

            @if ($allReceiptCount > 0)
                <div class="card-body py-3">
                    <div class="filter-bar">
                        <ul class="nav nav-pills">
                            <li class="nav-item" style="font-size: 11px;">
                                <p class="nav-link text-white rounded-pill" style="background-color: #0064ff;">Tất cả
                                    <span>({{ $allReceiptCount }})</span>
                                </p>
                            </li>
                            <li class="nav-item" style="font-size: 11px;">
                                <p class="nav-link text-white rounded-pill" style="background-color: green;">Đã duyệt
                                    <span>({{ $approvedReceiptsCount }})</span>
                                </p>
                            </li>
                            <li class="nav-item" style="font-size: 11px;">
                                <p class="nav-link text-white rounded-pill" style="background-color: red;">Chờ duyệt
                                    <span>({{ $draftReceiptsCount }})</span>
                                </p>
                            </li>
                            <li class="nav-item" style="font-size: 11px;">
                                <p class="nav-link text-white rounded-pill" style="background-color: rgb(123, 0, 255);">
                                    Lưu Tạm
                                    <span>({{ $tempReceiptsCount }})</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            @if ($receiptTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown" id="action_delete_all">
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
                        {{ $receiptTrash->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Khôi Phục Tất Cả --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllModal">Khôi Phục Phiếu Nhập</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục phiếu nhập đã chọn?
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
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Vĩnh Viễn Phiếu Nhập</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn hủy phiếu nhập đã chọn?</p>
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

    @foreach ($receiptTrash as $item)
        <!-- Modal Khôi Phục Phiếu -->
        <div class="modal fade" id="restore-{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="restoreLabel-{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white" id="restoreLabel-{{ $item->code }}">
                            Khôi Phục Phiếu Nhập Kho</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('warehouse.trash') }}" method="POST">
                        @csrf
                        <input type="hidden" name="restore_value" value="{{ $item->code }}">
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục phiếu nhập kho
                                này?
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-sm btn-twitter px-4 rounded-pill load_animation">
                                Khôi Phục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Xóa Vĩnh Viễn Phiếu -->
        <div class="modal fade" id="delete-{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="deleteLabel-{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteLabel-{{ $item->code }}">
                            Xác Nhận Xóa Vĩnh Viễn Phiếu</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('warehouse.trash') }}" method="POST">
                        @csrf
                        <input type="hidden" name="delete_value" value="{{ $item->code }}">
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa vĩnh viễn phiếu này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-sm btn-danger px-4 rounded-pill load_animation">
                                Xóa Vĩnh Viễn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(td) {
            td.addEventListener('click', function(event) {
                // Tìm phần tử <i> bên trong <td>
                var icon = this.querySelector('i');

                // Kiểm tra nếu có <i> thì thực hiện đổi biểu tượng
                if (icon) {
                    // Đổi icon khi click
                    if (icon.classList.contains('fa-chevron-right')) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    } else {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                }

                // Ngăn chặn việc click ảnh hưởng đến hàng (row)
                event.stopPropagation();
            });
        });

        // Hàm kiểm tra và ẩn/hiện nút hủy tất cả
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

        // Khi click vào checkbox "Select All"
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
            toggleDeleteAction();
        });

        // Khi checkbox của từng hàng thay đổi
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
                toggleDeleteAction(); // Gọi hàm kiểm tra nút hủy tất cả
            });
        });

        // Khi người dùng click vào hàng
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
                    toggleDeleteAction(); // Gọi hàm kiểm tra nút hủy tất cả
                }
            });
        });

        // Kiểm tra trạng thái ban đầu khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            toggleDeleteAction();

            document.querySelector('#restoreAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'restore';
            });

            document.querySelector('#deleteAll').addEventListener('show.bs.modal', function() {
                document.getElementById('action_type').value = 'delete';
            });
        });

        toggleDeleteAction();
    </script>
@endsection

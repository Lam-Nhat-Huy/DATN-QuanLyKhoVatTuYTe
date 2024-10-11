@extends('master_layout.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
@endsection

@section('title')
    Nhập Kho
@endsection

@php
    if ($action == 'create' && !empty(request('cd'))) {
        $action = route('warehouse.import_equipment_request');

        $required = 'required';

        $d_none_save = '';

        $d_none_update = 'd-none';

        $d_none_temp = '';
    } elseif ($action == 'create') {
        $action = route('warehouse.store_import');

        $required = 'required';

        $d_none_save = '';

        $d_none_update = 'd-none';

        $d_none_temp = '';
    } elseif ($action == 'update') {
        $action = route('warehouse.update_import', request('code'));

        $required = '';

        $d_none_save = 'd-none';

        $d_none_update = '';

        $d_none_temp = 'd-none';
    }
@endphp

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thông Tin Phiếu Nhập</span>
            </h3>

            <div class="card-toolbar">
                <a href="{{ route('warehouse.import') }}" class="btn btn-sm btn-dark rounded-pill">
                    <i class="fa fa-arrow-left me-1" style="margin-bottom: 2px;"></i>Trở Lại
                </a>
            </div>
        </div>

        <div class="container">
            <div class="card border-0 px-8 mb-4 rounded-3 mt-3">
                <div class="row">
                    <div class="col-md-6 fv-row">
                        <label for="receipt_no" class="{{ $required }} form-label fw-semibold">Nhà cung cấp</label>
                        <div class="d-flex align-items-center">
                            <select name="supplier_code" id="supplier_code" {{ !empty($infoIER) ? 'disabled' : '' }}
                                class="form-select form-select-sm border border-success rounded-pill ps-5">
                                <option value="0">Chọn Nhà Cung Cấp...</option>
                                @foreach ($suppliers as $item)
                                    <option value="{{ $item->code }}" id="option_supplier_{{ $item->code }}"
                                        {{ !empty($infoIER) ? ($infoIER->supplier_code == $item->code ? 'selected' : '') : (old('supplier_code', $editForm->supplier_code ?? '') == $item->code ? 'selected' : '') }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>

                            <span class="ms-4 pointer" data-bs-toggle="modal" data-bs-target="#add_modal_ncc"
                                title="Thêm Nhà Cung Cấp">
                                <i class="fa fa-plus bg-primary rounded-circle p-2 text-white"
                                    style="width: 25px; height: 25px;"></i>
                            </span>
                        </div>
                        <div class="message_error" id="supplier_code_error"></div>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="receipt_no" class="{{ $required }} form-label fw-semibold">Số hóa đơn</label>
                        <input type="text" tabindex="3"
                            class="form-control form-control-sm border border-success rounded-pill" id="receipt_no"
                            name="receipt_no" placeholder="Nhập số hóa đơn" {{ !empty($infoIER) ? 'disabled' : '' }}
                            value="{{ !empty($infoIER) ? $infoIER->code : old('receipt_no', $editForm->receipt_no ?? null) }}">
                        <div class="message_error" id="receipt_no_error"></div>
                    </div>

                    <div class="mb-3 col-12">
                        <label for="note" class="form-label fw-semibold">Ghi chú</label>
                        <textarea name="note" id="note" class="form-control form-control-sm border border-success rounded"
                            {{ !empty($infoIER) ? 'disabled' : '' }} rows="5" placeholder="Nhập ghi chú...">{{ !empty($infoIER) ? $infoIER->note : old('note', $editForm->note ?? null) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5 pb-5 pt-5 mb-xl-8 shadow">
        <div class="card-header border-0">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thiết Bị Nhập</span>
            </h3>
        </div>
        <div class="container {{ !empty($infoIER) ? 'd-none' : '' }}">
            <div class="card border-0 px-8 mb-4 rounded-3">
                <div class="row mb-3">
                    <div class="mb-4 col-6">
                        <label for="equipment_code" class="{{ $required }} form-label fw-semibold">Thiết
                            bị</label>
                        <select name="equipment" id="equipment"
                            class="form-select form-select-sm border border-success rounded-pill ps-5">
                            <option value="" selected>Chọn Thiết Bị...</option>
                            @foreach ($equipmentsWithStock as $item)
                                @if ($item->inventories->sum('current_quantity') <= 25)
                                    <option value="{{ $item->code }}"
                                        class="text-danger {{ in_array($item->code, $checkList ?? []) ? 'd-none' : '' }}">
                                        {{ $item->name }} - (Tổng Tồn:
                                        {{ $item->inventories->sum('current_quantity') ?? 0 }})
                                    </option>
                                @else
                                    <option value="{{ $item->code }}"
                                        class="{{ in_array($item->code, $checkList ?? []) ? 'd-none' : '' }}">
                                        {{ $item->name }} - (Tổng Tồn:
                                        {{ $item->inventories->sum('current_quantity') ?? 0 }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div class="message_error" id="equipment_error"></div>
                    </div>

                    <div class="col-6 mb-4">
                        <label for="price" class="{{ $required }} form-label fw-semibold" id="price_label">Giá
                            nhập</label>
                        <input type="number" tabindex="10"
                            class="form-control form-control-sm border border-success rounded-pill" id="price"
                            name="price" placeholder="Nhập đơn giá">
                        <div class="message_error" id="price_error"></div>
                    </div>

                    <div class="col-6 mb-4">
                        <label for="product_date" class="{{ $required }} form-label fw-semibold"
                            id="product_date_label">Ngày sản
                            xuất</label>
                        <input type="date" tabindex="8"
                            class="form-control form-control-sm border border-success rounded-pill" id="product_date"
                            name="product_date">
                        <div class="message_error" id="product_date_error"></div>
                    </div>

                    <div class="col-6 mb-4">
                        <label for="expiry_date" class="form-label fw-semibold" id="expiry_date_label">Ngày hết
                            hạn</label>
                        <input type="date" tabindex="9"
                            class="form-control form-control-sm border border-success rounded-pill" id="expiry_date"
                            name="expiry_date">
                        <div class="message_error" id="expiry_date_error"></div>
                    </div>

                    <div class="col-3 mb-4">
                        <label for="batch_number" class="{{ $required }} form-label fw-semibold"
                            id="batch_number_label">Số lô</label>
                        <input type="text" tabindex="7"
                            class="form-control form-control-sm border border-success rounded-pill" id="batch_number"
                            name="batch_number" placeholder="Nhập số lô">
                        <div class="message_error" id="batch_number_error"></div>
                    </div>

                    <div class="col-3 mb-4">
                        <label for="quantity" class="{{ $required }} form-label fw-semibold" id="quantity_label">Số
                            lượng</label>
                        <input type="number" tabindex="11"
                            class="form-control form-control-sm border border-success rounded-pill" id="quantity"
                            name="quantity" placeholder="Nhập số lượng">
                        <div class="message_error" id="quantity_error"></div>
                    </div>

                    <div class="col-3 mb-4">
                        <label for="discount_rate" class="{{ $required }} form-label fw-semibold"
                            id="discount_rate_label">Chiết khấu
                            (%)</label>
                        <input type="text" tabindex="12"
                            class="form-control form-control-sm border border-success rounded-pill" id="discount_rate"
                            name="discount_rate" placeholder="Nhập chiết khấu (%)">
                        <div class="message_error" id="discount_rate_error"></div>
                    </div>

                    <div class="col-3 mb-4">
                        <label for="VAT" class="{{ $required }} form-label fw-semibold" id="vat_label">VAT
                            (%)</label>
                        <input type="text" tabindex="13"
                            class="form-control form-control-sm border border-success rounded-pill" id="VAT"
                            name="VAT" placeholder="Nhập thuế VAT (%)">
                        <div class="message_error" id="VAT_error"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <button style="font-size: 11px;" type="button" class="btn btn-sm btn-info rounded-pill me-2">
                        <i class="fa fa-file-excel" style="margin-bottom: 2px;"></i>Nhập Excel
                    </button>
                    <button style="font-size: 11px;" type="button" class="btn btn-sm btn-danger rounded-pill"
                        id="add_equipment_import">
                        <i class="fa fa-plus" style="margin-bottom: 2px;"></i> Thêm Thiết Bị
                    </button>
                </div>
            </div>
        </div>

        <div class="row container">
            <div class="col-md-12 ps-10">
                <div class="card border-0 shadow bg-white rounded-3">
                    <div class="table-responsive rounded bg-white shadow">
                        <table class="table table-bordered table-striped mb-0" id="table_list_equipment">
                            <thead class="table-dark">
                                <tr class="">
                                    @if (!empty($getListIERD))
                                        <th style="width: 25%;" class="ps-5">Thiết bị</th>
                                        <th style="width: 17%;">Số lô</th>
                                        <th style="width: 17%;">Giá</th>
                                        <th style="width: 8%;">SL</th>
                                        <th style="width: 1%;">NSX</th>
                                        <th style="width: 1%;">HSD</th>
                                        <th style="width: 9%;">CK</th>
                                        <th style="width: 9%;">VAT</th>
                                        <th style="width: 20%;" class="pe-5">Thành tiền</th>
                                    @else
                                        <th style="width: 24%;" class="ps-5">Thiết bị</th>
                                        <th style="width: 11%;">Số lô</th>
                                        <th style="width: 12%;">Giá</th>
                                        <th style="width: 9%;">SL</th>
                                        <th style="width: 1%;">NSX</th>
                                        <th style="width: 1%;">HSD</th>
                                        <th style="width: 9%;">CK</th>
                                        <th style="width: 9%;">VAT</th>
                                        <th style="width: 14%;" class="pe-5">Thành tiền</th>
                                        <th class="" style="width: 10%;" class="pe-5">Hành Động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="equipmentList" class="align-baseline">
                                @if (!empty($getListIERD))
                                    @foreach ($getListIERD as $item)
                                        @php
                                            $total_price =
                                                $item->price *
                                                $item->quantity *
                                                (1 - $item->discount / 100) *
                                                (1 + $item->VAT / 100);
                                        @endphp
                                        <tr id="equipment-row-{{ $item->equipment_code }}">
                                            <td class="ps-5">{{ $item->equipments->name }}</td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="text"
                                                        onkeyup="checkBatchNumberOnKeyUp(this.value, '{{ $item->equipment_code }}')"
                                                        id="batch_number_change_{{ $item->equipment_code }}"
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number" id="price_change_{{ $item->equipment_code }}"
                                                        value="{{ number_format($item->price, 0, ',', '') }}"
                                                        class="form-control form-control-sm border border-success rounded-pill"
                                                        oninput="calculateTotalPriceTop('{{ $item->equipment_code }}'); calculateTotalPriceBottom();">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="text"
                                                        id="quantity_change_{{ $item->equipment_code }}"
                                                        value="{{ $item->quantity }}" disabled
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="date"
                                                        id="product_date_change_{{ $item->equipment_code }}"
                                                        value="{{ $item->manufacture_date ? \Carbon\Carbon::parse($item->manufacture_date)->format('Y-m-d') : null }}"
                                                        style="width: 110px;"
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="date"
                                                        id="expiry_date_change_{{ $item->equipment_code }}"
                                                        value="{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : null }}"
                                                        style="width: 110px;"
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number"
                                                        id="discount_rate_change_{{ $item->equipment_code }}"
                                                        value="{{ number_format($item->discount, 0, ',', '') }}"
                                                        class="form-control form-control-sm border border-success rounded-pill"
                                                        oninput="calculateTotalPriceTop('{{ $item->equipment_code }}'); calculateTotalPriceBottom();">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number" id="vat_change_{{ $item->equipment_code }}"
                                                        value="{{ number_format($item->VAT, 0, ',', '') }}"
                                                        class="form-control form-control-sm border border-success rounded-pill"
                                                        oninput="calculateTotalPriceTop('{{ $item->equipment_code }}'); calculateTotalPriceBottom();">
                                                </div>
                                            </td>
                                            <td class="">
                                                <span
                                                    id="total_price_{{ $item->equipment_code }}">{{ number_format($total_price, 0, ',', '.') }}
                                                    VND</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if (!empty($getList))
                                    @foreach ($getList as $item)
                                        @php
                                            $total_price =
                                                $item->price *
                                                $item->quantity *
                                                (1 - $item->discount / 100) *
                                                (1 + $item->VAT / 100);
                                        @endphp
                                        <tr id="equipment-row-{{ $item->equipment_code }}">
                                            <td class="ps-5">{{ $item->equipments->name }}</td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="text" value="{{ $item->batch_number }}"
                                                        id="batch_number_change_{{ $item->equipment_code }}" disabled
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number" id="price_change_{{ $item->equipment_code }}"
                                                        value="{{ number_format($item->price, 0, ',', '') }}"
                                                        class="form-control form-control-sm border border-success rounded-pill"
                                                        oninput="calculateTotalPriceTop('{{ $item->equipment_code }}'); calculateTotalPriceBottom();">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number"
                                                        id="quantity_change_{{ $item->equipment_code }}"
                                                        value="{{ $item->quantity }}"
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="date"
                                                        id="product_date_change_{{ $item->equipment_code }}"
                                                        value="{{ \Carbon\Carbon::parse($item->manufacture_date)->format('Y-m-d') }}"
                                                        style="width: 110px;"
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="date"
                                                        id="expiry_date_change_{{ $item->equipment_code }}"
                                                        value="{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : null }}"
                                                        style="width: 110px;"
                                                        class="form-control form-control-sm border border-success rounded-pill">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number"
                                                        id="discount_rate_change_{{ $item->equipment_code }}"
                                                        value="{{ number_format($item->discount, 0, ',', '') }}"
                                                        class="form-control form-control-sm border border-success rounded-pill"
                                                        oninput="calculateTotalPriceTop('{{ $item->equipment_code }}'); calculateTotalPriceBottom();">
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex align-items-center">
                                                    <input type="number" id="vat_change_{{ $item->equipment_code }}"
                                                        value="{{ number_format($item->VAT, 0, ',', '') }}"
                                                        class="form-control form-control-sm border border-success rounded-pill"
                                                        oninput="calculateTotalPriceTop('{{ $item->equipment_code }}'); calculateTotalPriceBottom();">
                                                </div>
                                            </td>
                                            <td><span
                                                    id="total_price_{{ $item->equipment_code }}">{{ number_format($total_price, 0, ',', '.') }}
                                                    VND</span></td>
                                            <td class="text-center">
                                                <span
                                                    onclick="removeEquipment('{{ $item->equipment_code }}', '{{ $item->batch_number }}')"
                                                    class="pointer">
                                                    <i class="fas fa-trash text-danger p-0"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr id="noDataAlert"
                                    class="{{ !empty($getList) ? 'd-none' : '' }} {{ !empty($getListIERD) ? 'd-none' : '' }}">
                                    <td colspan="12" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fas fa-file-invoice"
                                                    style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông
                                                    tin
                                                    phiếu nhập trống</h5>
                                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                    Hiện tại chưa có phiếu nhập nào được thêm vào. Vui lòng kiểm tra lại
                                                    hoặc tạo mới phiếu nhập để bắt đầu.
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-8 container mt-5 pe-2" id="error_quantity_container">
                @if (!empty($getList))
                    @foreach ($getList as $item)
                        <div id="error_quantity_card_{{ $item->equipment_code }}"
                            class="card border-0 p-4 bg-light-warning rounded-0 d-none">
                            <span class="mb-1 d-none" id="price_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Giá nhập</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> là
                                bắt buộc và phải
                                lớn
                                hơn 0</span>

                            <span class="mt-1 mb-1 d-none" id="quantity_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Số lượng</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> là
                                bắt buộc
                                và phải
                                lớn hơn 0</span>

                            <span class="mt-1 mb-1 d-none" id="product_date_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Ngày sản xuất</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong>
                                là bắt
                                buộc</span>

                            <span class="mt-1 mb-1 d-none" id="expiry_date_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Ngày hết hạn</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong>
                                phải lớn
                                hơn ngày
                                sản xuất ít nhất 6 tháng</span>

                            <span class="mt-1 mb-1 d-none" id="discount_rate_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Chiết khấu</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> là
                                bắt
                                buộc và
                                phải lớn hơn 0, bé hơn 100</span>

                            <span class="mt-1 d-none" id="vat_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>VAT</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> là bắt
                                buộc và
                                phải lớn
                                hơn 0, bé hơn 100</span>
                        </div>
                    @endforeach
                @elseif (!empty($getListIERD))
                    @foreach ($getListIERD as $item)
                        <div id="error_quantity_card_2_{{ $item->equipment_code }}"
                            class="card border-0 p-4 bg-light-warning rounded-0 d-none">
                            <span class="mt-1 mb-1 d-none" id="batch_number_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Số lô</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> đã tồn
                                tại
                                trên hệ thống
                            </span>

                            <span class="mt-1 mb-1 d-none" id="list_batch_number_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Số lô</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> đã tồn
                                tại
                                trong danh sách
                            </span>
                        </div>

                        <div id="error_quantity_card_{{ $item->equipment_code }}"
                            class="card border-0 p-4 bg-light-warning rounded-0 d-none">
                            <span class="mb-1 d-none" id="price_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Giá nhập</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong>
                                là
                                bắt buộc và phải
                                lớn
                                hơn 0</span>

                            <span class="mt-1 mb-1 d-none" id="quantity_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Số lượng</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong>
                                là
                                bắt buộc
                                và phải
                                lớn hơn 0</span>

                            <span class="mt-1 mb-1 d-none" id="product_date_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Ngày sản xuất</strong> của thiết bị
                                <strong>{{ $item->equipments->name }}</strong>
                                là bắt
                                buộc</span>

                            <span class="mt-1 mb-1 d-none" id="expiry_date_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Ngày hết hạn</strong> của thiết bị
                                <strong>{{ $item->equipments->name }}</strong>
                                phải lớn
                                hơn ngày
                                sản xuất ít nhất 6 tháng</span>

                            <span class="mt-1 mb-1 d-none" id="discount_rate_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>Chiết khấu</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong>
                                là
                                bắt
                                buộc và
                                phải lớn hơn 0, bé hơn 100</span>

                            <span class="mt-1 d-none" id="vat_error_{{ $item->equipment_code }}"> <i
                                    class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                <strong>VAT</strong> của thiết bị <strong>{{ $item->equipments->name }}</strong> là bắt
                                buộc và
                                phải lớn
                                hơn 0, bé hơn 100</span>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="col-md-4 mt-5">
                <div class="card border-0 shadow p-4 mb-4 bg-white rounded-3 shadow">
                    <h6 class="mb-3 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary"></i> THỐNG KÊ PHIẾU NHẬP
                    </h6>

                    @if (!empty($getList))
                        {{-- Lấy để kiểm tra số hóa đơn --}}
                        <input type="hidden" name="request_code" id="request_code" value="{{ request('code') }}">

                        @php
                            $totalPrice = 0;
                            $totalDiscount = 0;
                            $totalVAT = 0;

                            foreach ($getList as $detail) {
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
                    @endif

                    @if (!empty($getListIERD))
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            <span class="fw-semibold">Tổng Đầu</span>
                            <span id="totalPrice" class="fw-bolder text-danger">0 VND</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Tổng Chiết Khấu</span>
                            <span id="totalDiscount" class="fw-bolder text-danger">0 VND</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Tổng VAT</span>
                            <span id="totalVAT" class="fw-bolder text-danger">0 VND</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Tổng Cuối</span>
                            <span id="totalAmount" class="fw-bolder text-danger">0 VND</span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            <span class="fw-semibold">Tổng Đầu</span>
                            <span id="totalPrice"
                                class="fw-bolder text-danger">{{ !empty($totalPrice) ? number_format($totalPrice, 0, ',', '.') : 0 }}
                                VND</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Tổng Chiết Khấu</span>
                            <span id="totalDiscount"
                                class="fw-bolder text-danger">{{ !empty($totalDiscount) ? number_format($totalDiscount, 0, ',', '.') : 0 }}
                                VND</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Tổng VAT</span>
                            <span id="totalVAT"
                                class="fw-bolder text-danger">{{ !empty($totalVAT) ? number_format($totalVAT, 0, ',', '.') : 0 }}
                                VND</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">Tổng Cuối</span>
                            <span id="totalAmount"
                                class="fw-bolder text-danger">{{ !empty($totalAmount) ? number_format($totalAmount, 0, ',', '.') : 0 }}
                                VND</span>
                        </div>
                    @endif

                    <hr class="my-4">

                    @if (!empty($getListIERD))
                        <button type="button"
                            class="btn btn-sm btn-twitter w-100 d-flex align-items-center justify-content-center rounded-pill"
                            id="import_equipment_request_create" disabled>
                            <i class="fas fa-save me-1"></i>Duyệt Phiếu
                        </button>
                    @else
                        <button type="button" name="status" value="0"
                            class="btn btn-sm btn-info w-100 mb-2 d-flex align-items-center justify-content-center rounded-pill {{ $d_none_temp }}"
                            id="import_equipment_request_temp">
                            <i class="fas fa-cloud-arrow-down me-1"></i>Lưu Tạm
                        </button>

                        <button type="button"
                            class="btn btn-sm btn-twitter w-100 d-flex align-items-center justify-content-center rounded-pill {{ $d_none_save }}"
                            id="import_equipment_request_save">
                            <i class="fas fa-save me-1"></i>Tạo Phiếu
                        </button>

                        <button type="button"
                            class="btn btn-sm btn-twitter w-100 d-flex align-items-center justify-content-center rounded-pill {{ $d_none_update }}"
                            id="import_equipment_request_update">
                            <i class="fas fa-save me-1"></i>Cập Nhật
                        </button>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Form thêm nhà cung cấp -->
    <div class="modal fade" id="add_modal_ncc" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="add_modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="add_modalLabel">Thêm Nhà Cung Cấp</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="required fs-5 er mb-2">Tên Nhà Cung Cấp</label>
                        <input type="text" class="form-control form-control-sm border border-success rounded-pill"
                            placeholder="Tên nhà cung cấp.." name="name" id="supplier_type_name" />
                        <div class="message_error" id="show-err-supplier-type"></div>
                    </div>
                </div>
                <div class="modal-body pt-0">
                    <div class="overflow-auto" style="max-height: 300px;">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="erer bg-success">
                                    <th class="ps-3" style="width: 70%;">Tên Nhà Cung Cấp</th>
                                    <th class="pe-3 text-center" style="width: 30%;">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="supplier-list">
                                @foreach ($suppliers as $item)
                                    <tr class="hover-table pointer" id="supplier-{{ $item->code }}">
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#delete_modal_supplier_type"
                                                onclick="setDeleteForm('{{ route('equipment_request.delete_supplier', $item->code) }}', '{{ $item->name }}')">
                                                <i class="fa fa-trash p-0"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-twitter rounded-pill"
                        id="submit_supplier_type">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form xóa nhà cung cấp --}}
    <div class="modal fade" id="delete_modal_supplier_type" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="deleteModalLabel">Xóa Nhà Cung Cấp</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="text-danger" id="delete-supplier-message"></h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#add_modal_ncc">Trở Lại</button>
                    <button type="button" class="btn btn-sm btn-danger rounded-pill"
                        id="confirm-delete-supplier">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    @include('warehouse.import_warehouse.modal')
@endsection

@section('scripts')
    <script>
        let addedEquipments = [];
        let equipmentData = [];
        let batchData = [];
        let updateReceiptEquipmentCodeArr = [];
        let updateReceiptBatchNumberArr = [];

        if (document.getElementById('import_equipment_request_temp') && document.getElementById(
                'import_equipment_request_save') && document.getElementById('import_equipment_request_update')) {
            document.getElementById('import_equipment_request_temp').addEventListener('click', function(event) {
                event.preventDefault();
                handleImportEquipmentRequest(3);
            });

            document.getElementById('import_equipment_request_save').addEventListener('click', function(event) {
                event.preventDefault();
                handleImportEquipmentRequest(4);
            });

            document.getElementById('import_equipment_request_update').addEventListener('click', function(event) {
                event.preventDefault();
                handleImportEquipmentRequest(4);
            });
        } else {
            document.getElementById('import_equipment_request_create').addEventListener('click', function(event) {
                event.preventDefault();
                handleImportEquipmentRequest(1);
            });
        }

        // Thêm phiếu nhập
        function handleImportEquipmentRequest(importEquipmentStatus) {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(async () => {
                let supplier_code = document.getElementById('supplier_code').value.trim();
                let receipt_no = document.getElementById('receipt_no').value.trim();
                let note = document.getElementById('note').value.trim();
                let request_code = document.getElementById('request_code') ? document.getElementById(
                    'request_code').value.trim() : null;
                let supplier_code_error = document.getElementById('supplier_code_error');
                let receipt_no_error = document.getElementById('receipt_no_error');
                let equipment_error = document.getElementById('equipment_error');
                let equipmentList = getEquipmentList();

                supplier_code_error.innerText = '';
                receipt_no_error.innerText = '';
                equipment_error.innerText = '';

                calculateTotals();

                let hasError = false;

                if (supplier_code == 0) {
                    supplier_code_error.innerText = "Vui lòng chọn nhà cung cấp";
                    hasError = true;
                }

                if (!receipt_no) {
                    receipt_no_error.innerText = "Vui lòng thêm số hóa đơn";
                    hasError = true;
                } else {
                    const receiptNo = await checkReceiptNo(receipt_no, request_code);
                    if (receiptNo) {
                        receipt_no_error.innerText = "Số hóa đơn đã tồn tại trên hệ thống, hãy thử lại";
                        hasError = true;
                    }
                }

                if (equipmentList.length === 0) {
                    equipment_error.innerText = "Vui lòng thêm thiết bị yêu cầu";
                    hasError = true;
                }

                equipmentList.forEach((item) => {
                    let six_month;

                    if (item.product_date) {
                        six_month = new Date(item.product_date);
                        six_month.setMonth(six_month
                            .getMonth() + 6);
                    }

                    if (!item.price || item.price <= 0) {
                        document.getElementById(`error_quantity_card_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        document.getElementById(`price_error_${item.equipment_code}`).classList.remove(
                            'd-none');
                        hasError = true;
                    } else {
                        document.getElementById(`price_error_${item.equipment_code}`).classList.add(
                            'd-none');
                    }

                    if (!item.quantity || item.quantity <= 0) {
                        document.getElementById(`error_quantity_card_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        document.getElementById(`quantity_error_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        hasError = true;
                    } else {
                        document.getElementById(`quantity_error_${item.equipment_code}`).classList.add(
                            'd-none');
                    }

                    if (!item.product_date) {
                        document.getElementById(`error_quantity_card_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        document.getElementById(`product_date_error_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        hasError = true;
                    } else {
                        document.getElementById(`product_date_error_${item.equipment_code}`).classList
                            .add(
                                'd-none');
                    }

                    if (item.expiry_date) {
                        const expiryDateObj = new Date(item.expiry_date);

                        if (expiryDateObj <= new Date(item.product_date) || expiryDateObj <
                            six_month) {
                            document.getElementById(`error_quantity_card_${item.equipment_code}`)
                                .classList
                                .remove(
                                    'd-none');
                            document.getElementById(`expiry_date_error_${item.equipment_code}`)
                                .classList
                                .remove(
                                    'd-none');
                            hasError = true;
                        } else {
                            document.getElementById(`expiry_date_error_${item.equipment_code}`)
                                .classList
                                .add(
                                    'd-none');
                        }
                    }

                    if (!item.discount_rate || item.discount_rate <= 0) {
                        document.getElementById(`error_quantity_card_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        document.getElementById(`discount_rate_error_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        hasError = true;
                    } else {
                        document.getElementById(`discount_rate_error_${item.equipment_code}`).classList
                            .add(
                                'd-none');
                    }

                    if (!item.vat || item.vat <= 0) {
                        document.getElementById(`error_quantity_card_${item.equipment_code}`).classList
                            .remove(
                                'd-none');
                        document.getElementById(`vat_error_${item.equipment_code}`).classList.remove(
                            'd-none');
                        hasError = true;
                    } else {
                        document.getElementById(`vat_error_${item.equipment_code}`).classList.add(
                            'd-none');
                    }
                });

                if (hasError) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    return;
                }

                let formData = new FormData();
                formData.append('supplier_code', supplier_code);
                formData.append('receipt_no', receipt_no);
                formData.append('note', note);
                formData.append('importEquipmentStatus', importEquipmentStatus);
                formData.append('equipment_list', JSON.stringify(equipmentList));

                fetch('{{ $action }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message);
                            window.location.href = "{{ route('warehouse.import') }}";
                        } else {
                            toastr.error(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });

            }, 500);
        }

        // Thêm thiết bị nhập
        document.getElementById('add_equipment_import').addEventListener('click', async function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(async () => {
                let noDataAlert = document.getElementById('noDataAlert');
                let equipment = document.getElementById('equipment').value.trim();
                let price = document.getElementById('price').value.trim();
                let product_date = document.getElementById('product_date').value.trim();
                let expiry_date = document.getElementById('expiry_date').value.trim();
                let batch_number = document.getElementById('batch_number').value.trim();
                let quantity = document.getElementById('quantity').value.trim();
                let discount_rate = document.getElementById('discount_rate').value.trim();
                let VAT = document.getElementById('VAT').value.trim();
                let getEquipmentLists = getEquipmentList();

                let equipment_error = document.getElementById('equipment_error');
                let price_error = document.getElementById('price_error');
                let product_date_error = document.getElementById('product_date_error');
                let expiry_date_error = document.getElementById('expiry_date_error');
                let batch_number_error = document.getElementById('batch_number_error');
                let quantity_error = document.getElementById('quantity_error');
                let discount_rate_error = document.getElementById('discount_rate_error');
                let VAT_error = document.getElementById('VAT_error');

                // Reset lỗi
                equipment_error.innerText = '';
                price_error.innerText = '';
                product_date_error.innerText = '';
                expiry_date_error.innerText = '';
                batch_number_error.innerText = '';
                quantity_error.innerText = '';
                discount_rate_error.innerText = '';
                VAT_error.innerText = '';

                let hasError = false;
                let six_months_after_product_date;

                // 6 months after product date
                if (product_date) {
                    six_months_after_product_date = new Date(product_date);
                    six_months_after_product_date.setMonth(six_months_after_product_date
                        .getMonth() + 6);
                }

                // Validation
                if (!equipment) {
                    equipment_error.innerText = "Vui lòng chọn thiết bị cần mua";
                    hasError = true;
                }

                if (!price || price <= 0) {
                    price_error.innerText = "Vui lòng điền giá nhập và phải lớn hơn 0";
                    hasError = true;
                }

                if (!product_date) {
                    product_date_error.innerText = "Vui lòng thêm ngày sản xuất";
                    hasError = true;
                } else {
                    const currentDate = new Date();
                    const productDateObj = new Date(product_date);

                    if (productDateObj > currentDate) {
                        product_date_error.innerText =
                            "Ngày sản xuất không được lớn hơn ngày hiện tại";
                        hasError = true;
                    }
                }

                if (expiry_date) {
                    const expiryDateObj = new Date(expiry_date);

                    if (expiryDateObj <= new Date(product_date) || expiryDateObj <
                        six_months_after_product_date) {
                        expiry_date_error.innerText =
                            "Ngày hết hạn phải lớn hơn ngày sản xuất 6 tháng";
                        hasError = true;
                    }
                }

                if (!batch_number) {
                    batch_number_error.innerText = "Vui lòng nhập số lô";
                    hasError = true;
                } else {
                    const batchExists = await checkBatchNumber(batch_number, equipment);

                    if (batchExists) {
                        batch_number_error.innerText =
                            "Số lô này đã bị trùng với thiết bị nhập khác trên hệ thống";
                        hasError = true;
                    } else {
                        getEquipmentLists.forEach((item) => {
                            if (batch_number === item.batch_number) {
                                batch_number_error.innerText =
                                    "Số lô này đã bị trùng với thiết bị nhập khác trong danh sách";
                                hasError = true;
                            }
                        });
                    }
                }

                if (!quantity || quantity <= 0) {
                    quantity_error.innerText = "Vui lòng nhập số lượng và phải lớn hơn 0";
                    hasError = true;
                }

                if (!discount_rate || discount_rate <= 0 || discount_rate > 100) {
                    discount_rate_error.innerText = "Chiết khấu phải lớn hơn 0 và bé hơn 100";
                    hasError = true;
                }

                if (!VAT || VAT <= 0 || VAT > 100) {
                    VAT_error.innerText = "Thuế VAT phải lớn hơn 0 và bé hơn 100";
                    hasError = true;
                }

                // If any validation errors, stop execution and hide loading
                if (hasError) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    return;
                }

                // Prepare and send data if no errors
                let formData = new FormData();
                formData.append('equipment', equipment);
                formData
                    .append('price', price);
                formData.append('product_date', product_date);
                formData
                    .append('expiry_date', expiry_date);
                formData.append('batch_number',
                    batch_number);
                formData.append('quantity', quantity);
                formData.append(
                    'discount_rate', discount_rate);
                formData.append('VAT', VAT);

                const total_price = price * quantity * (1 - discount_rate / 100) * (1 + VAT /
                    100);

                fetch('{{ route('warehouse.create_import') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Kiểm tra xem thiết bị đã được thêm chưa
                            if (!addedEquipments.includes(data.equipment_code)) {
                                addedEquipments.push(data.equipment_code);
                            }

                            noDataAlert.classList.add('d-none');

                            // Thêm thiết bị vào danh sách trong bảng mà không cần tải lại trang
                            let tableBody = document.getElementById('equipmentList');

                            let newRow = document.createElement('tr');

                            newRow.id = `equipment-row-${data.equipment_code}`;

                            newRow.innerHTML = `
                                <td class="ps-5">${data.equipment_name}</td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="text" value="${data.batch_number}"
                                            id="batch_number_change_${data.equipment_code}" disabled
                                            class="form-control form-control-sm border border-success rounded-pill">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="price_change_${data.equipment_code}"
                                            value="${parseInt(data.price, 10)}"
                                            class="form-control form-control-sm border border-success rounded-pill"
                                            oninput="calculateTotalPriceTop('${data.equipment_code}'); calculateTotalPriceBottom();">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="quantity_change_${data.equipment_code}"
                                            value="${parseInt(data.quantity, 10)}"
                                            class="form-control form-control-sm border border-success rounded-pill"
                                            oninput="calculateTotalPriceTop('${data.equipment_code}'); calculateTotalPriceBottom();">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="date" id="product_date_change_${data.equipment_code}"
                                            value="${ new Date(data.product_date).toISOString().split('T')[0] }" style="width: 110px;"
                                            class="form-control form-control-sm border border-success rounded-pill">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="date" id="expiry_date_change_${data.equipment_code}"
                                            value="${ data.expiry_date ? new Date(data.expiry_date).toISOString().split('T')[0] : '' }" style="width: 110px;"
                                            class="form-control form-control-sm border border-success rounded-pill">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="discount_rate_change_${data.equipment_code}"
                                            value="${data.discount_rate}"
                                            class="form-control form-control-sm border border-success rounded-pill"
                                            oninput="calculateTotalPriceTop('${data.equipment_code}'); calculateTotalPriceBottom();">
                                    </div>
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="vat_change_${data.equipment_code}"
                                            value="${data.vat}"
                                            class="form-control form-control-sm border border-success rounded-pill"
                                            oninput="calculateTotalPriceTop('${data.equipment_code}'); calculateTotalPriceBottom();">
                                    </div>
                                </td>
                                <td><span id="total_price_${data.equipment_code}">${total_price
                                    .toLocaleString("vi-VN", { style: "currency", currency: "VND" })
                                    .replace("₫", " VND")
                                    .replace(",00", "")}</span></td>
                                <td class="text-center">
                                    <span onclick="removeEquipment('${data.equipment_code}', '${data.batch_number}')" class="pointer">
                                        <i class="fas fa-trash text-danger p-0"></i>
                                    </span>
                                </td>
                                `;

                            tableBody.appendChild(newRow);

                            calculateTotals();

                            let error_quantity_container = document.getElementById(
                                'error_quantity_container');

                            let quantity_Label = document.getElementById('quantity_label')
                                .textContent;
                            let price_Label = document.getElementById('price_label')
                                .textContent;
                            let batch_number_Label = document.getElementById(
                                    'batch_number_label')
                                .textContent;
                            let product_date_Label = document.getElementById(
                                    'product_date_label')
                                .textContent;
                            let expiry_date_Label = document.getElementById('expiry_date_label')
                                .textContent;
                            let discount_rate_Label = document.getElementById(
                                    'discount_rate_label')
                                .textContent;
                            let vat_Label = document.getElementById('vat_label').textContent;

                            let newDivErr = document.createElement('div');

                            newDivErr.id = `error_quantity_card_${data.equipment_code}`;

                            newDivErr.classList.add('card', 'border-0', 'p-4',
                                'bg-light-warning',
                                'rounded-0', 'd-none');

                            newDivErr.innerHTML = `
                                <span class="mb-1 d-none" id="price_error_${data.equipment_code}"> <i class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                    <strong>${price_Label}</strong> của thiết bị <strong>${data.equipment_name}</strong> là bắt buộc và phải lớn hơn 0</span>

                                <span class="mt-1 mb-1 d-none" id="quantity_error_${data.equipment_code}"> <i class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                    <strong>${quantity_Label}</strong> của thiết bị <strong>${data.equipment_name}</strong> là bắt buộc và phải lớn hơn 0</span>
                                    
                                <span class="mt-1 mb-1 d-none" id="product_date_error_${data.equipment_code}"> <i class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                    <strong>${product_date_Label}</strong> của thiết bị <strong>${data.equipment_name}</strong> là bắt buộc</span>
                                    
                                <span class="mt-1 mb-1 d-none" id="expiry_date_error_${data.equipment_code}"> <i class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                    <strong>${expiry_date_Label}</strong> của thiết bị <strong>${data.equipment_name}</strong> phải lớn hơn ngày sản xuất ít nhất 6 tháng</span>
                                    
                                <span class="mt-1 mb-1 d-none" id="discount_rate_error_${data.equipment_code}"> <i class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                    <strong>${discount_rate_Label}</strong> của thiết bị <strong>${data.equipment_name}</strong> là bắt buộc và phải lớn hơn 0, bé hơn 100</span>
                                    
                                <span class="mt-1 d-none" id="vat_error_${data.equipment_code}"> <i class ="fa fa-warning text-warning me-2" style="font-size: 18px;"></i>
                                    <strong>${vat_Label}</strong> của thiết bị <strong>${data.equipment_name}</strong> là bắt buộc và phải lớn hơn 0, bé hơn 100</span>
                            `;

                            error_quantity_container.appendChild(newDivErr);

                            // Reset form sau khi thêm thành công
                            document.getElementById('equipment').value = "";
                            document.getElementById('price').value = "";
                            document.getElementById('product_date').value = "";
                            document.getElementById('expiry_date').value = "";
                            document.getElementById('batch_number').value = "";
                            document.getElementById('quantity').value = "";
                            document.getElementById('discount_rate').value = "";
                            document.getElementById('VAT').value = "";

                            // Ẩn các tùy chọn đã thêm trong danh sách thiết bị
                            let equipmentOptions = document.querySelectorAll(
                                '#equipment option');
                            equipmentOptions.forEach(option => {
                                if (addedEquipments.includes(option.value)) {
                                    option.classList.add('d-none');
                                }
                            });

                            toastr.success("Đã thêm thiết bị vào danh sách");
                        } else {
                            alert('Có lỗi xảy ra');
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });
            }, 500);
        });

        // Thống kê phiếu nhập
        function calculateTotals() {
            let totalPrice = 0;
            let totalDiscount = 0;
            let totalVAT = 0;
            let totalAmount = 0;
            let getEquipmentLists = getEquipmentList();

            getEquipmentLists.forEach((equipmentCalculateTotals) => {
                const total_price = equipmentCalculateTotals.price * equipmentCalculateTotals.quantity;
                const total_price_calculate = equipmentCalculateTotals.price * equipmentCalculateTotals
                    .quantity * (
                        1 - equipmentCalculateTotals.discount_rate / 100) * (1 + equipmentCalculateTotals
                        .vat / 100);
                const itemDiscount =
                    (equipmentCalculateTotals.price * equipmentCalculateTotals.quantity *
                        equipmentCalculateTotals
                        .discount_rate) / 100;
                const itemVAT =
                    ((equipmentCalculateTotals.price * equipmentCalculateTotals.quantity - itemDiscount) *
                        equipmentCalculateTotals.vat) /
                    100;
                const itemTotal = parseFloat(total_price_calculate);

                totalPrice += total_price;
                totalDiscount += itemDiscount;
                totalVAT += itemVAT;
                totalAmount += itemTotal;
            });

            document.getElementById("totalPrice").textContent =
                totalPrice.toLocaleString("vi-VN", {
                    style: "currency",
                    currency: "VND",
                })
                .replace("₫", "VND")
                .replace(",00", "");

            document.getElementById("totalDiscount").textContent =
                totalDiscount.toLocaleString("vi-VN", {
                    style: "currency",
                    currency: "VND",
                })
                .replace("₫", "VND")
                .replace(",00", "");

            document.getElementById("totalVAT").textContent = totalVAT.toLocaleString(
                    "vi-VN", {
                        style: "currency",
                        currency: "VND"
                    }
                )
                .replace("₫", "VND")
                .replace(",00", "");

            document.getElementById("totalAmount").textContent =
                totalAmount.toLocaleString("vi-VN", {
                    style: "currency",
                    currency: "VND",
                })
                .replace("₫", "VND")
                .replace(",00", "");
        }

        // Lấy dữ liệu từ danh sách thiết bị
        function getEquipmentList() {
            equipmentList = [];
            let rows = document.querySelectorAll('#table_list_equipment tbody tr');

            rows.forEach((row) => {
                if (row.id === "noDataAlert") return;

                let equipmentCode = row.id.split('-')[2]; // Lấy mã thiết bị từ ID của hàng
                let priceInput = document.getElementById(`price_change_${equipmentCode}`);
                let priceValue = priceInput.value.trim();
                let quantityInput = document.getElementById(`quantity_change_${equipmentCode}`);
                let quantityValue = quantityInput.value.trim();
                let batch_numberInput = document.getElementById(`batch_number_change_${equipmentCode}`);
                let batch_numberValue = batch_numberInput.value.trim();
                let product_dateInput = document.getElementById(`product_date_change_${equipmentCode}`);
                let product_dateValue = product_dateInput.value.trim();
                let expiry_dateInput = document.getElementById(`expiry_date_change_${equipmentCode}`);
                let expiry_dateValue = expiry_dateInput.value.trim();
                let discount_rateInput = document.getElementById(`discount_rate_change_${equipmentCode}`);
                let discount_rateValue = discount_rateInput.value.trim();
                let vatInput = document.getElementById(`vat_change_${equipmentCode}`);
                let vatValue = vatInput.value.trim();

                // Đưa dữ liệu vào mảng
                equipmentList.push({
                    equipment_code: equipmentCode,
                    price: priceValue,
                    quantity: quantityValue,
                    batch_number: batch_numberValue,
                    product_date: product_dateValue,
                    expiry_date: expiry_dateValue ?? null,
                    discount_rate: discount_rateValue,
                    vat: vatValue,
                });

            });

            return equipmentList;
        }

        function checkAllBatchNumbers() {
            const allBatchInputs = document.querySelectorAll('input[id^="batch_number_change_"]');
            let allFilled = true;

            allBatchInputs.forEach(input => {
                if (!input.value) {
                    allFilled = false;
                }
            });

            const submitButton = document.getElementById('import_equipment_request_create');
            submitButton.disabled = !allFilled;
        }

        // Cập nhật hàm kiểm tra số lô để gọi checkAllBatchNumbers
        function checkBatchNumberOnKeyUp(batch_number, equipment_code) {
            setTimeout(async function() {
                const batchExists = await checkBatchNumber(batch_number, equipment_code);

                let isDuplicateInList = false;
                const allBatchInputs = document.querySelectorAll('input[id^="batch_number_change_"]');

                allBatchInputs.forEach(input => {
                    if (input.id !== `batch_number_change_${equipment_code}` && input.value ===
                        batch_number) {
                        isDuplicateInList = true;
                    }
                });

                if (batchExists || isDuplicateInList) {
                    document.getElementById('import_equipment_request_create').disabled = true;
                    document.getElementById(`error_quantity_card_2_${equipment_code}`).classList.remove(
                        'd-none');
                    if (batchExists) {
                        document.getElementById(`batch_number_error_${equipment_code}`).classList.remove(
                            'd-none');
                        document.getElementById(`list_batch_number_error_${equipment_code}`).classList.add(
                            'd-none');
                    } else {
                        document.getElementById(`list_batch_number_error_${equipment_code}`).classList.remove(
                            'd-none');
                        document.getElementById(`batch_number_error_${equipment_code}`).classList.add('d-none');
                    }
                    return false;
                } else {
                    document.getElementById(`error_quantity_card_2_${equipment_code}`).classList.add('d-none');
                    document.getElementById(`batch_number_error_${equipment_code}`).classList.add('d-none');
                    document.getElementById(`list_batch_number_error_${equipment_code}`).classList.add(
                        'd-none');
                }

                // Kiểm tra trạng thái tất cả số lô
                checkAllBatchNumbers();

            }, 100);
        }

        function checkBatchNumber(batch_number_check, equipment_code) {
            if (updateReceiptEquipmentCodeArr.includes(batch_number_check) && updateReceiptBatchNumberArr.includes(
                    equipment_code)) {
                return Promise.resolve(false);
            } else {
                const formData = new FormData();
                formData.append('batch_number', batch_number_check);
                formData.append('equipment_code', equipment_code);

                return fetch('{{ route('warehouse.check_batch_number') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Kiểm tra xem success có phải true không
                        if (data.success === true) {
                            return false; // Không có lỗi, batch number hợp lệ
                        } else {
                            return true; // Batch number bị trùng
                        }
                    })
                    .catch(error => {
                        return Promise.reject('fetch_error');
                    });
            }
        }

        function checkReceiptNo(receipt_no, request_code) {
            const formData = new FormData();
            formData.append('receipt_no', receipt_no);
            formData.append('code', request_code);

            return fetch('{{ route('warehouse.check_receipt_no') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Kiểm tra xem success có phải true không
                    if (data.success === true) {
                        return true; // Bị trùng
                    } else {
                        return false; // Không có lỗi
                    }
                })
                .catch(error => {
                    return Promise.reject('fetch_error');
                });
        }

        // Xóa thiết bị trong danh sách
        function removeEquipment(equipmentCode, batchNumber) {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                updateReceiptEquipmentCodeArr.push(equipmentCode);
                updateReceiptBatchNumberArr.push(batchNumber);

                // Tìm hàng trong bảng dựa trên mã thiết bị
                let row = document.getElementById(`equipment-row-${equipmentCode}`);
                if (row) {
                    row.remove();
                }

                // Kiểm tra xem bảng có còn hàng nào không và hiển thị thông báo "Không Có Dữ Liệu"
                let tableBody = document.querySelector('#table_list_equipment tbody');
                if (tableBody.rows.length === 1) {
                    document.getElementById('noDataAlert').classList.remove('d-none');
                }

                // Bỏ ẩn các tùy chọn thiết bị đã thêm trong danh sách
                let equipmentOptions = document.querySelectorAll('#equipment option');
                equipmentOptions.forEach(option => {
                    if (option.value === equipmentCode) {
                        option.classList.remove('d-none');
                    }
                });

                // Cập nhật lại mảng thiết bị đã thêm
                addedEquipments = addedEquipments.filter(code => code !== equipmentCode);

                calculateTotals();

                toastr.success("Đã xóa thiết bị khỏi danh sách");

                document.getElementById('loading').style.display = 'none';
                document.getElementById('loading-overlay').style.display = 'none';
                this.disabled = false;
            }, 500);
        }

        function calculateTotalPriceTop(equipment_code) {
            // Lấy giá trị từ các trường input
            const price = parseFloat(document.getElementById(`price_change_${equipment_code}`).value.replace(/,/g, '')) ||
                0;
            const quantity = parseFloat(document.getElementById(`quantity_change_${equipment_code}`).value.replace(/,/g,
                '')) || 0;
            const discount = parseFloat(document.getElementById(`discount_rate_change_${equipment_code}`).value.replace(
                /,/g, '')) || 0;
            const vat = parseFloat(document.getElementById(`vat_change_${equipment_code}`).value.replace(/,/g, '')) || 0;

            // Tính toán thành tiền
            const discountedPrice = price * quantity * (1 - discount / 100);
            const totalPrice = discountedPrice * (1 + vat / 100);

            // Định dạng lại thành tiền
            const formattedTotalPrice = totalPrice.toLocaleString('vi-VN', {
                    style: 'currency',
                    currency: 'VND',
                    minimumFractionDigits: 0
                })
                .replace("₫", "VND")
                .replace(",00", "");

            // Cập nhật thành tiền trong HTML
            document.getElementById(`total_price_${equipment_code}`).innerText = formattedTotalPrice;
        }


        function calculateTotalPriceBottom() {
            let totalPrice = 0;
            let totalDiscount = 0;
            let totalVAT = 0;
            let totalAmount = 0;

            // Lặp qua các hàng sản phẩm
            document.querySelectorAll('tr[id^="equipment-row-"]').forEach(row => {
                const equipment_code = row.id.replace('equipment-row-', '');
                const price = parseFloat(document.getElementById(`price_change_${equipment_code}`).value.replace(
                    /,/g, '')) || 0;
                const quantity = parseFloat(document.getElementById(`quantity_change_${equipment_code}`).value
                    .replace(/,/g, '')) || 0;
                const discount = parseFloat(document.getElementById(`discount_rate_change_${equipment_code}`).value
                    .replace(/,/g, '')) || 0;
                const vat = parseFloat(document.getElementById(`vat_change_${equipment_code}`).value.replace(/,/g,
                    '')) || 0;

                const discountedPrice = price * quantity * (discount / 100); // Chiết khấu
                const subtotal = price * quantity - discountedPrice; // Sau chiết khấu
                const vatAmount = subtotal * (vat / 100); // VAT

                totalPrice += price * quantity; // Tổng đầu (trước chiết khấu và VAT)
                totalDiscount += discountedPrice; // Tổng chiết khấu
                totalVAT += vatAmount; // Tổng VAT
                totalAmount += subtotal + vatAmount; // Tổng cuối (sau chiết khấu và VAT)
            });

            // Định dạng và cập nhật các giá trị vào HTML
            document.getElementById('totalPrice').innerText = totalPrice.toLocaleString('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).replace("₫", "VND")
                .replace(",00", "");
            document.getElementById('totalDiscount').innerText = totalDiscount.toLocaleString('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                })
                .replace("₫", "VND")
                .replace(",00", "");
            document.getElementById('totalVAT').innerText = totalVAT.toLocaleString('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                })
                .replace("₫", "VND")
                .replace(",00", "");
            document.getElementById('totalAmount').innerText = totalAmount.toLocaleString('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                })
                .replace("₫", "VND")
                .replace(",00", "");
        }
    </script>
    <script>
        // Thêm nhà cung cấp
        document.getElementById('submit_supplier_type').addEventListener('click', function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                let supplierTypeName = document.getElementById('supplier_type_name').value.trim();
                let equipment_error = document.getElementById('show-err-supplier-type');
                let existingSuppliers = Array.from(document.querySelectorAll(
                    '#supplier-list tr td:first-child')).map(td => td.textContent.trim());

                if (supplierTypeName === '') {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    equipment_error.innerText = 'Vui lòng nhập tên nhà cung cấp';
                    supplierTypeName.focus();
                }

                if (existingSuppliers.includes(supplierTypeName)) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    equipment_error.innerText = 'Nhà cung cấp đã tồn tại';
                    supplierTypeName.focus();
                }

                equipment_error.innerText = '';

                let formData = new FormData();
                formData.append('name', supplierTypeName);

                fetch('{{ route('equipment_request.create_import') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Thêm thiết bị vào danh sách trong bảng mà không cần tải lại trang
                            let tableBodySupplier = document.getElementById('supplier-list');
                            let newRowSupplier = document.createElement('tr');
                            newRowSupplier.id = `supplier-${data.code}`;
                            newRowSupplier.className = `pointer`;

                            newRowSupplier.innerHTML =
                                `
                            <td>${data.name}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm rounded-pill" data-bs-toggle="modal"
                                    data-bs-target="#delete_modal_supplier_type"
                                    onclick="setDeleteForm('{{ route('equipment_request.delete_supplier', '') }}/` +
                                data.code + `', '` + data.name + `')">
                                    <i class="fa fa-trash p-0"></i>
                                </button>
                            </td>
                            `;

                            tableBodySupplier.prepend(newRowSupplier);

                            let selectOptionSupplier = document.getElementById('supplier_code');
                            let newOption = document.createElement('option');
                            newOption.value = data.code;
                            newOption.textContent = data.name;
                            newOption.id = `option_supplier_${data.code}`;

                            let defaultOption = selectOptionSupplier.querySelector('option[value="0"]');
                            selectOptionSupplier.insertBefore(newOption, defaultOption
                                .nextSibling);

                            toastr.success("Đã thêm nhà cung cấp");

                            document.getElementById('supplier_type_name').value = "";
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });

                document.getElementById('loading').style.display = 'none';
                document.getElementById('loading-overlay').style.display = 'none';
                this.disabled = false;
            }, 500);
        });

        // Xóa nhà cung cấp
        let deleteActionUrl = '';

        function setDeleteForm(actionUrl, supplierName) {
            deleteActionUrl = actionUrl;
            document.getElementById('delete-supplier-message').innerText =
                `Bạn có chắc chắn muốn xóa nhà cung cấp "${supplierName}" này?`;
        }

        // Xác nhận xóa nhà cung cấp
        document.getElementById('confirm-delete-supplier').addEventListener('click', function() {

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                fetch(deleteActionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`supplier-${data.supplier.code}`).remove();
                            $('#delete_modal_supplier_type').modal('hide');
                            $('#add_modal_ncc').modal('show');
                            document.getElementById(`option_supplier_${data.supplier.code}`).classList
                                .add(
                                    'd-none');
                            toastr.success("Đã xóa nhà cung cấp");
                        } else {
                            toastr.error(
                                "Không thể xóa nhà cung cấp này vì đã có giao dịch trong hệ thống");
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });
            }, 500);
        });
    </script>
@endsection

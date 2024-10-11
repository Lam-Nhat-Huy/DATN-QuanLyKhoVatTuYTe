@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@php
    if ($action == 'create') {
        $action = route('equipment_request.store_export');

        $button_text = 'Tạo';
    } else {
        $action = route('equipment_request.edit_export');

        $button_text = 'Cập Nhật';
    }
@endphp

@section('content')
    <form class="form" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        <div class="card mb-5 mb-xl-8 pb-5">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">{{ $title_form }}</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('equipment_request.export') }}" class="btn btn-sm btn-dark rounded-pill">
                        <span class="align-items-center d-flex">
                            <i class="fa fa-arrow-left me-1"></i>
                            Trở Lại
                        </span>
                    </a>
                </div>
            </div>
            <div class="py-5 px-lg-17">

                <div class="me-n7 pe-7">

                    <div class="row align-items-center mb-5">

                        <div class="col-md-6 fv-row">

                            <label class="required fs-5 fw-bold mb-3">Phòng Ban</label>

                            <select name="" class="form-select form-select-sm form-select-solid setupSelect2">
                                <option value="0">Chọn Phòng Ban...</option>
                                <option value="">
                                    Khoa Cấp Cứu
                                </option>
                                <option value="">
                                    Phòng Khám
                                </option>
                            </select>
                        </div>


                        <div class="col-md-6 fv-row">

                            <label class="fs-5 fw-bold mb-2">Ghi Chú</label>


                            <input type="text" class="form-control form-control-sm border border-success rounded-pill"
                                placeholder="Nhập ghi chú cho phiếu xuất.." name="last-name" />

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-5 mb-xl-8">
            <form class="form pt-5" action="" enctype="multipart/form-data">
                @csrf
                <div class="py-5 px-lg-17">

                    <div class="me-n7 pe-7">

                        <div class="row align-items-center mb-5">

                            <div class="col-md-6 fv-row">

                                <label class="required fs-5 fw-bold mb-3">Thiết Bị</label>

                                <select name="equipment" class="form-select form-select-sm form-select-solid setupSelect2">
                                    <option value="">Chọn Thiết Bị...</option>
                                    {{-- @foreach ($AllEquipment as $item)
                                        <option value="{{ $item['id'] }}"
                                            class="{{ $item['quantity'] <= 10 || \Carbon\Carbon::parse($item['expiry'])->diffInDays(now(), true) < 10 ? 'text-danger' : '' }}">
                                            {{ $item['equipment_name'] }} - ({{ $item['description'] }}) - (Tổng Tồn:
                                            {{ $item['quantity'] }})
                                        </option>
                                    @endforeach --}}
                                    <option value="">Băng Gạc - Số Lượng: 10</option>
                                </select>
                            </div>


                            <div class="col-md-6 fv-row">

                                <label class="fs-5 fw-bold mb-2">Số Lượng</label>


                                <input type="number"
                                    class="form-control form-control-sm border border-success rounded-pill" value="0"
                                    name="quantity" />

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer flex-right border-0 pe-0">
                        <button type="submit" class="btn btn-success btn-sm rounded-pill">
                            Thêm Thiết Bị
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mb-5 mb-xl-8 pt-5">
            <div class="card-body py-3 px-17">
                <div class="table-responsive rounded">
                    <table class="table table-striped align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder bg-success">
                                <th class="">Thiết Bị</th>
                                <th class="">Đơn Vị</th>
                                <th class="">Số Lượng</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>
                                    băng Gạc
                                </td>
                                <td>
                                    Bịch
                                </td>
                                <td>
                                    100
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger rounded-pill"><i
                                            class="fa fa-trash"></i>Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer flex-right pe-0">
                    <button type="submit" class="btn btn-twitter btn-sm rounded-pill">
                        {{ $button_text }}
                    </button>
                </div>
            </div>

        </div>
    </form>
@endsection

@section('scripts')
@endsection

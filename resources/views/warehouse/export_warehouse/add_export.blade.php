@extends('master_layout.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add_export.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .batch-row:hover {
            background: green !important;
        }

        .expired>td {
            color: red;
        }
    </style>
@endsection

@section('title')
    Tạo Phiếu Xuất Kho
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8">
        {{-- Tiêu đề --}}
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Xuất Kho</span>
            </h3>

            <div class="card-toolbar">
                <a href="{{ route('warehouse.export') }}" class="btn btn-sm btn-dark rounded-pill" style="font-size: 10px;">
                    <i class="fa fa-arrow-left me-1" style="font-size: 10px;"></i>Trở Lại
                </a>
            </div>
        </div>

        <!-- Form thêm vật tư -->
        <form action="{{ route('warehouse.store_export') }}" id="warehouse-export-form" method="POST">
            @csrf
            <div class="container mt-4">
                <div class="row">
                    <div class="col-8">
                        <div class="mt-3">
                            <div class="row mb-3">
                                <div class="col-12 mb-2">
                                    <label for="material_code" class="required form-label mb-2">Tên vật tư</label>
                                    <select class="form-select setupSelect2 bg-white form-select-sm rounded-pill"
                                        id="material_code" name="equipment_code" style="width: 100%;">
                                        <option value="" selected>Chọn vật tư</option>
                                        @foreach ($equipments as $equipment)
                                            <option value="{{ $equipment['code'] }}">{{ $equipment['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <h6 class="mb-3">Danh sách lô:</h6>
                                    <div id="batch_info" class="list-group">
                                        <div class="alert alert-danger" role="alert">
                                            Bạn chưa chọn vật tư.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle text-center" id="material-list">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="">Tên vật tư</th>
                                        <th class="">Số lô</th>
                                        <th class="">Số lượng</th>
                                        <th class="">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="material-list-body">
                                    <tr id="no-material-alert">
                                        <td colspan="4" class="text-center pe-0 px-0"
                                            style="box-shadow: none !important;">
                                            <div class="alert alert-warning mb-0" role="alert">
                                                Chưa có vật tư nào được thêm vào danh sách.
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>

                    <div class="col-4">
                        <div class="card border-0 shadow-sm p-4 mb-4 bg-white rounded-4 mt-3">
                            <h6 class="mb-4 fw-bold text-primary text-uppercase">Thông tin phiếu xuất</h6>
                            <div class="mb-4">
                                <label for="department_code" class="form-label fw-semibold text-muted">Mã phòng ban</label>
                                <select name="department_code"
                                    class="form-select form-select-sm rounded-pill setupSelect2 py-2 px-3"
                                    id="department_code" required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department['code'] }}">{{ $department['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="created_by" class="form-label fw-semibold text-muted">Người tạo</label>
                                <input type="text" name="created_by" value="{{ $users->first_name }}"
                                    class="form-control form-control-sm bg-white rounded-pill py-2 px-3" id="export_at"
                                    disabled>
                            </div>

                            <div class="mb-4">
                                <label for="export_at" class="form-label fw-semibold text-muted">Ngày xuất</label>
                                <input type="date" name="export_at"
                                    class="form-control form-control-sm rounded-pill py-2 px-3" id="export_at"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required readonly>
                            </div>


                            <div class="mb-4">
                                <label for="note" class="form-label fw-semibold text-muted">Ghi chú</label>
                                <textarea name="note" class="form-control form-control-sm rounded-3 py-2 px-3" id="note" rows="3"
                                    placeholder="Nhập ghi chú..."></textarea>
                            </div>

                            <hr class="my-4">

                            <input type="hidden" name="material_list" id="material_list_input">

                            <button type="submit" name="status" value="0"
                                class="btn btn-sm btn-warning w-100 mb-2 d-flex align-items-center justify-content-center rounded-pill">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Lưu phiếu tạm
                            </button>
                            <button type="submit" name="status" value="1"
                                class="btn btn-success btn-sm rounded-pill w-100">
                                <i class="fas fa-file-export me-1"></i>Duyệt phiếu xuất
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    @include('warehouse.export_warehouse.modal')
@endsection

@section('scripts')
    <script>
        const postExportUrl = '{{ route('warehouse.post_export') }}';
        const csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('js/warehouse/export_store.js') }}"></script>
@endsection

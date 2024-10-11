<div class="card-header border-0 pt-5">
    <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Nhập Kho</span>
    </h3>
    <div class="card-toolbar">
        <a href="{{ route('warehouse.trash') }}" class="btn btn-sm btn-danger rounded-pill me-2">
            <i class="fas fa-trash" style="margin-bottom: 2px;"></i> Thùng Rác
        </a>
        <a href="{{ route('warehouse.create_import') }}" class="btn btn-sm btn-success rounded-pill">
            <i class="fas fa-plus" style="margin-bottom: 2px;"></i> Tạo Phiếu Nhập
        </a>
    </div>
</div>
{{-- Bộ lọc của Zy --}}
<div class="card-body py-1">
    <form action="{{ route('warehouse.import') }}" class="row align-items-center" id="form-1">
        <div class="col-lg-3 col-md-4 col-sm-12">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-5 col-sm-5 pe-0">
                    <input type="date" name="start_date"
                        class="form-control form-control-sm border-success rounded-pill"
                        value="{{ request('start_date', \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d')) }}">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 text-center"><span>-</span></div>
                <div class="col-lg-5 col-md-5 col-sm-5 ps-0">
                    <input type="date" name="end_date"
                        class="form-control form-control-sm border-success rounded-pill"
                        value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12">
            <select name="spl" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                <option value="" selected>--Theo Nhà Cung Cấp--</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->code }}" {{ request()->spl == $supplier->code ? 'selected' : '' }}>
                        {{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12">
            <select name="us" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                <option value="" selected>--Theo Người Tạo--</option>
                @foreach ($users as $user)
                    <option value="{{ $user->code }}" {{ request()->us == $user->code ? 'selected' : '' }}>
                        {{ $user->last_name . ' ' . $user->first_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-12">
            <select name="stt" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2 w-100">
                <option value="" {{ request()->stt == '' ? 'selected' : '' }}>--Theo Trạng Thái--</option>
                <option value="0" {{ request()->stt == '0' ? 'selected' : '' }}>Chờ Duyệt</option>
                <option value="1" {{ request()->stt == '1' ? 'selected' : '' }}>Đã Duyệt</option>
                <option value="3" {{ request()->stt == '3' ? 'selected' : '' }}>Lưu Tạm</option>
            </select>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <input type="search" name="kw" placeholder="Tìm kiếm mã phiếu nhập.."
                        class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success w-100"
                        value="{{ request()->kw }}">
                </div>
                <div class="col-md-3 d-flex">
                    <a class="btn rounded-pill btn-info btn-sm mt-2 mb-2 w-100 me-2"
                        href="{{ route('warehouse.import') }}"><i class="fas fa-times-circle"
                            style="margin-bottom: 2px;"></i> Bỏ
                        Lọc</a>
                    <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 w-100 load_animation" type="submit"><i
                            class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm</button>
                </div>
            </div>
        </div>
    </form>
</div>

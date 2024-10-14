<div class="card-body py-1">
    <form action="" class="row align-items-center">
        <div class="col-3">
            <div class="row align-items-center">
                <div class="col-5 pe-1">
                    <input type="date"
                        class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success rounded-pill"
                        value="" name="start_date">
                </div>
                <div class="col-2 text-center">
                    Đến
                </div>
                <div class="col-5 ps-1">
                    <input type="date"
                        class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success rounded-pill"
                        value="" name="end_date">
                </div>
            </div>
        </div>

        <div class="col-2">
            <select name="category" id="category"
                class="mt-2 mb-2 form-select form-select-sm border border-success setupSelect2 rounded-pill">
                <option value="" selected>--Theo Nhóm--</option>
                @forelse ($equipmentType as $equipment)
                    <option value="{{ $equipment->code }}">
                        {{ $equipment->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-2">
            <select class="mt-2 mb-2 form-select form-select-sm border border-success setupSelect2 rounded-pill"
                name="expiry_date">
                <option value="" selected>--Theo HSD--</option>
                <option value="valid">Còn hạn</option>
                <option value="expiring_soon">Cảnh báo</option>
                <option value="expired">Hết hạn</option>
            </select>
        </div>
        <div class="col-2">
            <select class="mt-2 mb-2 form-select form-select-sm border border-success setupSelect2 rounded-pill"
                name="quantity">
                <option value="" selected>--Theo SL--</option>
                <option value="enough">Còn hàng</option>
                <option value="low">Sắp hết</option>
                <option value="out_stock">Hết hàng</option>
            </select>
        </div>
        <div class="col-3">
            <input type="text" id="search" name="search" autocapitalize="off" style="text-transform: none;"
                placeholder="Tìm kiếm.."
                class="mt-2 mb-2 form-control bg-white form-control-sm form-control-solid border border-success rounded-pill">
        </div>
    </form>
</div>

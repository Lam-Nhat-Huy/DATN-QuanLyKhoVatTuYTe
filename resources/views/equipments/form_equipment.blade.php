@extends('master_layout.layout')

@section('styles')
    <style>
        /* Ẩn nhãn khi có hình ảnh */
        .image-preview-wrapper img[style*="display: block;"]+.custom-file-input-label {
            display: none;
        }

        .image-preview-wrapper {
            display: inline-block;
        }

        .custom-file-input {
            opacity: 0;
            z-index: 2;
            cursor: pointer;
        }

        .custom-file-input-label {
            border-radius: 10px;
            display: flex;
            border: 2px dashed #28a745;
            padding: 5px;
            width: 250px;
            text-align: center;
            height: 250px;
            cursor: pointer;
            z-index: 1;
            justify-content: center;
            align-items: center;
        }

        .custom-file-input-label:hover {
            background-color: #e9ecef;
            border-color: #28a745;
        }

        .custom-file-input-text {
            color: #28a745;
            font-weight: bold;
        }

        .image-preview {
            width: 250px;
            height: 250px;
            display: block;
            border-radius: 10px;
            border: 1px solid #28a745;
        }

        /* Ẩn nhãn khi có hình ảnh */
        .image-preview-wrapper img[style*="display: block;"]+.custom-file-input-label {
            display: none;
        }
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@php
    if ($action == 'edit') {
        $form_action = route('equipments.edit_equipments', $currentEquipment->code);
        $button_text = 'Cập Nhật';
    } else {
        $form_action = route('equipments.create_equipments');
        $button_text = 'Thêm';
    }
@endphp

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">{{ $title_form }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipments.index') }}" class="btn btn-sm btn-dark rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form class="form" action="{{ $form_action }}" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="py-5 px-lg-9">
                    <div class="me-n7 pe-7">
                        <div class="row mb-5">
                            <!-- Ảnh thiết bị -->
                            <div class="col-3 mb-5">
                                <div class="required">Ảnh Thiết Bị</div>
                                <div class="image-preview-wrapper mt-3">
                                    <img id="preview-image"
                                        src="{{ old('current_image') ? asset('storage/' . old('current_image')) : (!empty($equipment) && $equipment->image ? asset('storage/' . $equipment->image) : '') }}"
                                        alt="Hình ảnh thiết bị" class="image-preview"
                                        style="display: {{ !empty($equipment->image) || old('current_image') ? 'block' : 'none' }}; cursor: pointer;" />
                                    <!-- Thêm cursor: pointer để hiển thị con trỏ khi hover -->

                                    <label for="equipment_image" class="custom-file-input-label">
                                        <span class="custom-file-input-text"><i class="fa fa-upload mb-1 me-2"
                                                style="color: #28a745;"></i>Tải ảnh lên</span>
                                    </label>

                                    <input type="file" id="equipment_image" name="equipment_image"
                                        onchange="changeImage()" class="custom-file-input"
                                        value="{{ old('current_image') }}" accept="image/*" style="display: none;">
                                </div>
                                @error('equipment_image')
                                    <div class="message_error" id="equipment_image_error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-9">
                                <div class="row">
                                    <!-- Tên thiết bị -->
                                    <div class="col-12 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Tên thiết bị</label>
                                        <input type="text" onchange="changeName()"
                                            class="form-control form-control-sm rounded-pill border border-success"
                                            name="name" id="name" placeholder="Tên thiết bị.."
                                            value="{{ !empty($currentEquipment) ? $currentEquipment->name : old('name') }}">
                                        @error('name')
                                            <div class="message_error" id="name_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nhóm thiết bị -->
                                    <div class="col-6 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Nhóm thiết bị</label>
                                        <div class="d-flex align-items-center">
                                            <select name="equipment_type_code" id="equipment_type_code"
                                                onchange="changeEquipmentType()"
                                                class="form-select form-select-sm rounded-pill border-success">
                                                <option value="0">--Chọn Nhóm thiết bị--</option>
                                                @foreach ($equipmentTypes as $type)
                                                    <option value="{{ $type->code }}"
                                                        id="option_equipment_type_{{ $type->code }}"
                                                        {{ !empty($currentEquipment) && $currentEquipment->equipment_type_code == $type->code ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="ms-4 pointer" data-bs-toggle="modal"
                                                data-bs-target="#add_modal_equip_group" title="Thêm Nhóm Thiết Bị">
                                                <i class="fa fa-plus bg-primary rounded-circle p-2 text-white"
                                                    style="width: 25px; height: 25px;"></i>
                                            </span>
                                        </div>
                                        @error('equipment_type_code')
                                            <div class="message_error" id="equipment_type_code_eror">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Đơn Vị Tính -->
                                    <div class="col-6 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Đơn Vị Tính</label>
                                        <div class="d-flex align-items-center">
                                            <select name="unit_code" id="unit_code" onchange="changeUnit()"
                                                class="form-select form-select-sm rounded-pill border-success">
                                                <option value="0">--Chọn Đơn Vị Tính--</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->code }}"
                                                        id="option_unit_{{ $unit->code }}"
                                                        {{ !empty($currentEquipment) && $currentEquipment->unit_code == $unit->code ? 'selected' : '' }}>
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="ms-4 pointer" data-bs-toggle="modal"
                                                data-bs-target="#add_modal_unit_conversion" title="Thêm Đơn Vị Tính">
                                                <i class="fa fa-plus bg-primary rounded-circle p-2 text-white"
                                                    style="width: 25px; height: 25px;"></i>
                                            </span>

                                        </div>
                                        @error('unit_code')
                                            <div class="message_error" id="unit_code_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Giá -->
                                    <div class="col-6 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Giá</label>
                                        <input type="number" onchange="changePrice()"
                                            class="form-control form-control-sm rounded-pill border border-success"
                                            name="price" id="price"
                                            value="{{ old('price', !empty($currentEquipment) ? $currentEquipment->price : '') }}"
                                            placeholder="Vui lòng nhập giá">
                                        @error('price')
                                            <div class="message_error" id="price_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Ngày Hết Hạn -->
                                    <div class="col-6 fv-row mb-5">
                                        <label class="fw-bold mb-3">Ngày Hết Hạn (Nếu Có)</label>
                                        <input type="date"
                                            class="form-control form-control-sm rounded-pill border border-success"
                                            name="expiry_date"
                                            value="{{ old('expiry_date', !empty($currentEquipment) ? $currentEquipment->expiry_date : '') }}">
                                        @error('expiry_date')
                                            <div class="message_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nhà Cung Cấp -->
                                    <div class="col-6 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Nhà Cung Cấp</label>
                                        <div class="d-flex align-items-center">
                                            <select name="supplier_code" id="supplier_code" onchange="changeSupplier()"
                                                class="form-select form-select-sm rounded-pill border-success">
                                                <option value="0">--Chọn Nhà Cung Cấp--</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->code }}"
                                                        id="option_supplier_{{ $supplier->code }}"
                                                        {{ old('supplier_code', !empty($currentEquipment) ? $currentEquipment->supplier_code : '') == $supplier->code ? 'selected' : '' }}>
                                                        {{ $supplier->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="ms-4 pointer" data-bs-toggle="modal"
                                                data-bs-target="#add_modal_ncc" title="Thêm Nhà Cung Cấp">
                                                <i class="fa fa-plus bg-primary rounded-circle p-2 text-white"
                                                    style="width: 25px; height: 25px;"></i>
                                            </span>
                                        </div>
                                        @error('supplier_code')
                                            <div class="message_error" id="supplier_code_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nước Sản Xuất -->
                                    <div class="col-6 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Nước Sản Xuất</label>
                                        <select name="country" id="country" onchange="changeCountry()"
                                            class="form-select form-select-sm rounded-pill border-success">4
                                            <option value="0">--Chọn Nước Sản Xuất--</option>
                                            @foreach (config('apps.country') as $value)
                                                <option value="{{ $value }}"
                                                    {{ old('country', !empty($currentEquipment) ? $currentEquipment->country : '') == $value ? 'selected' : '' }}>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('country')
                                            <div class="message_error" id="country_error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Mô Tả -->
                                    <div class="col-12 fv-row mb-5">
                                        <label class="required fw-bold mb-3">Mô Tả</label>
                                        <textarea name="description" id="description" cols="30" rows="10" onchange="changeDescription()"
                                            class="form-control form-control-sm rounded-3 border border-success" placeholder="Thêm Mô Tả Cho thiết bị..">{{ old('description', !empty($currentEquipment) ? $currentEquipment->description : '') }}</textarea>
                                        @error('description')
                                            <div class="message_error" id="description_error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 justify-content-end">
                        <button type="submit" class="btn rounded-pill btn-sm btn-twitter load_animation">
                            <!-- Thay đổi màu và độ trong suốt tại đây -->
                            {{ $button_text }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Thêm Nhóm Thiết Bị -->
    <div class="modal fade" id="add_modal_equip_group" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="add_modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="add_modalLabel">Thêm Nhóm Thiết Bị</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="required er mb-2">Tên Nhóm Thiết Bị</label>
                        <input type="text" class="form-control form-control-sm border border-success rounded-pill"
                            placeholder="Tên nhóm thiết bị.." name="name" id="equipment_group_name" />
                        <div class="message_error" id="show-err-equipment-group"></div>
                    </div>
                </div>
                <div class="modal-body pt-0">
                    <div class="overflow-auto" style="max-height: 300px;">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="erer bg-success">
                                    <th class="ps-3" style="width: 70%;">Tên Nhóm Thiết Bị</th>
                                    <th class="pe-3 text-center" style="width: 30%;">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="equipment-group-list">
                                @foreach ($equipmentTypes as $item)
                                    <tr class="hover-table pointer" id="equipment-group-{{ $item->code }}">
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#delete_modal_equipment_group"
                                                onclick="setDeleteEquipmentGroupForm('{{ route('equipments.delete_equipment_group', $item->code) }}', '{{ $item->name }}')">
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
                        id="submit_equipment_group">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form xóa nhóm thiết bị -->
    <div class="modal fade" id="delete_modal_equipment_group" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Nhóm Thiết Bị</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <h6 class="text-danger mb-4 text-center" id="delete-equipment-group-message"></h6>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#add_modal_equip_group">Trở Lại</button>
                    <button type="button" class="btn btn-sm btn-danger px-4 rounded-pill"
                        id="confirm-delete-equipment-group">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Đơn Vị Tính -->
    <div class="modal fade" id="add_modal_unit_conversion" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="add_modalLabel_conversion" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="add_modalLabel_conversion">Thêm Đơn Vị Tính</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="required er mb-2">Tên Đơn Vị Tính</label>
                        <input type="text" class="form-control form-control-sm border border-success rounded-pill"
                            placeholder="Tên đơn vị tính.." name="unit_name_conversion" id="unit_name_conversion" />
                        <div class="message_error" id="show-err-unit-conversion"></div>
                    </div>
                </div>
                <div class="modal-body pt-0">
                    <div class="overflow-auto" style="max-height: 300px;">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="erer bg-success">
                                    <th class="ps-3" style="width: 70%;">Tên Đơn Vị Tính</th>
                                    <th class="pe-3 text-center" style="width: 30%;">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="unit-list-conversion">
                                @foreach ($units as $unit)
                                    <tr class="hover-table pointer" id="unit-conversion-{{ $unit->code }}">
                                        <td>{{ $unit->name }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill"
                                                data-bs-toggle="modal" data-bs-target="#delete_modal_unit_conversion"
                                                onclick="setDeleteUnitFormConversion('{{ route('equipments.delete_unit', $unit->code) }}', '{{ $unit->name }}')">
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
                        id="submit_unit_conversion">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form xóa đơn vị tính -->
    <div class="modal fade" id="delete_modal_unit_conversion" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="deleteModalLabel_conversion" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteModalLabel_conversion">Xóa Đơn Vị Tính</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <h6 class="text-danger mb-4 text-center" id="delete-unit-message-conversion"></h6>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#add_modal_unit_conversion">Trở Lại</button>
                    <button type="button" class="btn btn-sm btn-danger px-4 rounded-pill"
                        id="confirm-delete-unit-conversion">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Nhà Cung Cấp -->
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
                        <label class="required er mb-2">Tên Nhà Cung Cấp</label>
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
                                @foreach ($AllSuppiler as $item)
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
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Nhà Cung Cấp</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="padding-bottom: 0px;">
                    <h6 class="text-danger mb-4 text-center" id="delete-supplier-message"></h6>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill" data-bs-toggle="modal"
                        data-bs-target="#add_modal_ncc">Trở Lại</button>
                    <button type="button" class="btn btn-sm btn-danger px-4 rounded-pill"
                        id="confirm-delete-supplier">Xóa</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputFile = document.getElementById('equipment_image');
            const previewImage = document.getElementById('preview-image');

            // Khi click vào ảnh, kích hoạt input file
            previewImage.addEventListener('click', function() {
                inputFile.click(); // Mở cửa sổ chọn file
            });

            // Xử lý sự kiện thay đổi file
            inputFile.addEventListener('change', function(event) {
                const file = event.target.files[0]; // Lấy file đã chọn

                if (file) {
                    const reader = new FileReader(); // Tạo FileReader để đọc file
                    reader.onload = function(e) {
                        previewImage.src = e.target.result; // Đặt src cho thẻ img
                        previewImage.style.display = 'block'; // Hiển thị thẻ img
                    }
                    reader.readAsDataURL(file); // Đọc file dưới dạng URL
                }
            });
        });

        // Thêm nhóm thiết bị
        document.getElementById('submit_equipment_group').addEventListener('click', function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                let equipmentGroupName = document.getElementById('equipment_group_name').value.trim();
                let equipmentGroupError = document.getElementById('show-err-equipment-group');
                let existingEquipmentGroups = Array.from(document.querySelectorAll(
                    '#equipment-group-list tr td:first-child')).map(td => td.textContent.trim());

                if (equipmentGroupName === '') {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    equipmentGroupError.innerText = 'Vui lòng nhập tên nhóm thiết bị';
                    document.getElementById('equipment_group_name').focus();
                    return;
                }

                if (existingEquipmentGroups.includes(equipmentGroupName)) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    equipmentGroupError.innerText = 'Tên nhóm thiết bị đã tồn tại';
                    document.getElementById('equipment_group_name').focus();
                    return;
                }

                equipmentGroupError.innerText = '';

                let formDataEquipmentGroup = new FormData();
                formDataEquipmentGroup.append('name', equipmentGroupName);

                fetch('{{ route('equipments.create_equipment_group') }}', {
                        method: 'POST',
                        body: formDataEquipmentGroup,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let tableBodyEquipmentGroup = document.getElementById(
                                'equipment-group-list');
                            let newRowEquipmentGroup = document.createElement('tr');
                            newRowEquipmentGroup.id = `equipment-group-${data.code}`;
                            newRowEquipmentGroup.className = 'pointer';

                            newRowEquipmentGroup.innerHTML =
                                `
                                <td>${data.name}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#delete_modal_equipment_group"
                                        onclick="setDeleteEquipmentGroupForm('{{ route('equipments.delete_equipment_group', '') }}/` +
                                data.code + `', '` +
                                data.name + `')">
                                        <i class="fa fa-trash p-0"></i>
                                    </button>
                                </td>
                                `;

                            tableBodyEquipmentGroup.prepend(newRowEquipmentGroup);

                            let selectOptionEquipmentGroup = document.getElementById(
                                'equipment_type_code');
                            let newOptionEquipmentGroup = document.createElement('option');
                            newOptionEquipmentGroup.value = data.code;
                            newOptionEquipmentGroup.textContent = data.name;
                            newOptionEquipmentGroup.id = `option_equipment_type_${data.code}`;

                            let defaultOptionEquipmentGroup = selectOptionEquipmentGroup.querySelector(
                                'option[value="0"]');
                            selectOptionEquipmentGroup.insertBefore(newOptionEquipmentGroup,
                                defaultOptionEquipmentGroup.nextSibling);

                            toastr.success("Đã thêm nhóm thiết bị");
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('equipment_group_name').value = "";
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });
            }, 500);
        });

        // Xóa nhóm thiết bị
        let deleteEquipmentGroupUrl = '';

        function setDeleteEquipmentGroupForm(actionUrlEquipmentType, equipmentGroupName) {
            deleteEquipmentGroupUrl = actionUrlEquipmentType;
            document.getElementById('delete-equipment-group-message').innerText =
                `Bạn có chắc chắn muốn xóa nhóm thiết bị "${equipmentGroupName}" này?`;
        }

        // Xác nhận xóa nhóm thiết bị
        document.getElementById('confirm-delete-equipment-group').addEventListener('click', function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                fetch(deleteEquipmentGroupUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.messages);
                            document.getElementById(`equipment-group-${data.equipment_group.code}`)
                                .remove();
                            document.getElementById(
                                    `option_equipment_type_${data.equipment_group.code}`).classList
                                .add('d-none');
                            $('#delete_modal_equipment_group').modal('hide');
                            $('#add_modal_equip_group').modal('show');
                        } else {
                            toastr.error(data.messages);
                            $('#delete_modal_equipment_group').modal('hide');
                            $('#add_modal_equip_group').modal('show');
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

        //------------------------------------------------------------------------------------//

        // Thêm đơn vị tính
        document.getElementById('submit_unit_conversion').addEventListener('click', function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                let unitNameConversion = document.getElementById('unit_name_conversion').value.trim();
                let unitErrorConversion = document.getElementById('show-err-unit-conversion');
                let existingUnitsConversion = Array.from(document.querySelectorAll(
                    '#unit-list-conversion tr td:first-child')).map(td => td.textContent.trim());

                if (unitNameConversion === '') {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    unitErrorConversion.innerText = 'Vui lòng nhập tên đơn vị tính';
                    document.getElementById('unit_name_conversion').focus();
                    return;
                }

                if (existingUnitsConversion.includes(unitNameConversion)) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    unitErrorConversion.innerText = 'Tên đơn vị tính đã tồn tại';
                    document.getElementById('unit_name_conversion').focus();
                    return;
                }

                unitErrorConversion.innerText = '';

                let formDataUnitConversion = new FormData();

                formDataUnitConversion.append('unit_name_conversion', unitNameConversion);

                fetch('{{ route('equipments.create_unit') }}', {
                        method: 'POST',
                        body: formDataUnitConversion,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let tableBodyUnitConversion = document.getElementById(
                                'unit-list-conversion');
                            let newRowUnitConversion = document.createElement('tr');
                            newRowUnitConversion.id = `unit-conversion-${data.code}`;
                            newRowUnitConversion.className = 'pointer';

                            newRowUnitConversion.innerHTML =
                                `
                                <td>${data.name}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm rounded-pill" data-bs-toggle="modal"
                                        data-bs-target="#delete_modal_unit_conversion"
                                        onclick="setDeleteUnitFormConversion('{{ route('equipments.delete_unit', '') }}/` +
                                data.code + `', '` + data.name + `')">
                                        <i class="fa fa-trash p-0"></i>
                                    </button>
                                </td>
                                `;

                            tableBodyUnitConversion.prepend(newRowUnitConversion);

                            let selectOptionUnitConversion = document.getElementById(
                                'unit_code');

                            let newOptionUnitConversion = document.createElement('option');
                            newOptionUnitConversion.value = data.code;
                            newOptionUnitConversion.textContent = data.name;
                            newOptionUnitConversion.id = `option_unit_${data.code}`;

                            let defaultOptionUnitConversion = selectOptionUnitConversion.querySelector(
                                'option[value="0"]');
                            selectOptionUnitConversion.insertBefore(newOptionUnitConversion,
                                defaultOptionUnitConversion.nextSibling);

                            toastr.success("Đã thêm đơn vị tính");
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('unit_name_conversion').value = "";
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });
            }, 500);
        });

        // Xóa đơn vị tính
        let deleteUnitUrlConversion = '';

        function setDeleteUnitFormConversion(actionUrlUnitConversion, unitNameConversion) {
            deleteUnitUrlConversion = actionUrlUnitConversion;
            document.getElementById('delete-unit-message-conversion').innerText =
                `Bạn có chắc chắn muốn xóa đơn vị tính "${unitNameConversion}" này?`;
        }

        // Xác nhận xóa đơn vị tính
        document.getElementById('confirm-delete-unit-conversion').addEventListener('click', function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                fetch(deleteUnitUrlConversion, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.messages);
                            document.getElementById(`supplier-${data.supplier.code}`).remove();
                            $('#delete_modal_unit_conversion').modal('hide');
                            $('#add_modal_unit_conversion').modal('show');
                            document.getElementById(`option_supplier_${data.supplier.code}`).classList
                                .add('d-none');
                        } else {
                            toastr.error(data.messages);
                            $('#delete_modal_unit_conversion').modal('hide');
                            $('#add_modal_unit_conversion').modal('show');
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

        //------------------------------------------------------------------------------------//

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
                    document.getElementById('supplier_type_name').focus();
                    return; // Kết thúc xử lý khi có lỗi
                }

                if (existingSuppliers.includes(supplierTypeName)) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    equipment_error.innerText = 'Tên nhà cung cấp đã tồn tại';
                    document.getElementById('supplier_type_name').focus();
                    return; // Kết thúc xử lý khi có lỗi
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
                            // Thêm nhà cung cấp vào danh sách trong bảng mà không cần tải lại trang
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
                            selectOptionSupplier.insertBefore(newOption, defaultOption.nextSibling);

                            toastr.success("Đã thêm nhà cung cấp");
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        document.getElementById('supplier_type_name').value = "";
                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('loading-overlay').style.display = 'none';
                        this.disabled = false;
                    });
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
        document.getElementById('confirm-delete-supplier').addEventListener('click', function(event) {
            event.preventDefault();

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
                            toastr.success(data.messages);
                            document.getElementById(`supplier-${data.supplier.code}`).remove();
                            $('#delete_modal_supplier_type').modal('hide');
                            $('#add_modal_ncc').modal('show');
                            document.getElementById(`option_supplier_${data.supplier.code}`).classList
                                .add('d-none');
                        } else {
                            toastr.error(data.messages);
                            $('#delete_modal_supplier_type').modal('hide');
                            $('#add_modal_ncc').modal('show');
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

        function changeEquipmentType() {
            const equipment_type_code_change = document.getElementById('equipment_type_code').value;
            const equipment_type_code_eror_change = document.getElementById('equipment_type_code_eror');

            if (equipment_type_code_change !== "") {
                equipment_type_code_eror_change.innerText = '';
            }
        }

        function changeUnit() {
            const unit_code_change = document.getElementById('unit_code').value;
            const unit_code_error_change = document.getElementById('unit_code_error');

            if (unit_code_change !== "") {
                unit_code_error_change.innerText = '';
            }
        }

        function changeSupplier() {
            const selectedValue2 = document.getElementById('supplier_code').value;
            const errorMessage2 = document.getElementById('supplier_code_error');

            if (selectedValue2 !== "") {
                errorMessage2.innerText = '';
            }
        }

        function changeImage() {
            const img = document.getElementById('equipment_image').value;
            const imgerr = document.getElementById('equipment_image_error');

            if (img !== "") {
                imgerr.innerText = '';
            }
        }

        function changeName() {
            const name = document.getElementById('name').value;
            const nameerr = document.getElementById('name_error');

            if (name !== "") {
                nameerr.innerText = '';
            }
        }

        function changePrice() {
            const price = document.getElementById('price').value;
            const priceerr = document.getElementById('price_error');

            if (price !== "") {
                priceerr.innerText = '';
            }
        }

        function changeCountry() {
            const country = document.getElementById('country').value;
            const countryerr = document.getElementById('country_error');

            if (country !== "") {
                countryerr.innerText = '';
            }
        }

        function changeDescription() {
            const description = document.getElementById('description').value;
            const descriptionerr = document.getElementById('description_error');

            if (description !== "") {
                descriptionerr.innerText = '';
            }
        }
    </script>
@endsection

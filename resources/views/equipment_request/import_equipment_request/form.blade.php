@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@php
    if ($action == 'create') {
        $action = route('equipment_request.store_import');

        $button_text = 'Tạo Phiếu';

        $required = 'required';

        $d_none_save = '';

        $d_none_update = 'd-none';

        $d_none_temp = '';
    } else {
        $action = route('equipment_request.edit_import', request('code'));

        $button_text = 'Cập Nhật';

        $required = '';

        $d_none_save = 'd-none';

        $d_none_update = '';

        $d_none_temp = 'd-none';
    }
@endphp

@section('content')
    <div class="card mb-5 mb-xl-8 pb-5 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thông Tin Phiếu Yêu Cầu Mua Hàng</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipment_request.import') }}" class="btn btn-sm btn-dark rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <div class="py-3 px-lg-17">
            <div class="me-n7 pe-7">
                <div class="row align-items-center">
                    <div class="col-md-6 fv-row">
                        <label class="{{ $required }} fs-5 fw-bold mb-3">Nhà Cung Cấp</label>
                        <div class="d-flex align-items-center">
                            <select name="supplier_code" id="supplier_code" onchange="changeSupplier()"
                                class="form-select form-select-sm border border-success rounded-pill ps-5">
                                <option value="0">Chọn Nhà Cung Cấp...</option>
                                @foreach ($AllSuppiler as $item)
                                    <option value="{{ $item->code }}" id="option_supplier_{{ $item->code }}"
                                        {{ old('supplier_code', $editForm->supplier_code ?? '') == $item->code ? 'selected' : '' }}>
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

                    <div class="col-md-6 fv-row">
                        <label class="fs-5 fw-bold mb-3">Ghi Chú</label>
                        <input type="text" class="form-control form-control-sm border border-success rounded-pill"
                            placeholder="Nhập ghi chú cho phiếu yêu cầu nhập.." name="note" id="note"
                            value="{{ old('note', $editForm->note ?? '') }}" />
                        <div class="message_error" id="supplier_code_error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-xl-8 pt-5 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thêm Thiết Bị Yêu Cầu</span>
            </h3>
        </div>
        <div class="py-3 px-lg-17">
            <div class="me-n7 pe-7">
                <div class="row align-items-center">
                    <div class="col-md-6 fv-row">
                        <label class="{{ $required }} fs-5 fw-bold mb-3">Thiết Bị</label>
                        <select name="equipment" id="equipment" onchange="changeEquipment()"
                            class="form-select form-select-sm border border-success rounded-pill ps-5">
                            <option value="" selected>Chọn Thiết Bị...</option>
                            @foreach ($AllEquipment as $item)
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

                    <div class="col-md-6 fv-row">
                        <label class="{{ $required }} fs-5 fw-bold mb-3">Số Lượng</label>
                        <input type="number" id="quantity" onchange="changeEquipmentQuantity()"
                            class="form-control form-control-sm border border-success rounded-pill" value="0"
                            name="quantity" min="0" />
                        <div class="message_error" id="quantity_error"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer flex-right pe-0 py-5">
                <button type="butotn" class="btn btn-danger btn-sm rounded-pill" id="btn_add_equipment">
                    <i class="fa fa-plus" style="margin-bottom: 2px;"></i>Thêm Vào Danh Sách
                </button>
            </div>

            <div class="table-responsive rounded">
                <table class="table table-striped align-middle gs-0 gy-4" id="table_list_equipment">
                    <thead class="table-dark">
                        <tr class="fw-bolder bg-success">
                            <th class="ps-10" style="width: 35%;">Thiết Bị</th>
                            <th class="" style="width: 20%;">Đơn Vị</th>
                            <th class="" style="width: 25%;">Số Lượng</th>
                            <th class="pe-3 text-center" style="width: 20%;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($getList))
                            @foreach ($getList as $item)
                                <tr id="equipment-row-{{ $item->equipment_code }}">
                                    <td>{{ $item->equipments->name }}</td>
                                    <td>{{ $item->equipments->units->name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input type="number" id="quantity_change_{{ $item->equipment_code }}"
                                                value="{{ $item->quantity }}"
                                                class="form-control form-control-sm border border-success rounded-pill"
                                                style="width: 30%;">
                                            <div class="message_error d-none ms-2 m-0 p-0"
                                                id="quantity_error_{{ $item->equipment_code }}">
                                                (Số lượng
                                                phải lớn hơn 0)
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="btn btn-sm btn-dark pointer rounded-pill"
                                            onclick="removeEquipment('{{ $item->equipment_code }}')">
                                            <i class="fa fa-trash p-0"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr id="noDataAlert" class="{{ !empty($getList) ? 'd-none' : '' }} ">
                            <td colspan="12" class="text-center">
                                <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                    role="alert"
                                    style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                    <div class="mb-3">
                                        <i class="fas fa-box-open" style="font-size: 36px; color: #6c757d;"></i>
                                    </div>
                                    <div class="text-center">
                                        <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Danh sách thiết
                                            bị
                                            trống</h5>
                                        <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                            Hiện chưa có thiết bị nào được thêm vào danh sách yêu cầu.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="d-none mb-3" id="important_error"><strong class="text-danger">Lưu ý: </strong><span
                    class="ms-1 fw-bolder">Các thiết bị được đánh dấu <span class="text-warning bg-dark">màu
                        vàng</span>
                    đã tồn tại trong lịch sử yêu cầu hoặc phiếu tạm của người khác, hoặc đã bị đưa vào thùng rác (trong
                    3
                    ngày gần đây). Vui lòng kiểm tra và thử lại.. (<span id="countdown">20</span>)</div>

            <div class="modal-footer flex-right pe-0 py-5">
                <button type="button" class="btn btn-info btn-sm {{ $d_none_temp }} rounded-pill"
                    id="import_equipment_request_temp">
                    <i class="fa fa-cloud-arrow-down me-1" style="margin-bottom: 2px;"></i>Lưu Tạm
                </button>
                <button type="button" class="btn btn-twitter btn-sm {{ $d_none_save }} rounded-pill"
                    id="import_equipment_request_save">
                    <i class="fa fa-save me-1" style="margin-bottom: 2px;"></i>{{ $button_text }}
                </button>
                <button type="button" class="btn btn-twitter btn-sm {{ $d_none_update }} rounded-pill"
                    id="import_equipment_request_update">
                    <i class="fa fa-save me-1" style="margin-bottom: 2px;"></i>{{ $button_text }}
                </button>
            </div>
        </div>
    </div>

    <!-- Form thêm loại báo cáo -->
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
@endsection

@section('scripts')
    <script>
        function countDown() {
            let timeLeft = 20;
            const countdownElement = document.getElementById('countdown');

            const countdownTimer = setInterval(() => {
                timeLeft--;

                countdownElement.textContent = timeLeft;
            }, 1000);
        }

        // Duyệt danh sách bị trùng
        function highlightDuplicatedEquipment(list_duplicated) {
            list_duplicated.forEach(equipmentCode => {
                const row = document.getElementById(`equipment-row-${equipmentCode}`);
                if (row) {
                    row.style.backgroundColor = '#ffc700';
                    row.style.setProperty('--bs-table-accent-bg', 'none');
                }
            });
        }

        // Lấy dữ liệu từ danh sách thiết bị yêu cầu
        function getEquipmentList() {
            let equipmentList = [];
            let rows = document.querySelectorAll('#table_list_equipment tbody tr');

            rows.forEach((row, index) => {
                if (row.id === "noDataAlert") return;

                let equipmentCode = row.id.split('-')[2]; // Lấy mã thiết bị từ ID của hàng
                let unit = row.cells[1].innerText.trim();

                let quantityInput = document.getElementById(`quantity_change_${equipmentCode}`);
                let quantity = quantityInput.value.trim();

                // Đưa dữ liệu vào mảng
                equipmentList.push({
                    equipment_code: equipmentCode,
                    unit: unit,
                    quantity: quantity
                });

            });

            return equipmentList;
        }

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

        // Thêm yêu cầu mua hàng
        function handleImportEquipmentRequest(importEquipmentStatus) {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                let supplier_code = document.getElementById('supplier_code').value;
                let note = document.getElementById('note').value;
                let supplier_code_error = document.getElementById('supplier_code_error');
                let equipment_error = document.getElementById('equipment_error');
                let equipmentList = getEquipmentList();

                supplier_code_error.innerText = '';
                equipment_error.innerText = '';

                let hasError = false;

                if (supplier_code == 0) {
                    supplier_code_error.innerText = "Vui lòng chọn nhà cung cấp";
                    document.getElementById('quantity_error').innerText = '';
                    hasError = true;
                }

                if (equipmentList.length === 0) {
                    equipment_error.innerText = "Vui lòng thêm thiết bị yêu cầu";
                    hasError = true;
                }

                equipmentList.forEach((item) => {
                    if (item.quantity <= 0) {
                        document.getElementById('quantity_error').innerText = '';
                        document.getElementById(`quantity_error_${item.equipment_code}`).classList.remove(
                            'd-none');
                        hasError = true;
                    } else {
                        document.getElementById('quantity_error').innerText = '';
                        document.getElementById(`quantity_error_${item.equipment_code}`).classList.add(
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
                            window.location.href = "{{ route('equipment_request.import') }}";
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                            countDown();
                            document.getElementById('important_error').classList.remove('d-none');
                            highlightDuplicatedEquipment(data.list_duplicated);

                            setTimeout(() => {
                                document.getElementById('important_error').classList.add('d-none');
                            }, 21000);
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

        let addedEquipments = [];

        // Thêm thiết bị yêu cầu
        document.getElementById('btn_add_equipment').addEventListener('click', function(event) {
            event.preventDefault();

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                // Lấy dữ liệu từ form con
                let noDataAlert = document.getElementById('noDataAlert');
                let equipment = document.getElementById('equipment').value;
                let quantity = document.getElementById('quantity').value;
                let equipment_error = document.getElementById('equipment_error');
                let quantity_error = document.getElementById('quantity_error');

                equipment_error.innerText = '';
                quantity_error.innerText = '';

                if (!equipment) {
                    equipment_error.innerText = "Vui lòng chọn thiết bị cần mua";
                }

                if (quantity <= 0) {
                    quantity_error.innerText = "Vui lòng nhập số lượng cần mua và phải lớn hơn 0";
                }
                if (!equipment ||
                    quantity <= 0) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.disabled = false;
                    return;
                }

                let formData = new
                FormData();
                formData.append('equipment', equipment);
                formData.append('quantity', quantity); // AJAX request

                fetch('{{ route('equipment_request.create_import') }}', {
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
                            let tableBody = document.querySelector('#table_list_equipment tbody');
                            let newRow = document.createElement('tr');
                            newRow.id = `equipment-row-${data.equipment_code}`;

                            newRow.innerHTML = `
                                <td>${data.equipment_name} - (Tổng tồn: ${data.inventory})</td>
                                <td>${data.unit}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="quantity_change_${data.equipment_code}"
                                            value="${parseInt(data.quantity, 10)}"
                                            class="form-control form-control-sm border border-success rounded-pill" style="width: 30%;">
                                        <div class="message_error d-none ms-2 m-0 p-0"
                                            id="quantity_error_${data.equipment_code}">
                                            (Số lượng
                                            phải lớn hơn 0)
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="btn btn-sm btn-dark pointer rounded-pill" onclick="removeEquipment('${data.equipment_code}')">
                                        <i class="fa fa-trash p-0"></i>
                                    </span>
                                </td>
                                `;
                            tableBody.appendChild(newRow);

                            // Reset form sau khi thêm thành công
                            document.getElementById('equipment').value = "";
                            document.getElementById('quantity').value = 0;

                            // Ẩn các tùy chọn đã thêm trong danh sách thiết bị
                            let equipmentOptions = document.querySelectorAll('#equipment option');
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

        // Xóa thiết bị trong danh sách
        function removeEquipment(equipmentCode) {

            document.getElementById('loading').style.display = 'block';
            document.getElementById('loading-overlay').style.display = 'block';
            this.disabled = true;

            setTimeout(() => {
                // Tìm hàng trong bảng dựa trên mã thiết bị
                let row = document.getElementById(`equipment-row-${equipmentCode}`);
                if (row) {
                    row.remove();
                }

                // Kiểm tra xem bảng có còn hàng nào không và hiển thị thông báo "Không Có Dữ Liệu" nếu cần
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

                toastr.success("Đã xóa thiết bị khỏi danh sách");

                document.getElementById('loading').style.display = 'none';
                document.getElementById('loading-overlay').style.display = 'none';
                this.disabled = false;
            }, 500);
        }

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

        function changeEquipment() {
            const selectedValue = document.getElementById('equipment').value;
            const errorMessage = document.getElementById('equipment_error');

            if (selectedValue !== "") {
                errorMessage.innerText = '';
            }
        }

        function changeEquipmentQuantity() {
            const quantityValue = document.getElementById('quantity').value;
            const errorMessageQuantity = document.getElementById('quantity_error');

            if (quantityValue > 0) {
                errorMessageQuantity.innerText = '';
            }
        }

        function changeSupplier() {
            const selectedValue2 = document.getElementById('supplier_code').value;
            const errorMessage2 = document.getElementById('supplier_code_error');

            if (selectedValue2 !== "") {
                errorMessage2.innerText = '';
            }
        }
    </script>
@endsection

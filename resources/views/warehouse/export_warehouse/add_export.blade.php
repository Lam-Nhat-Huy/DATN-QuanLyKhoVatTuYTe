@extends('master_layout.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add_export.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@section('title')
    Tạo Phiếu Xuất Kho
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        {{-- Tiêu đề --}}
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Xuất Kho</span>
            </h3>

            <div class="card-toolbar">
                <a href="{{ route('warehouse.export') }}" class="btn btn-sm btn-dark" style="font-size: 10px;">
                    <i class="fa fa-arrow-left me-1" style="font-size: 10px;"></i>Trở Lại
                </a>
            </div>
        </div>

        <!-- Form thêm thiết bị -->
        <form action="{{ route('warehouse.store_export') }}" method="POST">
            @csrf
            <div class="container mt-4">
                <div class="row">
                    <div class="col-8">
                        <div class="mt-3">
                            <div class="row mb-3">
                                <div class="col-12 mb-2">
                                    <label for="equipment_code" class="required form-label mb-2">Tên thiết bị</label>
                                    <select class="form-select setupSelect2 form-select-sm" id="equipment_code"
                                        name="equipment_code" style="width: 100%;">
                                        <option value="" selected disabled>Chọn thiết bị</option>
                                        @foreach ($equipments as $equipment)
                                            <option value="{{ $equipment['code'] }}">{{ $equipment['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <h6 class="mb-3">Danh sách lô:</h6>
                                    <div id="batch_info" class="list-group">
                                        <div class="alert alert-danger" role="alert">
                                            Bạn chưa chọn thiết bị.
                                        </div>
                                    </div>


                                </div>

                                <div class="col-12 mt-3 mb-3 text-end">
                                    <button type="button" class="btn btn-sm"
                                        style="background-color: #FF0000; color: #fff; font-size: 10px;"
                                        id="add-to-list">Thêm</button>
                                </div>
                            </div>
                        </div>


                        <!-- Bảng danh sách thiết bị đã thêm -->
                        <div class="table-responsive rounded">
                            <table class="table table-bordered" id="equipment-list">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tên Thiết Bị</th>
                                        <th>Số Lô</th>
                                        <th>Số Lượng</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody id="equipment-list-body">
                                    <tr id="no-equipment-alert">
                                        <td colspan="4" class="text-center pe-0 px-0"
                                            style="box-shadow: none !important;">
                                            <div class="alert alert-warning" role="alert">
                                                Chưa có thiết bị nào được thêm vào danh sách.
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
                                <select name="department_code" class="form-select form-select-sm rounded-pill py-2 px-3"
                                    id="department_code" required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department['code'] }}">{{ $department['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="created_by" class="form-label fw-semibold text-muted">Người tạo</label>
                                <input type="text" name="created_by" value="{{$users->first_name}}"
                                    class="form-control form-control-sm rounded-pill py-2 px-3" id="export_at" disabled>
                            </div>

                            <div class="mb-4">
                                <label for="export_at" class="form-label fw-semibold text-muted">Ngày xuất</label>
                                <input type="date" name="export_at"
                                    class="form-control form-control-sm rounded-pill py-2 px-3" id="export_at" required>
                            </div>

                            <div class="mb-4">
                                <label for="note" class="form-label fw-semibold text-muted">Ghi chú</label>
                                <textarea name="note" class="form-control form-control-sm rounded-3 py-2 px-3" id="note" rows="3"
                                    placeholder="Nhập ghi chú..."></textarea>
                            </div>

                            <hr class="my-4">

                            <input type="hidden" name="equipment_list" id="equipment_list_input">

                            <button type="submit" class="btn btn-success btn-sm rounded-pill w-100">Xuất Kho</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Khởi tạo Select2 sau khi DOM đã tải xong
        $(document).ready(function() {
            $('#equipment_code').select2({
                placeholder: "Chọn thiết bị",
                allowClear: true
            });

            $('#department_code').select2({
                placeholder: "-- Chọn phòng ban --",
                allowClear: true
            });

        });

        // Dữ liệu từ server cho tồn kho
        const inventories = @json($inventories);

        let equipmentList = [];

        // Khi chọn thiết bị trong Select2, tải thông tin lô
        $('#equipment_code').on('change', function() {
            const selectedEquipment = $(this).val();
            const batchDetailsContainer = $('#batch_info');
            batchDetailsContainer.html('');

            const filteredInventories = inventories.filter(inv => inv.equipment_code === selectedEquipment);

            if (filteredInventories.length > 0) {
                const today = new Date();
                const alertThreshold = 30;

                filteredInventories.forEach(inventory => {
                    let inputField = '';
                    let textColor = '';
                    let alertMessage = '';
                    let icon = '';

                    const quantity = Number(inventory.current_quantity);
                    const expiryDate = new Date(inventory.expiry_date);
                    const daysToExpiry = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));

                    if (quantity === 0) {
                        textColor = 'color: #FF0000;';
                        inputField = `
                            <input type="text" class="form-control form-control-sm"
                                value="Hết Hàng" readonly style="max-width: 100px; background-color: #DD0000; color: #fff; text-align: center;">
                        `;
                    } else if (quantity < 10) {
                        textColor = 'color: #FFCC00;';
                        inputField = `
                            <input type="number" class="form-control form-control-sm"
                                name="batches[${inventory.batch_code}]"
                                id="batch_${inventory.batch_code}"
                                min="0" max="${quantity}"
                                placeholder="Số Lượng" style="max-width: 100px ; text-align: center;">
                        `;
                    } else {
                        textColor = 'color: #66FF00;';
                        inputField = `
                            <input type="number" class="form-control form-control-sm"
                                name="batches[${inventory.batch_code}]"
                                id="batch_${inventory.batch_code}"
                                min="0" max="${quantity}"
                                placeholder="Số Lượng" style="max-width: 100px; text-align: center;">
                        `;
                    }

                    if (daysToExpiry <= alertThreshold && daysToExpiry > 0) {
                        icon =
                            `<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcThr7qrIazsvZwJuw-uZCtLzIjaAyVW_ZrlEQ&s"
                            alt="AI Icon" style="width: 20px; height: 20px; display: inline-block; margin-left: 10px;"
                            title="Gợi ý: Nên xuất thiết bị này trước khi hết hạn">`;
                    } else if (daysToExpiry <= 0) {
                        icon =
                            `<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcThr7qrIazsvZwJuw-uZCtLzIjaAyVW_ZrlEQ&s"
                            alt="AI Icon" style="width: 20px; height: 20px; display: inline-block; margin-left: 10px;"
                            title="Thiết bị này đã hết hạn">`;
                    }

                    const batchElement = document.createElement('div');
                    batchElement.classList.add('list-group-item', 'd-flex', 'align-items-center',
                        'justify-content-between');
                    batchElement.innerHTML = `
                        <div style="${textColor}; display: inline-block;">
                            <strong>Số Lô: ${inventory.batch_code}</strong>
                            <span class="text-muted">(Tồn: ${quantity})</span>
                            <span class="text-muted">(HSD: ${inventory.expiry_date})</span>
                            ${icon} ${alertMessage}
                        </div>
                        <div class="ms-2" style="width: 100px;">
                            ${inputField}
                        </div>
                    `;
                    batchDetailsContainer.append(batchElement);
                });
            }
        });

        $('#add-to-list').on('click', function() {
            const selectedEquipment = $('#equipment_code').val();
            const departmentCode = $('#department_code').val();
            const createdBy = $('#created_by').val();
            const exportDate = $('#export_at').val();
            const note = $('#note').val();

            const batches = Array.from($('#batch_info input[type="number"]'))
                .filter(input => input.value > 0)
                .map(input => ({
                    batch_code: input.name.replace('batches[', '').replace(']', ''),
                    quantity: parseInt(input.value)
                }));

            if (selectedEquipment && batches.length > 0) {
                equipmentList.push({
                    equipment_code: selectedEquipment,
                    batches,
                    department_code: departmentCode,
                    created_by: createdBy,
                    export_at: exportDate,
                    note
                });

                updateEquipmentListTable();
                resetForm();
            }
        });

        function updateEquipmentListTable() {
            const tbody = $('#equipment-list-body');
            tbody.html('');

            if (equipmentList.length === 0) {
                tbody.html(`
                <tr id="no-equipment-alert">
                    <td colspan="4" class="text-center pe-0 px-0">
                        <div class="alert alert-warning" role="alert">
                            Chưa có thiết bị nào được thêm vào danh sách. Vui lòng chọn thiết bị
                        </div>
                    </td>
                </tr>
            `);
            } else {
                equipmentList.forEach((item, equipmentIndex) => {
                    item.batches.forEach(batch => {
                        const row = $('<tr>');
                        row.html(`
                            <td class="text-center">${item.equipment_code}</td>
                            <td class="text-center">${batch.batch_code}</td>
                            <td class="text-center">${batch.quantity}</td>
                            <td class="text-center">
                                <button type="button"
                                    onclick="removeFromList(${equipmentIndex}, '${batch.batch_code}')"><i class='fa fa-trash'></i></button>
                            </td>
                        `);
                        tbody.append(row);
                    });
                });
            }

            $('#equipment_list_input').val(JSON.stringify(equipmentList));
        }

        function removeFromList(equipmentIndex, batchCode) {
            let equipment = equipmentList[equipmentIndex];
            equipment.batches = equipment.batches.filter(batch => batch.batch_code !== batchCode);
            if (equipment.batches.length === 0) {
                equipmentList.splice(equipmentIndex, 1);
            }
            updateEquipmentListTable();
        }

        function resetForm() {
            $('#equipment_code').val('').trigger('change');
            $('#batch_info').html('');
        }
    </script>
@endsection

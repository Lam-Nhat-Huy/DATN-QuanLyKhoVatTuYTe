@extends('master_layout.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/add_export.css') }}">
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
                <a href="{{ route('warehouse.export') }}" class="btn btn-sm btn-dark" style="font-size: 12px;">
                    <i class="fa fa-arrow-left me-1"></i>Trở Lại
                </a>
            </div>
        </div>

        <!-- Form thêm vật tư -->
        <form action="{{ route('warehouse.store_export') }}" method="POST">
            @csrf
            <div class="container mt-4">
                <div class="row">
                    <div class="col-8">
                        <div class="mt-3">
                            <div class="row mb-3">
                                <div class="col-12 mb-2">
                                    <label for="material_code" class="required form-label mb-2">Tên vật tư</label>
                                    <select class="form-select form-select-sm" id="material_code" name="material_code"
                                        style="width: 100%;">
                                        <option value="" selected disabled>Chọn vật tư</option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material['code'] }}">{{ $material['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <h6 class="mb-3">Danh sách lô:</h6>
                                    <div id="batch_info" class="list-group">
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="button" class="btn btn-sm btn-danger" id="add-to-list">Thêm vào danh
                                        sách</button>
                                </div>
                            </div>
                        </div>


                        <!-- Bảng danh sách vật tư đã thêm -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="material-list">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tên Vật Tư</th>
                                        <th>Số Lô</th>
                                        <th>Số Lượng</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="card border-0 shadow p-4 mb-4 bg-white rounded-3 mt-3">
                            <h6 class="mb-3 fw-bold text-primary">Thông tin phiếu xuất</h6>

                            <div class="mb-3">
                                <label for="department_code" class="form-label fw-semibold">Mã phòng ban</label>
                                <select name="department_code" class="form-control form-control-sm" id="department_code"
                                    required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department['code'] }}">{{ $department['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="created_by" class="form-label fw-semibold">Người tạo</label>
                                <select name="created_by" class="form-control form-control-sm" id="created_by" required>
                                    <option value="">-- Chọn người tạo --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="export_at" class="form-label fw-semibold">Ngày xuất</label>
                                <input type="date" name="export_at" class="form-control form-control-sm" id="export_at"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label fw-semibold">Ghi chú</label>
                                <textarea name="note" class="form-control form-control-sm" id="note" rows="3"
                                    placeholder="Nhập ghi chú..."></textarea>
                            </div>

                            <hr class="my-4">
                            <!-- Input ẩn để lưu trữ danh sách vật tư và lô -->
                            <input type="hidden" name="material_list" id="material_list_input">


                            <button type="submit" class="btn btn-sm btn-danger">Xuất Kho</button>

                        </div>
                    </div>
                </div>


            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        const inventories = @json($inventories);

        let materialList = [];

        document.getElementById('material_code').addEventListener('change', function() {
            const selectedMaterial = this.value;
            const batchDetailsContainer = document.getElementById('batch_info');

            batchDetailsContainer.innerHTML = '';

            const filteredInventories = inventories.filter(inv => inv.material_code === selectedMaterial);

            if (filteredInventories.length > 0) {
                filteredInventories.forEach(inventory => {
                    let inputField = '';
                    let textColor = '';

                    const quantity = Number(inventory.current_quantity);

                    if (quantity === 0) {
                        textColor = 'color: red;';
                        inputField = `
                            <input type="text" class="form-control form-control-sm"
                                   value="Hết Hàng" readonly style="max-width: 100px; background-color: #f8d7da;">
                        `;
                    } else if (quantity < 10) {
                        textColor = 'color: orange;';
                        inputField = `
                            <input type="number" class="form-control form-control-sm"
                                   name="batches[${inventory.batch_code}]"
                                   id="batch_${inventory.batch_code}"
                                   min="0" max="${quantity}"
                                   placeholder="Số Lượng" style="max-width: 100px;">
                        `;
                    } else {
                        textColor = 'color: green;';
                        inputField = `
                            <input type="number" class="form-control form-control-sm"
                                   name="batches[${inventory.batch_code}]"
                                   id="batch_${inventory.batch_code}"
                                   min="0" max="${quantity}"
                                   placeholder="Số Lượng" style="max-width: 100px;">
                        `;
                    }

                    const batchElement = document.createElement('div');
                    batchElement.classList.add('list-group-item', 'd-flex', 'align-items-center',
                        'justify-content-between');
                    batchElement.innerHTML = `
                        <div style="${textColor}">
                            <strong>Số Lô: ${inventory.batch_code}</strong>
                            <span class="text-muted">(Tồn: ${quantity})</span>
                            <span class="text-muted">(HSD: ${inventory.expiry_date})</span>
                        </div>
                        <div class="ms-2" style="width: 100px;">
                            ${inputField}
                        </div>
                    `;
                    batchDetailsContainer.appendChild(batchElement);
                });
            }
        });

        document.getElementById('add-to-list').addEventListener('click', function() {
            const selectedMaterial = document.getElementById('material_code').value;
            const departmentCode = document.getElementById('department_code').value;
            const createdBy = document.getElementById('created_by').value;
            const exportDate = document.getElementById('export_at').value;
            const note = document.getElementById('note').value;

            const batches = Array.from(document.querySelectorAll('#batch_info input[type="number"]'))
                .filter(input => input.value > 0)
                .map(input => ({
                    batch_code: input.name.replace('batches[', '').replace(']', ''),
                    quantity: parseInt(input.value)
                }));

            if (selectedMaterial && batches.length > 0) {
                materialList.push({
                    material_code: selectedMaterial,
                    batches,
                    department_code: departmentCode,
                    created_by: createdBy,
                    export_at: exportDate,
                    note
                });

                // Cập nhật bảng hiển thị danh sách vật tư
                updateMaterialListTable();

                // Reset form
                resetForm();
            }
        });

        function updateMaterialListTable() {
            const tbody = document.querySelector('#material-list tbody');
            tbody.innerHTML = '';
            materialList.forEach((item, index) => {
                item.batches.forEach(batch => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-center">${item.material_code}</td>
                        <td class="text-center">${batch.batch_code}</td>
                        <td class="text-center">${batch.quantity}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeFromList(${index})">Xóa</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });

            // Cập nhật input ẩn để gửi danh sách vật tư tới server
            document.getElementById('material_list_input').value = JSON.stringify(materialList);
        }

        function removeFromList(index) {
            materialList.splice(index, 1);
            updateMaterialListTable();
        }

        function resetForm() {
            document.getElementById('material_code').value = '';
            document.getElementById('batch_info').innerHTML = '';
        }
    </script>
@endsection

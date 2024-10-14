@foreach ($equipments as $equipment)
    <tr class="hover-table" style="cursor: pointer;" data-bs-toggle="collapse"
        data-bs-target="#collapse{{ $equipment->code }}" aria-expanded="false">
        <td>
            {{ $loop->iteration }}
        </td>
        <td>{{ $equipment->code }}</td>
        <td style="text-align: left;">{{ $equipment->name }}</td>
        <td>{{ $equipment->equipmentType->name }}</td>
        <td>
            <div style="display: flex; justify-content: space-evenly; align-items: center;">
                @php
                    $totalQuantity = $inventories[$equipment->code]['total_quantity'] ?? 0;
                    $quantityColor = '';
                    $icon = '';
                    $bgColor = '';

                    if ($totalQuantity < 1) {
                        $quantityColor = 'color: red; font-weight: bold;';
                        $icon =
                            '<i class="fa-solid fa-exclamation-triangle" style="color:red; font-size:18px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Hết hàng"></i>';
                        $bgColor = 'background-color: rgba(255, 0, 0, 0.1);';
                    } elseif ($totalQuantity <= 10) {
                        $quantityColor = 'color: orange; font-weight: bold;';
                        $icon =
                            '<i class="fa-solid fa-exclamation-triangle" style="color:orange; font-size:18px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Cảnh báo: Số lượng thấp"></i>';
                        $bgColor = 'background-color: rgba(255, 165, 0, 0.1);';
                    } else {
                        $quantityColor = 'color: #28a745; font-weight: bold;';
                        $icon =
                            '<i class="fa-solid fa-check-circle" style="color:#28a745; font-size:18px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Còn hàng"></i>';
                        $bgColor = 'background-color: rgba(40, 167, 69, 0.1);';
                    }
                @endphp

                <span style="{{ $quantityColor }}">
                    {{ $totalQuantity }}
                </span>

                {!! $icon !!}
            </div>
        </td>
        <td>{{ $equipment->units->name }}</td>
    </tr>

    <tr class="collapse multi-collapse" id="collapse{{ $equipment->code }}" style="{{ $bgColor }}">
        <td colspan="6" class="p-0" style="border: 1px solid #dcdcdc; background-color: #fafafa;">
            <div class="card card-flush p-2" style="border: none; margin: 0;">
                <div class="card-body p-2">
                    <div class="table-responsive rounded">
                        <table class="table table-sm ">
                            <thead class="fw-bolder bg-danger text-white">
                                <tr>
                                    <th class="ps-4">STT</th>
                                    <th>Số lô</th>
                                    <th>Số lượng</th>
                                    <th>Ngày sản xuất</th>
                                    <th>Hạn sử dụng</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemsTableBody">
                                @forelse ($inventories[$equipment->code]['inventories'] as $index => $inventory)
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $expiryDate = \Carbon\Carbon::parse($inventory->expiry_date);
                                        $fiveMonthsLater = \Carbon\Carbon::now()->addMonths(5);
                                        $rowClass = '';
                                        if ($expiryDate <= $now) {
                                            $rowClass =
                                                '<i class="fa-solid fa-exclamation-triangle" style="color:red;font-size:18px;padding-left:5px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Hết hạn"></i>';
                                        } elseif ($expiryDate > $now && $expiryDate <= $fiveMonthsLater) {
                                            $rowClass =
                                                '<i class="fa-solid fa-exclamation-triangle" style="color:orange;font-size:18px;padding-left:5px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Sắp hết hạn"></i>';
                                        }
                                    @endphp

                                    <tr class="text-center"
                                        style="background-color: {{ $inventory->current_quantity < 1 ? 'rgba(255, 0, 0, 0.1)' : ($inventory->current_quantity <= 10 ? 'rgba(255, 165, 0, 0.1)' : 'rgba(40, 167, 69, 0.1)') }};">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $inventory->batch_number }}</td>
                                        <td>{{ $inventory->current_quantity }}</td>
                                        <td>{{ \Carbon\Carbon::parse($inventory->manufacture_date)->format('d/m/Y') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($inventory->expiry_date)->format('d/m/Y') }}
                                            {!! $rowClass !!}</td>
                                    </tr>

                                @empty
                                    <tr id="noDataAlert">
                                        <td colspan="12" class="text-center">
                                            <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                                role="alert"
                                                style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                                <div class="mb-3">
                                                    <i class="fas fa-file-invoice"
                                                        style="font-size: 36px; color: #6c757d;"></i>
                                                </div>
                                                <div class="text-center">
                                                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">
                                                        Thông tin tồn kho trống</h5>
                                                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                        Hiện tại chưa có thiết bị nào được thêm vào.
                                                        Vui lòng kiểm tra lại hoặc
                                                        tạo mới thiết bị để bắt đầu.
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
            </div>
        </td>
    </tr>
@endforeach

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

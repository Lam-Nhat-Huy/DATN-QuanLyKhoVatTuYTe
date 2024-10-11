@forelse ($equipments as $equipment)
    <tr class="hover-table" style="cursor: pointer;" data-bs-toggle="collapse"
        data-bs-target="#collapse{{ $equipment->code }}" aria-expanded="false">
        <td>
            <input type="checkbox" class="row-checkbox" />
        </td>
        <td>{{ $equipment->name }}</td>
        <td>{{ $equipment->equipmentType->name }}</td>
        <td>{{ $inventories[$equipment->code]['total_quantity'] ?? 0 }}</td>
        <td>{{ $equipment->units->name }}</td>
    </tr>
    <tr class="collapse multi-collapse" id="collapse{{ $equipment->code }}">
        <td colspan="6" class="p-0" style="border: 1px solid #dcdcdc; background-color: #fafafa;">
            <div class="card card-flush p-2" style="border: none; margin: 0;">
                <div class="card-body p-2">
                    <div class="table-responsive rounded">
                        <table class="table table-striped table-sm table-hover">
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
                                                '<i class="fa-solid fa-triangle-exclamation" style="color:red"></i>';
                                        } elseif ($expiryDate > $now && $expiryDate <= $fiveMonthsLater) {
                                            $rowClass =
                                                '<i class="fa-solid fa-triangle-exclamation" style="color:orange"></i>';
                                        }
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $inventory->batch_number }}</td>
                                        <td>{{ $inventory->current_quantity }}</td>
                                        <td>{{ $inventory->manufacture_date }}</td>
                                        <td>{{ $inventory->expiry_date }} {!! $rowClass !!}</td>
                                    </tr>
                                @empty
                                    <tr id="noDataAlert">
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                                role="alert"
                                                style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                                <div class="mb-3">
                                                    <i class="fas fa-file-invoice"
                                                        style="font-size: 36px; color: #6c757d;"></i>
                                                </div>
                                                <div class="text-center">
                                                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông
                                                        tin tồn kho trống</h5>
                                                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                        Không có lô sản phẩm nào thỏa mãn điều kiện lọc.
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
@empty
    <tr id="noDataAlert">
        <td colspan="12" class="text-center">
            <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                role="alert" style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                <div class="mb-3">
                    <i class="fas fa-search" style="font-size: 36px; color: #6c757d;"></i>
                </div>
                <div class="text-center">
                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có kết quả tìm kiếm</h5>
                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                        Không tìm thấy kết quả phù hợp với yêu cầu tìm kiếm của bạn. Vui lòng thử lại với từ khóa khác.
                    </p>
                </div>
            </div>
        </td>
    </tr>
@endforelse

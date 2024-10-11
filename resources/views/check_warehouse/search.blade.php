@forelse ($inventoryChecks as $item)
    @php
        $totalUnequal = collect($item['details'])->sum('unequal');
    @endphp

    <tr class="text-center hover-table pointer" data-bs-toggle="collapse" data-bs-target="#collapse{{ $item['code'] }}"
        aria-expanded="false" aria-controls="collapse{{ $item['code'] }}">
        <td>
            <!-- Sử dụng Font Awesome icon để hiển thị mũi tên -->
            <i class="row-icon fa fa-chevron-right"></i>
        </td>
        <td>{{ $item['code'] }}</td>
        <td>{{ \Carbon\Carbon::parse($item['check_date'])->format('d/m/Y') }}</td>
        <td>{{ $totalUnequal }}</td> <!-- Hiển thị tổng chênh lệch -->
        <td>{{ $item['details']->where('unequal', '>', 0)->sum('unequal') }}</td>
        <!-- Số lượng lệch tăng -->
        <td>{{ $item['details']->where('unequal', '<', 0)->sum('unequal') }}</td>
        <!-- Số lượng lệch giảm -->
        <td>
            @if ($item['status'] == 0)
                <span class="label label-temp text-warning">Phiếu lưu tạm</span>
            @elseif($item['status'] == 1)
                <span class="label label-final text-success">Đã cân bằng</span>
            @else
                <span class="label label-temp text-danger">Phiếu đã hủy</span>
            @endif
        </td>
    </tr>

    <!-- Collapse content -->
    <tr class="collapse multi-collapse" id="collapse{{ $item['code'] }}">
        <td class="p-0" colspan="12"
            style="border: 1px solid #dcdcdc; background-color: #fafafa; padding-top: 0 !important;">
            <div class="flex-lg-row-fluid border-2 border-lg-1">
                <div class="card card-flush p-2 mb-3"
                    style="padding-top: 0px !important; padding-bottom: 0px !important;">
                    <div class="card-header d-flex justify-content-between align-items-center p-2"
                        style="padding-top: 0 !important; padding-bottom: 0px !important;">
                        <h4 class="fw-bold m-0">Chi tiết phiếu kiểm kho</h4>
                    </div>
                    <div class="card-body p-2" style="padding-top: 0px !important">
                        <div class="row py-5" style="padding-top: 0px !important">
                            <!-- Begin::Receipt Info (Left column) -->
                            <div class="col-md-4">
                                <table class="table table-flush gy-1">
                                    <tbody>
                                        <tr>
                                            <td class="" style="width: 150px;"><strong>Mã kiểm
                                                    kho</strong></td>
                                            <td class="text-gray-800">{{ $item['code'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Thời gian</strong></td>
                                            <td class="text-gray-800">
                                                {{ \Carbon\Carbon::parse($item['created_by'])->format('d/m/Y H:i:s') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Ngày cân bằng</strong></td>
                                            <td class="text-gray-800">
                                                {{ \Carbon\Carbon::parse($item['check_date'])->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Ghi chú</strong></td>
                                            <td class="text-gray-800">{{ $item['note'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-4">
                                <table class="table table-flush gy-1">
                                    <tbody>
                                        <tr>
                                            <td class=""><strong>Trạng thái</strong></td>
                                            <td class="text-gray-800">
                                                @if ($item['status'] == 0)
                                                    <span class="text-warning">Phiếu lưu tạm</span>
                                                @elseif($item['status'] == 1)
                                                    <span class="text-success">Đã cân bằng</span>
                                                @else
                                                    <span class="text-danger">Phiếu đã hủy</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=""><strong>Tài khoản tạo</strong></td>
                                            <td class="text-gray-800">
                                                {{ $item->user->last_name . ' ' . $item->user->first_name }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="table-responsive rounded">
                                <table class="table table-striped table-sm table-hover">
                                    <thead style="background-color: #FFA500;">
                                        <tr class="text-center">
                                            <th style="width: 15%;" class="ps-3">Mã thiết bị</th>
                                            <th style="width: 15%;">Tên thiết bị</th>
                                            <th style="width: 15%;">Số lô</th>
                                            <th style="width: 15%;">Tồn kho</th>
                                            <th style="width: 15%;">Số lượng thực tế</th>
                                            <th style="width: 15%;">Số lượng lệch</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['details'] as $detail)
                                            <tr class="text-center">
                                                <td class="ps-4">{{ $detail['equipment_code'] }}
                                                </td>
                                                <td>{{ $detail->equipment->name }}</td>
                                                <td>{{ $detail['batch_number'] }}</td>
                                                <td>{{ $detail['current_quantity'] }}</td>
                                                <td>{{ $detail['actual_quantity'] }}</td>
                                                <td>{{ $detail['unequal'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- End card-body -->
                </div> <!-- End card -->

                <div class="card-body py-3 text-end">
                    <div class="button-group">
                        <!-- Nút Duyệt đơn, chỉ hiển thị khi là Phiếu Tạm -->
                        @if ($item['status'] == 0)
                            <button style="font-size: 10px;" class="btn btn-sm btn-success me-2 rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#browse-{{ $item->code }}" type="button">
                                <i style="font-size: 10px;" class="fas fa-clipboard-check"></i>
                                Duyệt Phiếu
                            </button>

                            <!-- Nút Xóa phiếu tạm -->
                            <button style="font-size: 10px;" class="btn btn-danger btn-sm me-2 rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#delete-{{ $item->code }}">
                                <i style="font-size: 10px;" class="fa fa-trash"></i> Xóa Phiếu
                            </button>
                        @endif

                        @if ($item['status'] == 1)
                            <!-- Nút In Phiếu -->
                            <button style="font-size: 10px;" class="btn btn-sm btn-dark me-2 rounded-pill"
                                id="printPdfBtn" type="button">
                                <i style="font-size: 10px;" class="fa fa-print"></i> In Phiếu
                            </button>

                            <!-- Nút Hủy Phiếu -->
                            <button style="font-size: 10px;" class="btn btn-danger btn-sm rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#cancel-{{ $item->code }}">
                                <i style="font-size: 10px;" class="fa fa-times"></i> Hủy Phiếu
                            </button>
                        @endif

                        @if ($item['status'] == 3)
                            <!-- Nút Xóa phiếu đã hủy -->
                            <button style="font-size: 10px;" class="btn btn-danger btn-sm me-2 rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#delete-{{ $item->code }}">
                                <i style="font-size: 10px;" class="fa fa-trash"></i> Xóa Phiếu
                            </button>
                        @endif

                        <!-- Modal Duyệt Phiếu -->
                        <div class="modal fade" id="browse-{{ $item['code'] }}" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="browseLabel-{{ $item['code'] }}"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title text-white" id="browseLabel-{{ $item['code'] }}">
                                            Duyệt Phiếu Kiểm Kho
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center pb-0">
                                        <form action="{{ route('check_warehouse.approve', $item['code']) }}"
                                            method="POST" id="approveForm-{{ $item['code'] }}">
                                            @csrf
                                            <p class="text-dark mb-4">Bạn có chắc chắn muốn duyệt
                                                phiếu kiểm kho này?</p>
                                        </form>
                                    </div>
                                    <div class="modal-footer justify-content-center pt-0">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill"
                                            data-bs-dismiss="modal">
                                            Đóng
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm rounded-pill"
                                            onclick="event.preventDefault(); document.getElementById('approveForm-{{ $item['code'] }}').submit();">
                                            Duyệt
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hủy Phiếu -->
                        <div class="modal fade" id="cancel-{{ $item['code'] }}" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1"
                            aria-labelledby="cancelLabel-{{ $item['code'] }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title text-white" id="cancelLabel-{{ $item['code'] }}">
                                            Hủy Phiếu Kiểm Kho
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center pb-0">
                                        <form action="{{ route('check_warehouse.cancel', $item['code']) }}"
                                            method="POST" id="cancelForm-{{ $item['code'] }}">
                                            @csrf
                                            <p class="text-danger mb-4">Bạn có chắc chắn muốn hủy
                                                phiếu kiểm kho này?
                                                Số lượng vật tư sẽ được trả về trạng thái trước khi
                                                kiểm.</p>
                                        </form>
                                    </div>
                                    <div class="modal-footer justify-content-center pt-0">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill"
                                            data-bs-dismiss="modal">Đóng</button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-pill"
                                            onclick="event.preventDefault(); document.getElementById('cancelForm-{{ $item['code'] }}').submit();">
                                            Hủy Phiếu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Xóa Phiếu -->
                        <div class="modal fade" id="delete-{{ $item['code'] }}" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1"
                            aria-labelledby="deleteLabel-{{ $item['code'] }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title text-white" id="deleteLabel-{{ $item['code'] }}">
                                            Xóa Phiếu Kiểm Kho
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center pb-0">
                                        <form action="{{ route('check_warehouse.delete', $item['code']) }}"
                                            method="POST" id="deleteForm-{{ $item['code'] }}">
                                            @csrf
                                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa
                                                phiếu kiểm kho này?</p>
                                        </form>
                                    </div>
                                    <div class="modal-footer justify-content-center pt-0">
                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill"
                                            data-bs-dismiss="modal">Đóng</button>
                                        <button type="button" class="btn btn-danger btn-sm rounded-pill"
                                            onclick="event.preventDefault(); document.getElementById('deleteForm-{{ $item['code'] }}').submit();">
                                            Xóa Phiếu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End flex-lg-row-fluid -->
        </td>
    </tr>
@empty
    <tr id="noDataAlert">
        <td colspan="12" class="text-center">
            <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                role="alert" style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                <div class="mb-3">
                    <i class="fas fa-clipboard-check" style="font-size: 36px; color: #6c757d;"></i>
                </div>
                <div class="text-center">
                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông tin phiếu
                        kiểm kho trống</h5>
                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                        Hiện tại chưa có phiếu kiểm kho nào được tạo. Vui lòng kiểm tra lại hoặc tạo
                        mới phiếu kiểm kho để bắt đầu.
                    </p>
                </div>
            </div>
        </td>
    </tr>
@endforelse

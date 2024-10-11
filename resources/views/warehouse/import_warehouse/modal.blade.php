<!-- Modal nhập excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded">
            <!-- Header Section -->
            <div class="modal-header bg-gradient-primary-to-secondary text-white rounded">
                <h5 class="modal-title fw-bold" id="importExcelModalLabel">
                    <i class="fa-solid fa-file-import me-2"></i> Nhập Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body Section -->
            <div class="modal-body p-4 bg-light rounded">
                <!-- Notice Section -->
                <div class="bg-white border-0 p-4 rounded-3 shadow-sm mb-4">
                    <p class="mb-3">
                        Tải về file mẫu:
                        <a href="{{ route('warehouse.exportExcel') }}"
                            class="text-decoration-underline text-primary fw-semibold">
                            Tải File Excel Mẫu
                        </a>
                    </p>
                    <p class="fw-bold text-danger mb-2">Lưu ý:</p>
                    <ul class="ps-3 text-dark">
                        <li>Hỗ trợ tối đa <strong>500</strong> hàng hóa mỗi lần nhập.</li>
                        <li>Chỉnh sửa dòng bị lỗi nếu có hàng hóa không hợp lệ.</li>
                        <li>Số lượng hàng hóa không quản lý Serial phải lớn hơn 0.</li>
                        <li>Giá nhập, giá bán phải >= 0.</li>
                        <li>Serial phải là duy nhất, không trùng lặp trong hệ thống.</li>
                        <li>Với hàng sản xuất định lượng, vui lòng tạo phiếu sản xuất để ghi nhận tồn kho chính xác.
                        </li>
                    </ul>
                </div>

                <!-- File Upload Section -->
                <form action="{{ route('warehouse.importExcel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="border rounded p-5 text-center bg-white position-relative shadow-sm"
                        style="border: 2px dashed #007bff;">
                        <label for="excelFile" class="form-label fw-semibold pointer">
                            <i class="fa-solid fa-file-excel fa-3x text-success mb-3"></i><br>
                            <span class="text-muted">Kéo thả hoặc click vào đây để chọn file Excel</span>
                        </label>
                        <input type="file" id="excelFile" name="file" class="form-control d-none"
                            accept=".xls,.xlsx" required>
                        <p class="mt-3 text-dark small">Dung lượng tối đa: 10MB</p>
                    </div>

                    <!-- Footer Section -->
                    <div class="modal-footer bg-light border-0 pe-0 pb-1 pt-4">
                        <button type="button" class="btn btn-sm rounded-pill btn-secondary"
                            data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-sm rounded-pill btn-twitter">Tải lên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

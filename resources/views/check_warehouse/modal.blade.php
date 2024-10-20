<!-- Modal nhập excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="displayCatagoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('warehouse.importCheckWarehouseExcel') }}" method="POST" enctype="multipart/form-data"
            class="text-center">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="fw-bold m-0 text-uppercase fw-bolder">
                        Nhập file dữ liệu
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Notice Section -->
                    <div class="alert alert-warning rounded-3 p-3 mb-4 text-start">
                        <p class="mb-1">
                            Tải về file mẫu: <a href="{{ route('warehouse.exportCheckWarehouseExcel') }}"
                                class="text-decoration-none text-primary">Excel mẫu</a> (danh sách tất cả thiết bị có
                            trong kho)
                        </p>
                        <p class="fw-bold text-danger mb-2">Lưu ý:</p>
                        <ul class="mb-0 text-muted">
                            <li>- Vui lòng nhập đúng và đủ số liệu theo mẫu được yêu cầu để hệ thống xử lý chính xác.
                            </li>
                            <li>- Đảm bảo các cột và định dạng trong file Excel không bị thay đổi so với mẫu đã cung
                                cấp.
                            </li>
                            <li>- Kiểm tra kỹ thông tin như mã số thiết, tồn kho trước khi nhập để
                                tránh sai sót. Những lỗi nhập sai số liệu có thể ảnh hưởng đến kết quả kiểm kê kho.</li>
                        </ul>

                    </div>

                    <div class="file-upload-wrapper p-4 text-center rounded-3 position-relative"
                        style="border: 2px dashed #4CAF50; transition: all 0.3s ease-in-out; background-color: #f8f9fa;">
                        <label for="excelFile" class="form-label fw-semibold text-secondary"
                            style="cursor: pointer; transition: color 0.3s; margin-bottom: 0px !important;">
                            <i class="fa-solid fa-file-excel fa-2x text-success"></i><br>
                            <span class="upload-text text-dark " style="font-size: 12px">Chọn file dữ liệu vào
                                đây</span>
                        </label>

                        <input type="file" name="file" id="excelFile" class="form-control d-none"
                            accept=".xls,.xlsx" onchange="displayFileName()">
                        <div id="fileName" class="mt-3 fw-semibold text-success"></div> <!-- Hiển thị tên file -->
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-danger btn-sm rounded-pill" style="font-size: 12px;"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill" style="font-size: 12px;">Tải
                        lên</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function displayFileName() {
        const fileInput = document.getElementById('excelFile');
        const fileNameDisplay = document.getElementById('fileName');

        if (fileInput.files.length > 0) {
            const fileName = fileInput.files[0].name; // Lấy tên file
            fileNameDisplay.textContent = `File đã tải lên: ${fileName}`; // Hiển thị tên file
        } else {
            fileNameDisplay.textContent = ''; // Nếu không có file nào được chọn, xóa nội dung
        }
    }
</script>

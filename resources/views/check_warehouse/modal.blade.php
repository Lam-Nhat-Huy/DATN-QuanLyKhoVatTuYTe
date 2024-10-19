<!-- Modal nhập excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="displayCatagoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('warehouse.importCheckWarehouseExcel') }}" method="POST" enctype="multipart/form-data"
            class="text-center">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-primary" id="importExcelModalLabel">Nhập Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Notice Section -->
                    <div class="alert alert-warning rounded-3 p-3 mb-4 text-start">
                        <p class="mb-1">
                            Tải về file mẫu: <a href="{{ route('warehouse.exportCheckWarehouseExcel') }}"
                                class="text-decoration-none text-primary">Excel mẫu</a>
                        </p>
                        <p class="fw-bold text-danger mb-2">Lưu ý:</p>
                        <ul class="mb-0 text-muted">
                            <li>"Hệ thống hiện tại chỉ hỗ trợ tối đa <strong>500</strong> thiết bị cho mỗi lần nhập dữ
                                liệu từ file
                                Excel, điều này có nghĩa là khi bạn thực hiện việc nhập liệu từ một tệp Excel, số lượng
                                thiết bị không được vượt quá con số này, nhằm đảm bảo hiệu suất và tính chính xác của
                                quá trình xử lý dữ liệu."</li>
                        </ul>
                    </div>

                    <!-- File Upload Section -->
                    <div class="rounded-3 p-4 text-center" style="border: 2px dashed #000">
                        <label for="excelFile" class="form-label fw-semibold text-secondary">
                            <i class="fa-solid fa-file-excel fa-2x text-dark mb-3"></i><br>
                            <span class="text-dark">Click vào để chọn file Excel</span>
                        </label>
                        <input type="file" name="file" id="excelFile" class="form-control d-none"
                            accept=".xls,.xlsx" onchange="displayFileName()">
                        <div id="fileName" class="mt-2 text-dark"></div> <!-- Div để hiển thị tên file -->
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-danger btn-sm rounded-pill"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill">Tải lên</button>
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

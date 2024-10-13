<div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="editMaterialModalLabel">Cập nhật vật tư</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editInputQuantity" class="form-label text-start">Số lượng</label>
                    <input type="number" class="form-control rounded-pill " id="editInputQuantity" min="1">
                    <span id="editQuantityError" class="invalid-feedback"></span>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                    data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-sm btn-success rounded-pill" id="saveEditMaterial">Lưu</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-danger mb-0">Bạn có chắc chắn muốn xóa vật tư này không?</p>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                    data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-sm btn-danger rounded-pill" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Nhập Số Lượng -->
<div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="quantityModalLabel">Nhập số lượng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quantityForm">
                    <div class="mb-3">
                        <label for="inputQuantity" class="form-label">Số lượng:</label>
                        <input type="number" class="form-control rounded-pill" id="inputQuantity" min="1"
                            required>
                        <div id="quantityError" class="invalid-feedback"></div>
                    </div>
                    <input type="hidden" id="batchNumberModal">
                    <input type="hidden" id="currentQuantityModal">
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-sm  btn-secondary rounded-pill"
                    data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-sm btn-success rounded-pill" id="saveQuantity">Lưu</button>
            </div>
        </div>
    </div>
</div>


{{-- Modal Duyệt Tất Cả --}}
<div class="modal fade" id="confirmAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="confirmAll" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white" id="confirmAll">Duyệt Tất Cả Phiếu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding-bottom: 0px;">
                <form action="" method="">
                    @csrf
                    <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt tất cả phiếu đã chọn?</p>
                </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-sm btn-secondary btn-sm px-4"
                    data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-sm btn-success px-4">
                    Duyệt</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Xác Nhận Xóa Tất Cả --}}
<div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="deleteAllLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Tất Cả Phiếu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding-bottom: 0px;">
                <form action="" method="">
                    @csrf
                    <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả phiếu đã chọn?</p>
                </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-sm btn-success px-4"> Xóa</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Duyệt Phiếu --}}
<div class="modal fade" id="browse" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="browseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white" id="browseLabel">Duyệt Phiếu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding-bottom: 0px;">
                <form action="" method="">
                    @csrf
                    <p class="text-danger mb-4">Bạn có chắc chắn muốn duyệt phiếu này?</p>
                </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-sm btn-secondary btn-sm px-4"
                    data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-sm btn-success px-4">
                    Duyệt</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Xác Nhận Xóa --}}
<div class="modal fade" id="deleteConfirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteConfirmLabel">Xác Nhận Xóa Phiếu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding-bottom: 0px;">
                <form action="" method="">
                    @csrf
                    <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa phiếu này?</p>
                </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-sm btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-sm btn-danger px-4"> Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- Chi tiết  -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <!-- Modal header -->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-twitter" data-bs-dismiss="modal"
                    aria-label="Close">
                    X
                </button>
            </div>
            <!-- Modal body -->
            <div id="printArea">
                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                    <form action="" method="post">
                        <div class="text-center mb-13">
                            <h1 class="mb-3">Phiếu Xuất</h1>
                            <div class="text-muted fw-bold fs-6">Thông Tin Chi Tiết Về Phiếu Xuất Kho
                                <span class="link-twitter fw-bolder">#MaXuatKho</span>.
                            </div>
                        </div>
                        <div class="mb-15">
                            <!-- Begin::Export Info -->
                            <div class="mb-4">
                                <h4 class="text-twitter border-bottom border-dark pb-4">Thông tin phiếu xuất</h4>
                                <div class="row pt-3">
                                    <div class="col-4">
                                        <p><strong>Mã Phiếu Xuất:</strong> <span id="modalExportCode">PX00019</span>
                                        </p>
                                        <p><strong>Số Phiếu Xuất:</strong> <span id="modalExportNumber">025</span></p>
                                        <p><strong>Khách Hàng:</strong> <span id="modalCustomer">Nguyễn Văn A</span>
                                        </p>
                                        <p><strong>Ngày Xuất:</strong> <span id="modalExportDate">26/08/2024</span></p>
                                        <p><strong>Người Tạo:</strong> <span id="modalCreatedBy">Nhật Huy</span></p>
                                        <p><strong>Ghi Chú:</strong> <span id="modalNote">Hàng dễ vỡ</span></p>
                                    </div>
                                </div>
                            </div>
                            <!-- End::Export Info -->

                            <!-- Begin::Export Items -->
                            <div class="mb-4">
                                <h4 class="text-twitter border-bottom border-dark pb-4 mb-4">Danh sách vật tư</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm table-hover">
                                        <thead class="fw-bolder bg-success">
                                            <tr>
                                                <th class="ps-3">Mã vật tư</th>
                                                <th>Số lượng</th>
                                                <th>Đơn giá</th>
                                                <th>Số lô</th>
                                                <th>Chiết khấu (%)</th>
                                                <th>VAT (%)</th>
                                                <th class="pe-3">Tổng giá</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalItemsTableBody">
                                            <tr>
                                                <td>VT001</td>
                                                <td>10</td>
                                                <td>50,000 VND</td>
                                                <td>L001</td>
                                                <td>5</td>
                                                <td>10</td>
                                                <td>55,000 VND</td>
                                            </tr>
                                            <tr>
                                                <td>VT002</td>
                                                <td>20</td>
                                                <td>30,000 VND</td>
                                                <td>L002</td>
                                                <td>0</td>
                                                <td>10</td>
                                                <td>33,000 VND</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- End::Export Items -->

                            <!-- Begin::Summary -->
                            <div class="card p-4" style="background-color: #e1e9f4; border: 1px solid #e3e3e3;">
                                <h5 class="card-title">Tổng Cộng</h5>
                                <hr>
                                <p class="mb-1">Tổng Tiền Hàng:
                                    <span class="fw-bold" id="modalSubtotal">12.000.000 VND</span>
                                </p>
                                <p class="mb-1">Tổng Chiết Khấu:
                                    <span class="fw-bold" id="modalTotalDiscount">0 VND</span>
                                </p>
                                <p class="mb-1">Tổng VAT:
                                    <span class="fw-bold" id="modalTotalVat">0 VND</span>
                                </p>
                                <p class="mb-1">Chi Phí Vận Chuyển:
                                    <span class="fw-bold" id="modalShippingCost">0 VND</span>
                                </p>
                                <p class="mb-1">Phí Khác:
                                    <span class="fw-bold" id="modalOtherFees">0 VND</span>
                                </p>
                                <hr>
                                <p class="fs-4 fw-bold text-success">Tổng:
                                    <span id="modalFinalTotal">12.000.000 VND</span>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chỉnh sửa -->
<div class="modal fade" id="editExportReceiptModal" tabindex="-1" aria-labelledby="editExportReceiptModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded">
            <!-- Modal Header -->
            <div class="modal-header border-0">
                <h5 class="modal-title" id="editExportReceiptModalLabel">Chỉnh Sửa Phiếu</h5>
                <button type="button" class="btn btn-sm btn-icon btn-dark" data-bs-dismiss="modal"
                    aria-label="Close">
                    X
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <form action="" method="post">
                    <!-- Export Receipt Info -->
                    <div class="mb-5">
                        <h5 class="text-twitter mb-3">Thông tin phiếu</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editExportCode" class="form-label">Mã Phiếu:</label>
                                    <input type="text" class="form-control form-control-sm" id="editExportCode"
                                        value="PX00019">
                                </div>
                                <div class="mb-3">
                                    <label for="editExportNumber" class="form-label">Số Phiếu:</label>
                                    <input type="text" class="form-control form-control-sm" id="editExportNumber"
                                        value="025">
                                </div>
                                <div class="mb-3">
                                    <label for="editCustomer" class="form-label">Khách Hàng:</label>
                                    <input type="text" class="form-control form-control-sm" id="editCustomer"
                                        value="Nguyễn Văn A">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editExportDate" class="form-label">Ngày:</label>
                                    <input type="date" class="form-control form-control-sm" id="editExportDate"
                                        value="2024-08-26">
                                </div>
                                <div class="mb-3">
                                    <label for="editCreatedBy" class="form-label">Người Tạo:</label>
                                    <input type="text" class="form-control form-control-sm" id="editCreatedBy"
                                        value="Nhật Huy">
                                </div>
                                <div class="mb-3">
                                    <label for="editNote" class="form-label">Ghi Chú:</label>
                                    <input class="form-control form-control-sm" id="editNote" rows="2"
                                        value="Hàng dễ vỡ">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Items -->
                    <div class="mb-5">
                        <h5 class="text-twitter">Danh sách vật tư</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="fw-bold bg-success">
                                        <th class="ps-3">Mã vật tư</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Số lô</th>
                                        <th>Chiết khấu (%)</th>
                                        <th>VAT (%)</th>
                                        <th>Tổng giá</th>
                                        <th class="pe-3">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="editItemsTableBody">
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm" value="VT001"
                                                disabled></td>
                                        <td><input type="number" class="form-control form-control-sm"
                                                value="10"></td>
                                        <td><input type="text" class="form-control form-control-sm"
                                                value="50,000 VND"></td>
                                        <td><input type="text" class="form-control form-control-sm"
                                                value="L001"></td>
                                        <td><input type="number" class="form-control form-control-sm"
                                                value="5"></td>
                                        <td><input type="number" class="form-control form-control-sm"
                                                value="10"></td>
                                        <td><input type="text" class="form-control form-control-sm"
                                                value="55,000 VND" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm">Xóa</button>
                                        </td>
                                    </tr>
                                    <!-- More rows as needed -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-twitter btn-sm">Thêm vật tư</button>
                    </div>

                    <!-- Summary -->
                    <div class="card p-3" style="background: #e1e9f4">
                        <h5 class="card-title">Tổng kết</h5>
                        <hr>
                        <p class="mb-1">Tổng tiền hàng:
                            <span class="fw-bold" id="editSubtotal">12.000.000 VND</span>
                        </p>
                        <p class="mb-1">Tổng chiết khấu:
                            <span class="fw-bold" id="editTotalDiscount">0 VND</span>
                        </p>
                        <p class="mb-1">Tổng VAT:
                            <span class="fw-bold" id="editTotalVat">0 VND</span>
                        </p>
                        <p class="mb-1">Chi phí vận chuyển:
                            <span class="fw-bold" id="editShippingCost">0 VND</span>
                        </p>
                        <p class="mb-1">Phí khác:
                            <span class="fw-bold" id="editOtherFees">0 VND</span>
                        </p>
                        <hr>
                        <p class="fs-4 fw-bold text-success">Tổng giá:
                            <span id="editFinalTotal">12.000.000 VND</span>
                        </p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-sm btn-success">Lưu Thay Đổi</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
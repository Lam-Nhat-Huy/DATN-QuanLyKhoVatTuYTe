@extends('master_layout.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        #productDropdown {
            position: absolute;
            z-index: 1000;
            width: calc(100% - 80px);
            display: none;
            max-height: 200px;
            overflow-y: auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #productDropdown a.dropdown-item:hover {
            background-color: #f0f0f0;
            /* Màu nền khi hover */
        }

        .d-flex.align-items-center>* {
            flex-shrink: 0;
        }

        .setupSelect2 {
            max-width: 70%;
        }

        .btn-outline-primary {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .material-form-container {
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
        }

        .form-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
            color: #333;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .form-group {
            flex: 1;
            margin-right: 10px;
        }

        .form-group:last-child {
            margin-right: 0;
        }

        .material-input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f5f5f5;
            color: #333;
            transition: border-color 0.3s;
        }

        .material-input:focus {
            border-color: #007bff;
            background-color: #fff;
            outline: none;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .btn {
            padding: 5px 15px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cancel-btn {
            color: #333;
            background: none;
            margin-right: 10px;
        }

        .save-continue-btn {
            color: #007bff;
            background: none;
            margin-right: 10px;
        }

        .save-btn {
            color: #007bff;
            background: none;
        }

        .btn:hover {
            opacity: 0.7;
        }

        .align-middle>tbody>tr>td,
        .align-middle>tbody>tr>th,
        .align-middle>thead>tr>td,
        .align-middle>thead>tr>th {
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .table input[type="number"] {
            width: 50px;
        }

        #noDataAlert {
            display: table-row;
        }

        .status-indicator {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .status-item {
            display: flex;
            align-items: center;
        }

        .color-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 10px;
        }

        .status-text {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .small-text {
            font-size: 0.95rem;
        }

        .pointer {
            transition: background-color 0.3s;
        }

        .pointer:hover,
        .pointer:focus {
            background-color: #e0e0e0;
            /* Màu nền khi hover hoặc focus */
            outline: none;
            /* Xóa viền focus mặc định */
            border-radius: 5px;
            /* Bo góc cho hiệu ứng mượt mà hơn */
        }
    </style>
@endsection

@section('title')
    Kiểm Kho
@endsection

@section('content')
    <div class="container mt-4">
        <form action="{{ route('check_warehouse.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-lg p-4 bg-body rounded-4">
                        <div class="container mt-4 position-relative px-0 pe-0">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchProductInput"
                                    placeholder="Nhập tên thiết bị để tiến hành thêm (Nhấn F2)" aria-label="Search"
                                    onkeyup="filterProducts()" onfocus="showDropdown()">
                                <button type="button" class="btn" style="background-color: #ff0000;"
                                    onclick="addAllProducts()">
                                    <i class="fas fa-tasks text-white"></i>
                                </button>
                            </div>

                            <div class="dropdown-menu w-750px" id="productDropdown" style="display:none;"></div>

                            <div class="modal fade" id="importantNotificationModal" data-bs-backdrop="static"
                                data-bs-keyboard="false" tabindex="-1" aria-labelledby="DetailModal" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header justify-content-center" style="background-color: #ff0000;">
                                            <h3 class="modal-title text-white" id="DetailModal">BEESOFT THÔNG BÁO</h3>
                                        </div>
                                        <div class="modal-body" id="importantNotificationContent">
                                        </div>
                                        <div class="modal-footer pt-2 pb-2">
                                            <button type="button" class="btn btn-sm text-white"
                                                style="font-size: 10px; background-color: #ff0000;"
                                                data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <ul class="d-flex list-unstyled">
                                <li class="me-5 text-dark pointer" id="filterAll">Tất cả (<span id="totalCount">0</span>)
                                </li>
                                <li class="me-5 text-success pointer" id="filterMatched">Khớp (<span
                                        id="matchedCount">0</span>)
                                </li>
                                <li class="me-5 text-warning pointer" id="filterMismatched">Lệch (<span
                                        id="mismatchedCount">0</span>)</li>
                                <li class="me-5 text-danger pointer" id="filterUnchecked">Chưa kiểm (<span
                                        id="uncheckedCount">0</span>)</li>
                            </ul>
                        </div>

                        <div class="mb-2">
                            <strong class="d-block text-dark mb-2">Hướng dẫn tổ hợp phím:</strong>
                            <div class="d-flex justify-content-between">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1 text-dark">
                                        - <strong>F2:</strong> Tìm kiếm thiết bị
                                    </li>
                                    <li class="mb-1 text-dark">
                                        - <strong>ENTER:</strong> Di chuyển xuống ô số lượng của thiết bị tiếp theo
                                    </li>
                                    <li class="mb-1 text-dark">
                                        - <strong>SHIFT:</strong> Di chuyển lên ô số lượng của thiết bị phía trên
                                    </li>
                                </ul>

                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1 text-dark">
                                        - <strong>ALT + A:</strong> Thêm tất cả sản phẩm vào danh sách
                                    </li>
                                    <li class="mb-1 text-dark">
                                        - <strong>ALT + F:</strong> Điền tất cả số lượng thực tế
                                    </li>
                                    <li class="mb-1 text-dark">
                                        - <strong>ALT + Q:</strong> Điền số lượng tồn kho cho sản phẩm đang chọn
                                    </li>
                                </ul>


                            </div>
                        </div>


                        <div class="table-responsive mt-4">
                            <table class="table text-center align-middle" style="background-color: #f4f6f9;">
                                <thead style="background-color: #000000;">
                                    <tr>
                                        <th style="width: 15%;">STT</th>
                                        <th style="width: 15%;">Mã thiết bị</th>
                                        <th style="width: 15%;">Tên thiết bị</th>
                                        <th style="width: 15%;">Số lô</th>
                                        <th style="width: 15%;">Tồn kho</th>
                                        <th style="width: 15%;">Thực tế</th>
                                        <th style="width: 10%;">SL lệch</th>
                                        <th style="width: 25%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="materialList">
                                    {{-- Thông tin sau khi được thêm vật tư từ FORM 1 sẽ được hiển thị ở đây --}}
                                    <tr id="noDataAlert">
                                        <td colspan="12" class="text-center">
                                            <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                                role="alert"
                                                style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057; height: 290px;">
                                                <div class="mb-3">
                                                    <i class="fas fa-box" style="font-size: 36px; color: #6c757d;"></i>
                                                    <!-- Đổi biểu tượng ở đây -->
                                                </div>
                                                <div class="text-center">
                                                    <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thông
                                                        tin
                                                        danh sách kiểm kho trống</h5>
                                                    <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                        Hiện tại chưa có thiết bị nào được thêm vào danh sách kiểm kho. Vui
                                                        lòng kiểm tra lại.
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>

                            <!-- Input ẩn để lưu trữ dữ liệu vật tư -->
                            <input type="hidden" id="materialData" name="materialData">
                        </div>

                    </div>
                </div>


                {{-- Giao diện kiểm kho mới --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg p-4 bg-body rounded-4 mb-5">
                        <h3 class="mb-4 text-dark text-uppercase">Chi tiết kiểm kho</h3>

                        <input type="hidden" id="created_by" value="{{ session('user_code') }}">

                        <!-- Trạng thái -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 13px;">Trạng thái</span>
                                <span class="badge"
                                    style="background-color: #08d63fce; color: white; padding: 0.5rem 1rem; border-radius: 50px; font-size: 9px;">Đang
                                    kiểm</span>
                            </div>
                        </div>

                        <!-- Mã kiểm kho -->
                        <div class="mb-4">
                            <label for="code" class="form-label fw-semibold text-dark" style="font-size: 13px;">Mã
                                kiểm kho</label>
                            <input type="text" id="code" class="form-control form-control-sm rounded-pill"
                                placeholder="Tạo tự động" disabled style="font-size: 12px;">
                        </div>

                        <!-- Ngày nhập -->
                        <div class="mb-4">
                            <label for="check_date" class="form-label fw-semibold text-dark"
                                style="font-size: 13px;">Ngày kiểm kho</label>
                            <input type="date" id="check_date" class="form-control form-control-sm rounded-pill"
                                value="{{ \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d') }}" readonly
                                style="pointer-events: none;">
                        </div>


                        <!-- Ghi chú -->
                        <div class="mb-4">
                            <label for="note" class="form-label fw-semibold text-dark" style="font-size: 13px;">Ghi
                                chú</label>
                            <textarea id="note" class="form-control form-control-lg rounded-3" placeholder="Nhập ghi chú..."
                                style="font-size: 12px;" rows="3"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-3">
                            <button name="status" value="0" onclick="submitMaterials()" type="submit"
                                class="btn btn-lg rounded-pill text-white" style="background-color: #FFA500;">Lưu phiếu
                                tạm</button>

                            <!-- Hoàn thành Button -->
                            <button type="button" class="btn text-white btn-lg rounded-pill"
                                style="background-color: #66CC00;" data-bs-toggle="modal"
                                data-bs-target="#completeModal">
                                Hoàn thành
                            </button>

                            <!-- Modal Hoàn thành -->
                            <div class="modal fade" id="completeModal" data-bs-backdrop="static"
                                data-bs-keyboard="false" tabindex="-1" aria-labelledby="completeModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title text-white" id="completeModalLabel">Duyệt phiếu kiểm
                                                kho</h5>
                                            <button type="button" class="btn-close btn-close-white rounded-pill"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center pb-0">
                                            <p>Bạn chắc chắn muốn cân bằng kho?</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pt-0">
                                            <button type="button" class="btn btn-sm btn-secondary px-4 rounded-pill"
                                                style="font-size: 12px" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" name="status" value="1"
                                                class="btn btn-sm btn-success px-4 rounded-pill" style="font-size: 12px"
                                                onclick="submitMaterials()">Duyệt
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card border-0 shadow-lg p-4 bg-body rounded-4" style="display: block;">
                        <div class="status-indicator">
                            <div class="status-item">
                                <div class="color-box" style="border: 1px solid black"></div>
                                <span class="status-text small-text">Chưa kiểm kê (Màu gạch đen)</span>
                            </div>
                            <div class="status-item">
                                <div class="color-box" style="background-color: #ffcccb;"></div>
                                <span class="status-text small-text">Thiếu số lượng (Màu đỏ nhạt)</span>
                            </div>
                            <div class="status-item">
                                <div class="color-box" style="background-color: #ffebc8;"></div>
                                <span class="status-text small-text">Thừa số lượng (Màu vàng nhạt)</span>
                            </div>
                            <div class="status-item">
                                <div class="color-box" style="background-color: #d1f0d1;"></div>
                                <span class="status-text small-text">Số lượng khớp (Màu xanh lá nhạt)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

    @include('check_warehouse.modal')
@endsection

@section('scripts')
    <script>
        var products = @json($equipmentsWithStock);
    </script>

    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'F2') {
                event.preventDefault();
                document.getElementById('searchProductInput').focus();
            }

            if (event.altKey && event.key.toLowerCase() === 'a') {
                event.preventDefault();
                addAllProducts();
            }

            if (event.altKey && event.key === 'f') {
                event.preventDefault();
                autoFillAllQuantities();
                checkInputs();
            }
        });

        document.querySelectorAll('.pointer').forEach(item => {
            item.addEventListener('click', function() {
                this.focus();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <script src="{{ asset('js/check_warehouse/check_warehouse.js') }}"></script>
@endsection

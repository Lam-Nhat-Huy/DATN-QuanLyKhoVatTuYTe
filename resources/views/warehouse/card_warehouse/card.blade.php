@extends('master_layout.layout')

@section('styles')
    <style>
        .hover-table {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .hover-table:hover {
            background-color: #e9ecef;
        }

        .collapse {
            display: none;
        }

        .collapse.show {
            display: table-row;
        }

        .collapse-content {
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .detail-box {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f7f7f7;
            border-radius: 5px;
        }

        .detail-box h5 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 5px;
        }

        .detail-box p {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 8px;
        }

        .btn-close-detail {
            margin-top: 10px;
            background-color: #dc3545;
            color: white;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
        }

        .btn-close-detail:hover {
            background-color: #c82333;
        }

        .card-header {
            border-bottom: 1px solid #dee2e6;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .form-control {
            border-radius: 0.375rem;
        }

        .form-control-sm {
            height: 2rem;
            padding: 0.25rem 0.5rem;
        }

        .table-search {
            width: 100%;
            margin-top: 15px;
        }

        .table-search input {
            width: 100%;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        .table-search input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Thiết Bị</span>
            </h3>
        </div>

        <div class="card-body py-1">
            <form action="" method="GET" class="row align-items-center d-flex justify-content-between">
                <div class="col-4 pe-8">
                    <select name="equipment_id"
                        class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success setupSelect2">
                        <option value="">Chọn thiết bị</option>
                        <option value="1" {{ request()->equipment_id == 1 ? 'selected' : '' }}>Thiết bị A</option>
                        <option value="2" {{ request()->equipment_id == 2 ? 'selected' : '' }}>Thiết bị B</option>
                        <option value="3" {{ request()->equipment_id == 3 ? 'selected' : '' }}>Thiết bị C</option>
                        <option value="4" {{ request()->equipment_id == 4 ? 'selected' : '' }}>Thiết bị D</option>
                    </select>
                </div>


                <div class="col-4">
                    <div class="row align-items-center">
                        <div class="col-5 pe-0">
                            <input type="date" name="start_date"
                                class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success"
                                value="{{ request()->start_date ?? \Carbon\Carbon::now()->subMonths(3)->format('Y-m-d') }}">
                        </div>
                        <div class="col-2 text-center">
                            Đến
                        </div>
                        <div class="col-5 ps-0">
                            <input type="date" name="end_date"
                                class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success"
                                value="{{ request()->end_date ?? \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body py-3">
            @if ($items->isNotEmpty())
                <div class="table-responsive rounded">
                    <table class="table table-striped align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder bg-success text-white">
                                <th class="ps-3">STT</th>
                                <th>Số lô</th>
                                <th>Tồn đầu kỳ</th>
                                <th>Nhập</th>
                                <th>Xuất</th>
                                <th>Tồn cuối kỳ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center hover-table" data-bs-toggle="collapse"
                                data-bs-target="#collapseDetails1">
                                <td>1</td>
                                <td>001</td>
                                <td>100</td>
                                <td>20</td>
                                <td>10</td>
                                <td>110</td>
                            </tr>
                            <tr class="collapse" id="collapseDetails1">
                                <td colspan="6" class="collapse-content">
                                    <div class="row">
                                        <div class="col-md-4 detail-box">
                                            <h5>Thông tin cơ bản</h5>
                                            <p><strong>Số lô:</strong> 001</p>
                                            <p><strong>Ngày sản xuất:</strong> 2024-01-01</p>
                                            <p><strong>Hạn sử dụng:</strong> 2025-01-01</p>
                                        </div>
                                        <div class="col-md-4 detail-box">
                                            <h5>Trạng thái lô hàng</h5>
                                            <p><strong>Tồn đầu kỳ:</strong> 100</p>
                                            <p><strong>Nhập:</strong> 20</p>
                                            <p><strong>Xuất:</strong> 10</p>
                                            <p><strong>Tồn cuối kỳ:</strong> 110</p>
                                        </div>
                                        <div class="col-md-4 detail-box">
                                            <h5>Thông tin bổ sung</h5>
                                            <p><strong>Nhà cung cấp:</strong> Công ty ABC</p>
                                            <p><strong>Địa chỉ kho:</strong> 123 Đường XYZ</p>
                                            <p><strong>Ghi chú:</strong> Ghi chú về thiết bị.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            @else
                <div class="col-md-12">
                    <p class="text-danger text-center">Không có dữ liệu để hiển thị, vui lòng chọn thông tin để thống kê</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleElements = document.querySelectorAll('tr[data-bs-toggle="collapse"]');

            toggleElements.forEach(function(element) {
                element.addEventListener('click', function(event) {
                    // Prevent Bootstrap's default collapse behavior
                    event.preventDefault();

                    var target = document.querySelector(this.getAttribute('data-bs-target'));

                    // Toggle the 'show' class on the target element
                    target.classList.toggle('show');
                });
            });
        });
    </script>
@endsection

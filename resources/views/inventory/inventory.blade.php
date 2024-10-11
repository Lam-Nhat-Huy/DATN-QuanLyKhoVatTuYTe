@extends('master_layout.layout')

@section('styles')
    <style>
        .hover-table:hover {
            background: #ccc;
        }

        .selected-row {
            background: #ccc;
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
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Tồn Kho</span>
            </h3>
        </div>

        @include('inventory.filter')

        <div class="card-body py-3">
            <div class="table-responsive rounded">
                <table class="table table-hover table-bordered align-middle text-center"
                    style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr class="fw-bolder bg-success text-white" style="background-color: #28a745;">
                            <th class="ps-4" style="width: 5%;">
                                <input type="checkbox" id="selectAll" />
                            </th>
                            <th class="ps-4">Tên sản phẩm</th>
                            <th>Nhóm sản phẩm</th>
                            <th>Tổng tồn</th>
                            <th class="pe-3">Đơn vị tính</th>
                        </tr>
                    </thead>
                    <tbody id="equipment-list">
                        @include('inventory.index')
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-body py-3 mb-3 d-flex justify-between flex-row-reverse">
            <div class="action-bar">
                {{ $equipments->links('pagination::bootstrap-4') }}
            </div>

            <div class="filter-bar">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <p class="nav-link text-dark">Tất cả <span class="badge bg-info">({{ $totalEquipments }})</span>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/warehouse/export.js') }}"></script>
    <script>
        $(document).ready(function() {
            let timer;

            $('#search').on('keyup', function() {
                clearTimeout(timer);
                let query = $(this).val();

                timer = setTimeout(function() {
                    let startDate = $('input[name="start_date"]').val();
                    let endDate = $('input[name="end_date"]').val();
                    let equipmentGroup = $('select[name="category"]').val();
                    let expiryStatus = $('select[name="expiry_date"]').val();
                    let quantityStatus = $('select[name="quantity"]').val();
                    if (!query && !startDate && !endDate && !equipmentGroup && !expiryStatus && !
                        quantityStatus) {
                        $.ajax({
                            url: "{{ route('inventory.index') }}",
                            type: "GET",
                            success: function(data) {
                                $('tbody').html(data);
                            }
                        });
                    } else {
                        $.ajax({
                            url: "{{ route('inventory.filter') }}",
                            type: "GET",
                            data: {
                                'search': query,
                                'start_date': startDate,
                                'end_date': endDate,
                                'category': equipmentGroup,
                                'expiry_date': expiryStatus,
                                'quantity': quantityStatus
                            },
                            success: function(data) {
                                $('tbody').html(data);
                            }
                        });
                    }
                }, 300);
            });

            $('input[name="start_date"], input[name="end_date"], select[name="category"], select[name="expiry_date"], select[name="quantity"]')
                .on('change', function() {
                    clearTimeout(timer);
                    let query = $('#search').val();

                    timer = setTimeout(function() {
                        let startDate = $('input[name="start_date"]').val();
                        let endDate = $('input[name="end_date"]').val();
                        let equipmentGroup = $('select[name="category"]').val();
                        let expiryStatus = $('select[name="expiry_date"]').val();
                        let quantityStatus = $('select[name="quantity"]').val();

                        if (!query && !startDate && !endDate && !equipmentGroup && !expiryStatus && !
                            quantityStatus) {
                            $.ajax({
                                url: "{{ route('inventory.index') }}",
                                type: "GET",
                                success: function(data) {
                                    $('tbody').html(data);
                                }
                            });
                        } else {
                            $.ajax({
                                url: "{{ route('inventory.filter') }}",
                                type: "GET",
                                data: {
                                    'search': query,
                                    'start_date': startDate,
                                    'end_date': endDate,
                                    'category': equipmentGroup,
                                    'expiry_date': expiryStatus,
                                    'quantity': quantityStatus
                                },
                                success: function(data) {
                                    $('tbody').html(data);
                                }
                            });
                        }
                    }, 300);
                });
        });
    </script>
@endsection

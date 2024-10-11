@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('check_warehouse.index') }}" class="btn btn-sm btn-dark me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive rounded">
                <table class="table table-striped align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bolder bg-success">
                            <th class="ps-3">Mã Phiếu Kiểm</th>
                            <th>Người Kiểm</th>
                            <th>Ngày Kiểm</th>
                            <th>Ghi Chú</th>
                            <th class="pe-3">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 5; $i++)
                            <tr class="text-center">
                                <td>#KK001</td>
                                <td>Phạm Anh Hoài</td>
                                <td>{{ now()->format('m-d-Y') }}</td>
                                <td>Phạm Anh Hoài</td>
                                <td>
                                    <button class="btn btn-sm btn-success mb-1 mt-1" data-bs-toggle="modal"
                                        data-bs-target="#restoreModal"><i class="fa fa-rotate-left"></i>Khôi Phục</button>

                                    <div class="modal fade" id="restoreModal" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="restoreModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="restoreModalLabel">Khôi Phục Phiếu Kiểm Kho
                                                    </h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="">
                                                        @csrf
                                                        <h4 class="text-success">Khôi Phục Phiếu Kiểm Kho Này?</h4>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-sm btn-twitter">Khôi Phục</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-sm btn-danger mb-1 mt-1" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal_"><i class="fa fa-trash"></i>Xóa Vĩnh Viễn</button>

                                    <div class="modal fade" id="deleteModal_" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="deleteModalLabel">Xóa Vĩnh Viễn Phiếu Kiểm Kho
                                                    </h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="">
                                                        @csrf
                                                        <h4 class="text-danger">Xóa Vĩnh Viễn Phiếu Kiểm Kho Này?</h4>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-sm btn-danger">Xóa</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

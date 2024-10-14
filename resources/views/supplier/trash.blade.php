@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('supplier.list') }}" class="btn rounded-pill btn-sm btn-dark me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <form action="{{ route('supplier.trash') }}" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $allSupplierTrash->count() == 0 ? 'd-none' : '' }}">
                            <tr class="fw-bolder bg-success">
                                <th class="ps-4"><input type="checkbox" id="selectAll" /></th>
                                <th style="width: 30% !important;">Tên Nhà cung cấp</th>
                                <th style="width: 15% !important;">Số điện thoại</th>
                                <th style="width: 25% !important;">Email</th>
                                <th style="width: 20% !important;">Người liên hệ</th>
                                <th style="width: 10% !important;" class="pe-3">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allSupplierTrash as $item)
                                <tr class="text-center hover-table pointer">
                                    <td class="text-xl-start"><input type="checkbox" class="row-checkbox"
                                            name="supplier_codes[]" value="{{ $item->code }}" /></td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">
                                        {{ $item->name }}
                                    </td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">{{ $item->phone }}
                                    </td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">{{ $item->email }}
                                    </td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">
                                        {{ $item->contact_name }}</td>
                                    <td class="text-center" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $item['code'] }}" id="toggleIcon{{ $item['code'] }}">
                                        <i class="fa fa-caret-right pointer">
                                        </i>
                                    </td>
                                </tr>

                                {{-- Khôi phục --}}
                                <div class="modal fade" id="restoreModal_{{ $item->code }}" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title text-white" id="deleteModalLabel">Khôi Phục Nhà cung
                                                    cấp
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('supplier.trash') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="supplier_code_restore"
                                                    value="{{ $item->code }}">
                                                <div class="modal-body pb-0 text-center">
                                                    <p class="text-primary mb-4">Khôi Phục Nhà cung cấp Này?</p>
                                                </div>
                                                <div class="modal-footer justify-content-center border-0">
                                                    <button type="button"
                                                        class="btn rounded-pill btn-sm btn-secondary px-4"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit"
                                                        class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Khôi
                                                        Phục</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Xóa --}}
                                <div class="modal fade" id="deleteModal_{{ $item->code }}" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Vĩnh Viễn Nhà
                                                    Cung Cấp
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('supplier.trash') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="supplier_code_delete"
                                                    value="{{ $item->code }}">
                                                <div class="modal-body pb-0 text-center">
                                                    <p class="text-danger mb-4">Xóa Vĩnh Viễn Nhà Cung Cấp Này?</p>
                                                </div>
                                                <div class="modal-footer justify-content-center border-0">
                                                    <button type="button"
                                                        class="btn rounded-pill btn-sm btn-secondary px-4"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit"
                                                        class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa
                                                        Vĩnh Viễn</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Collapse content -->
                                <tr class="collapse multi-collapse" id="collapse{{ $item['code'] }}">
                                    <td class="p-0" colspan="12">
                                        <div class="flex-lg-row-fluid border-2">
                                            <div class="card card-flush p-2"
                                                style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                                <div class="card-header d-flex justify-content-between align-items-center p-2"
                                                    style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                    <div class="row px-5">
                                                        <div class="col-12 mt-3">
                                                            <h4 class="fw-bold mt-3">Chi tiết</h4>
                                                        </div>

                                                        <!-- Left column: Supplier Info -->
                                                        <div class="col-7">
                                                            <table class="table table-borderless">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="fw-semibold">Nhà cung cấp:</td>
                                                                        <td class="text-dark">{{ $item->name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-semibold">Địa chỉ:</td>
                                                                        <td class="text-dark">{{ $item->address }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-semibold">Mã số thuế:</td>
                                                                        <td class="text-dark">{{ $item->tax_code }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <!-- Right column: Additional Supplier Info -->
                                                        <div class="col-5">
                                                            <table class="table table-borderless">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="fw-semibold">Đại diện:</td>
                                                                        <td class="text-dark">
                                                                            {{ $item->contact_name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-semibold">Email:</td>
                                                                        <td class="text-dark">{{ $item->email }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fw-semibold">Số điện thoại:</td>
                                                                        <td class="text-dark">{{ $item->phone }}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body py-3 border-top-0 border-2 text-end">
                                            <div class="button-group">
                                                <!-- Khôi phục -->
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#restoreModal_{{ $item->code }}"
                                                    class="btn rounded-pill btn-sm btn-twitter me-2 printPdfBtn"
                                                    type="button"><i class="fa fa-rotate-right me-1">
                                                    </i>Khôi Phục
                                                </a>
                                                <!-- Xóa vĩnh viễn -->
                                                <button class="btn rounded-pill btn-sm btn-danger me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal_{{ $item['code'] }}" type="button">
                                                    <i class="fa fa-trash"></i>Xóa Vĩnh Viễn
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataAlert">
                                    <td colspan="12" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fa-regular fa-trash-can"
                                                    style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Thùng Rác
                                                    Rỗng</h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($allSupplierTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fas fa-rotate-right me-2 text-primary"></i>Khôi Phục
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa Vĩnh Viễn
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $allSupplierTrash->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Khôi Phục Tất Cả --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllLabel">Xác Nhận khôi phục Nhà cung cấp</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục Nhà cung cấp đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-twitter px-4 load_animation">Khôi
                                Phục</button>
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
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Nhà cung cấp</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả Nhà cung cấp đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa Vĩnh
                                Viễn</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

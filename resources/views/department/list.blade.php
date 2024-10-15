@extends('master_layout.layout')

@section('styles')
@endsection

@section('scripts')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Phòng Ban</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('department.trash') }}?{{ request()->getQueryString() }}"
                    class="btn rounded-pill btn-sm btn-danger me-lg-3">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng rác
                    </span>
                </a>
                <a href="{{ route('department.add') }}?{{ request()->getQueryString() }}"
                    class="btn rounded-pill btn-sm btn-twitter">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-plus me-1"></i>
                        Thêm Phòng Ban
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body py-1">
            <form action="" method="">
                <div class="row align-items-center">
                    <div class="col-9">
                        <input type="search" name="kw" placeholder="Tìm kiếm phòng ban.."
                            class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success w-100"
                            value="{{ request()->kw }}">
                    </div>
                    <div class="col-3 d-flex justify-content-between">
                        <a class="btn rounded-pill btn-info btn-sm mt-2 mb-2 w-100 me-2"
                            href="{{ route('department.index') }}"><i class="fas fa-times-circle"
                                style="margin-bottom: 2px;"></i>Bỏ Lọc</a>
                        <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 w-100 load_animation" type="submit"><i
                                class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm</button>
                    </div>
                </div>
            </form>
        </div>
        <form action="{{ route('department.index') }} " method="POST">
            @csrf
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $department->count() == 0 ? 'd-none' : '' }}">
                            <tr class="fw-bolder bg-success">
                                <th class="ps-3"><input type="checkbox" id="selectAll" /></th>
                                <th style="width: 25%;">Phòng Ban</th>
                                <th style="width: 30%;">Mô tả</th>
                                <th style="width: 25%;">Vị trí</th>
                                <th style="width: 20%;" class="pe-3 text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($department as $item)
                                <tr class="hover-table pointer">
                                    <td class="text-xl-start">
                                        <input type="checkbox" class="row-checkbox" name="department_codes[]"
                                            value="{{ $item->code }}" />
                                    </td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">
                                        {{ $item->name }}
                                    </td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">
                                        {{ $item->description ?? 'Không Có' }}
                                    </td>
                                    <td class="text-xl-start text-truncate" style="max-width: 150px;">
                                        {{ $item->location }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('department.edit', $item->code) }}?{{ request()->getQueryString() }}"
                                                class="btn btn-sm btn-info me-2 rounded-pill">
                                                <i class="fa fa-edit" style="margin-bottom: 2px;"></i> Sửa
                                            </a>
                                            <button class="btn rounded-pill btn-sm btn-danger me-2" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal_{{ $item['code'] }}" type="button">
                                                <i class="fa fa-trash"></i>Xóa
                                            </button>
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
                                                <i class="fas fa-building" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center mt-1">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có
                                                    phòng ban </h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($department->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger"></i>Xóa</a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $department->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Xóa Tất Cả --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Tất Cả người dùng</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa tất cả người dùng đã chọn?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @foreach ($department as $item)
        {{-- Xóa --}}
        <div class="modal fade" id="deleteModal_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModalLabel">Xóa Phòng Ban
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('department.index') }}" method="POST">
                        @csrf
                        <input type="hidden" name="department_code_delete" value="{{ $item->code }}">
                        <div class="modal-body pb-0 text-center">
                            <p class="text-danger mb-4">Xóa Phòng Ban Này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <button type="button" class="btn rounded-pill btn-sm btn-secondary px-4"
                                data-bs-dismiss="modal">Đóng</button>
                            <button type="submit"
                                class="btn rounded-pill btn-sm btn-danger px-4 load_animation">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

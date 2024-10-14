@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5 d-flex justify-content-between">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Thùng Rác</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipments.equipments_group') }}" class="btn btn-sm btn-dark rounded-pill">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>

        {{-- Table content --}}
        <form action="{{ route('equipments.equipments_group_trash') }} " method="POST">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="">
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $AllEquipmentGroupTrash->count() == 0 ? 'd-none' : '' }}">
                            <tr class="fw-bolder bg-success">
                                <th class="ps-3" style="width: 5%;"><input type="checkbox" id="selectAll" /></th>
                                <th class="" style="width: 10%;">Mã</th>
                                <th class="" style="width: 20%;">Tên</th>
                                <th class="" style="width: 25%;">Mô Tả</th>
                                <th class="text-center" style="width: 10%;">Trạng Thái</th>
                                <th class="text-center" style="width: 30%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($AllEquipmentGroupTrash->isEmpty())
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
                            @else
                                @foreach ($AllEquipmentGroupTrash as $item)
                                    <tr class="hover-table pointer">
                                        <td class="text-xl-start">
                                            <input type="checkbox" class="row-checkbox" name="equipment_group_codes[]"
                                                value="{{ $item->code }}" />
                                        </td>
                                        <td>#{{ $item->code }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->description ?? 'Không có mô tả' }}</td>
                                        <td class="text-center">
                                            @if ($item->status)
                                                <span class="bg-success text-white rounded-pill"
                                                    style="padding: 5px 5px; display: inline-block; min-width: 80px; font-size: 10px;">Hoạt
                                                    động</span>
                                            @else
                                                <span class="bg-danger text-white rounded-pill"
                                                    style="padding: 5px 5px; display: inline-block; min-width: 80px; font-size: 10px;">Không
                                                    hoạt động</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-twitter rounded-pill me-2"
                                                    data-bs-toggle="modal" data-bs-target="#restore_{{ $item->code }}">
                                                    <i class="fa fa-rotate-right" style="margin-bottom: 2px;"></i> Khôi Phục
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                    data-bs-toggle="modal" data-bs-target="#delete_{{ $item->code }}">
                                                    <i class="fa fa-trash" style="margin-bottom: 2px;"></i> Xóa Vĩnh Viễn
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($AllEquipmentGroupTrash->count() > 0)
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div class="dropdown d-none" id="action_delete_all">
                        <span class="btn rounded-pill btn-info btn-sm dropdown-toggle" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span>Chọn Thao Tác</span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#restoreAll">
                                    <i class="fas fa-rotate-right me-2 text-primary" style="margin-bottom: 2px;"></i>Khôi
                                    Phục
                                </a>
                            </li>
                            <li><a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#deleteAll">
                                    <i class="fas fa-trash me-2 text-danger" style="margin-bottom: 2px;"></i>Xóa Vĩnh Viễn
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="DayNganCach"></div>
                    <ul class="pagination">
                        {{ $AllEquipmentGroupTrash->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Xóa Tất Cả --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Vĩnh Viễn Nhóm Thiết Bị
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa vĩnh viễn nhóm thiết bị đã chọn?</p>
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

            {{-- Modal Khôi Phục All --}}
            <div class="modal fade" id="restoreAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="restoreAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreAllLabel">Xác Nhận Khôi Phục Nhóm Thiết Bị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-primary mb-4">Bạn có chắc chắn muốn khôi phục nhóm thiết bị đã chọn?</p>
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
        </form>

        {{-- Modal khôi phục --}}
        @foreach ($AllEquipmentGroupTrash as $item)
            <div class="modal fade" id="restore_{{ $item->code }}" tabindex="-1" aria-labelledby="restoreModalOne"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title text-white" id="restoreModalOne">Xác Nhận Khôi
                                Phục Nhóm Thiết Bị
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center pb-0">
                            <p class="text-primary">Bạn có chắc chắn muốn khôi phục nhóm thiết bị này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <form action="{{ route('equipments.equipments_group_trash') }}" method="POST">
                                @csrf
                                <input type="hidden" name="restore_value" value="{{ $item->code }}">
                                <button type="button" class="btn btn-sm btn-secondary me-1 rounded-pill"
                                    data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-sm btn-twitter rounded-pill load_animation">Khôi
                                    Phục</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- modal xóa --}}
            <div class="modal fade" id="delete_{{ $item->code }}" tabindex="-1" aria-labelledby="deleteModalOne"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteModalOne">Xác Nhận Xóa Vĩnh Viễn Nhóm Thiết Bị
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center pb-0">
                            <p class="text-danger">Bạn có chắc chắn muốn xóa vĩnh viễn nhóm thiết bị này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <form action="{{ route('equipments.equipments_group_trash') }}" method="POST">
                                @csrf
                                <input type="hidden" name="delete_value" value="{{ $item->code }}">
                                <button type="button" class="btn btn-sm btn-secondary me-1 rounded-pill"
                                    data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill load_animation">Xóa Vĩnh
                                    Viễn</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
@endsection

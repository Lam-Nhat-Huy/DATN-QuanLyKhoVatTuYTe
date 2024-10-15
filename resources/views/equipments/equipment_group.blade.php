@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        {{-- Header with add unit button --}}
        <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Nhóm Thiết Bị</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('equipments.equipments_group_trash') }}" class="btn btn-sm btn-danger me-2 rounded-pill">
                    <i class="fa fa-trash me-1"></i> Thùng Rác
                </a>
                <a href="{{ route('equipments.add_equipments_group') }}" class="btn btn-sm btn-success rounded-pill">
                    <i class="fa fa-plus"></i> Thêm Nhóm Thiết Bị
                </a>
            </div>
        </div>

        <div class="card-body py-1">
            <form action="" class="row align-items-center g-3">
                <div class="col-auto flex-grow-1">
                    <input type="search" id="kw" name="kw" value="{{ request()->kw }}"
                        placeholder="Tìm Kiếm Mã, Tên Nhóm Thiết Bị.."
                        class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success w-100">
                </div>
                <div class="col-md-3">
                    <select name="stt" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" selected>--Theo Trạng Thái--</option>
                        <option value="1" {{ request()->stt == '1' ? 'selected' : '' }}>
                            Hoạt Động
                        </option>
                        <option value="0" {{ request()->stt == '0' ? 'selected' : '' }}>
                            Không Hoạt Động
                        </option>
                    </select>
                </div>
                <div class="col-auto">
                    <a href="{{ route('equipments.equipments_group') }}" class="btn rounded-pill btn-info btn-sm mt-2 mb-2">
                        <i class="fas fa-times-circle" style="margin-bottom: 2px;"></i>Bỏ Lọc
                    </a>
                </div>
                <div class="col-auto">
                    <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 load_animation" type="submit">
                        <i class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm
                    </button>
                </div>
            </form>
        </div>

        {{-- Table content --}}
        <form action="{{ route('equipments.equipments_group') }} " method="POST">
            @csrf
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="{{ $AllEquipmentGroup->count() == 0 ? 'd-none' : '' }}">
                            <tr class="bg-success">
                                <th class="ps-3" style="width: 5%;"><input type="checkbox" id="selectAll" /></th>
                                <th class="" style="width: 10%;">Mã</th>
                                <th class="" style="width: 25%;">Tên</th>
                                <th class="" style="width: 25%;">Mô Tả</th>
                                <th class="text-center" style="width: 15%;">Trạng Thái</th>
                                <th class="text-center" style="width: 20%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($AllEquipmentGroup->isEmpty())
                                <tr id="noDataAlert">
                                    <td colspan="12" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fas fa-box" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center mt-1">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có dữ
                                                    liệu về nhóm thiết bị </h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($AllEquipmentGroup as $item)
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
                                                <div class="rounded-pill px-2 py-1 text-white bg-success">
                                                    Hoạt động
                                                </div>
                                            @else
                                                <div class="rounded-pill px-2 py-1 text-white bg-danger">
                                                    Không hoạt động
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('equipments.update_equipments_group', $item->code) }}"
                                                    class="btn btn-sm btn-info me-2 rounded-pill">
                                                    <i class="fa fa-edit"></i> Sửa
                                                </a>

                                                @php
                                                    $linkedEquipments = \App\Models\Equipments::where(
                                                        'equipment_type_code',
                                                        $item->code,
                                                    )->count();
                                                @endphp

                                                @if ($linkedEquipments == 0)
                                                    <!-- Chỉ hiển thị nút Xóa nếu không có liên kết -->
                                                    <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteConfirmModal{{ $item->code }}">
                                                        <i class="fa fa-trash"></i> Xóa
                                                    </button>
                                                @else
                                                    <!-- Hiển thị nút không thể xóa nếu có liên kết -->
                                                    <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                                                        disabled
                                                        title="Nhóm thiết bị này đang liên kết với thiết bị, không thể xóa.">
                                                        <i class="fa fa-lock"></i> Xóa
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($AllEquipmentGroup->count() > 0)
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
                        {{ $AllEquipmentGroup->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Xóa Tất Cả --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Tất Cả Nhóm Thiết Bị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa nhóm thiết bị đã chọn?</p>
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

        {{-- Modal for delete confirmation --}}
        @foreach ($AllEquipmentGroup as $item)
            <div class="modal fade" id="deleteConfirmModal{{ $item->code }}" tabindex="-1"
                aria-labelledby="deleteConfirmLabel{{ $item->code }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteConfirmLabel{{ $item->code }}">Xác Nhận Xóa
                                Nhóm
                                Thiết Bị
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center pb-0">
                            <p class="text-danger">Bạn có chắc chắn muốn xóa nhóm thiết bị này?</p>
                        </div>
                        <div class="modal-footer justify-content-center border-0">
                            <form action="{{ route('equipments.delete_equipments_group', $item->code) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-secondary me-1 rounded-pill"
                                    data-bs-dismiss="modal">Hủy</button>
                                <button type="submit"
                                    class="btn btn-sm btn-danger rounded-pill load_animation">Xóa</button>
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

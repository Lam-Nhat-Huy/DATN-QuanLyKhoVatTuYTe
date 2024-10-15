@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8 shadow">
        <div class="card-header border-0 pt-5 d-flex justify-content-between align-items-center">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Đơn Vị</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('units.unit_trash') }}" class="btn btn-sm btn-danger rounded-pill me-2">
                    <i class="fa fa-trash" style="margin-bottom: 2px;"></i> Thùng Rác
                </a>
                <a href="{{ route('units.create') }}" class="btn btn-sm btn-twitter rounded-pill">
                    <i class="fa fa-plus" style="margin-bottom: 2px;"></i> Thêm Đơn Vị
                </a>
            </div>
        </div>

        <div class="card-body py-1">
            <form action="" class="row align-items-center g-3">
                <div class="col-auto flex-grow-1">
                    <input type="search" id="kw" name="kw" value="{{ request()->kw }}"
                        placeholder="Tìm Kiếm Mã, Tên Đơn Vị.."
                        class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success w-100">
                </div>
                <div class="col-md-3">
                    <select name="us" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" selected>--Theo Người Tạo--</option>
                        @foreach ($allUser as $item)
                            <option value="{{ $item->code }}" {{ request()->us == $item->code ? 'selected' : '' }}>
                                {{ $item->last_name . ' ' . $item->first_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <a href="{{ route('units.index') }}" class="btn rounded-pill btn-info btn-sm mt-2 mb-2">
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

        <form action="{{ route('units.index') }}" method="POST">
            @csrf
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="{{ $allUnits->count() == 0 ? 'd-none' : '' }}">
                            <tr class="bg-success">
                                <th class="ps-3" style="width: 5%;"><input type="checkbox" id="selectAll" /></th>
                                <th class="" style="width: 10%;">Mã</th>
                                <th style="width: 10%;">Tên</th>
                                <th style="width: 30%;">Mô Tả</th>
                                <th style="width: 15%;">Người tạo</th>
                                <th style="width: 10%;">Ngày tạo</th>
                                <th class="pe-3 text-center" style="width: 20%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody id="unitTableBody">
                            @forelse ($allUnits as $unit)
                                <tr class="hover-table pointer">
                                    <td class="text-xl-start">
                                        <input type="checkbox" class="row-checkbox" name="unit_codes[]"
                                            value="{{ $unit->code }}" />
                                    </td>
                                    <td>#{{ $unit->code }}</td>
                                    <td>{{ $unit->name }}</td>
                                    <td>{{ $unit->description ?? 'Không có mô tả' }}</td>
                                    <td>{{ $unit->users->last_name . ' ' . $unit->users->first_name }}</td>
                                    <td>{{ $unit->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('units.edit', $unit->code) }}"
                                                class="btn btn-sm btn-info me-2 rounded-pill">
                                                <i class="fa fa-edit" style="margin-bottom: 2px;"></i> Sửa
                                            </a>

                                            @php
                                                $linkedUnit = \App\Models\Equipments::where(
                                                    'unit_code',
                                                    $unit->code,
                                                )->count();
                                            @endphp

                                            @if ($linkedUnit == 0)
                                                <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteConfirmModal{{ $unit->code }}">
                                                    <i class="fa fa-trash" style="margin-bottom: 2px;"></i> Xóa
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                                                    disabled title="Đơn vị này đang liên kết với thiết bị, không thể xóa.">
                                                    <i class="fa fa-lock" style="margin-bottom: 2px;"></i> Xóa
                                                </button>
                                            @endif
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
                                                <i class="fas fa-medkit" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center mt-1">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có dữ
                                                    liệu về đơn vị </h5>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($allUnits->count() > 0)
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
                        {{ $allUnits->links('pagination::bootstrap-5') }}
                    </ul>
                </div>
            @endif

            {{-- Modal Xác Nhận Xóa --}}
            <div class="modal fade" id="deleteAll" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="deleteAllLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="deleteAllLabel">Xác Nhận Xóa Đơn Vị</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center" style="padding-bottom: 0px;">
                            <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa đơn vị đã chọn?</p>
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

    @foreach ($allUnits as $item)
        <div class="modal fade" id="deleteConfirmModal{{ $item->code }}" tabindex="-1" aria-labelledby="deleteModal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white" id="deleteModal">Xác Nhận Xóa
                            Đơn Vị</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pb-0">
                        <p class="text-danger">Bạn có chắc chắn muốn xóa đơn vị này?</p>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <form action="{{ route('units.destroy', $item->code) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill"
                                data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill load_animation">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
@endsection

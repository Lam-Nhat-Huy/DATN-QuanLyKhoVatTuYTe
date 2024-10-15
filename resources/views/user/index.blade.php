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
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Người Dùng</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('user.user_trash') }}?{{ request()->getQueryString() }}"
                    class="btn rounded-pill btn-sm btn-danger me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng Rác
                    </span>
                </a>
                <a href="{{ route('user.add') }}?{{ request()->getQueryString() }}"
                    class="btn rounded-pill btn-sm btn-twitter">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-plus me-1"></i>
                        Thêm Người Dùng
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body py-1">
            <form action="{{ route('user.index') }}" method="GET" class="row align-items-center">
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <select name="gd" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" {{ request()->gd == '' ? 'selected' : '' }}>--Theo Giới Tính--</option>
                        <option value="nam" {{ request()->gd == 'nam' ? 'selected' : '' }}>Nam</option>
                        <option value="nữ" {{ request()->gd == 'nữ' ? 'selected' : '' }}>Nữ</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <select name="ps" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" {{ request()->ps == '' ? 'selected' : '' }}>--Theo Chức Vụ--</option>
                        <option value="1" {{ request()->ps == '1' ? 'selected' : '' }}>Admin</option>
                        <option value="0" {{ request()->ps == '0' ? 'selected' : '' }}>Nhân Viên</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <select name="st" class="mt-2 mb-2 form-select form-select-sm rounded-pill setupSelect2">
                        <option value="" {{ request()->st == '' ? 'selected' : '' }}>--Theo Trạng Thái--</option>
                        <option value="0" {{ request()->st == '0' ? 'selected' : '' }}>Không</option>
                        <option value="1" {{ request()->st == '1' ? 'selected' : '' }}>Có</option>
                    </select>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="row align-items-center">
                        <div class="col-7">
                            <input type="search" name="kw" placeholder="Tìm Kiếm Mã, Tên, Email Người Dùng.."
                                class="mt-2 mb-2 form-control form-control-sm rounded-pill border border-success w-100"
                                value="{{ request()->kw }}">
                        </div>
                        <div class="col-5 d-flex justify-content-between">
                            <a class="btn rounded-pill btn-info btn-sm mt-2 mb-2 w-100 me-2"
                                href="{{ route('user.index') }}"><i class="fas fa-times-circle"
                                    style="margin-bottom: 2px;"></i>Bỏ Lọc</a>
                            <button class="btn rounded-pill btn-dark btn-sm mt-2 mb-2 w-100 load_animation"
                                type="submit"><i class="fa fa-search" style="margin-bottom: 2px;"></i>Tìm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <form action="{{ route('user.index') }}" method="POST">
            @csrf
            <div class="card-body py-3">
                <div class="table-responsive rounded">
                    <table class="table align-middle gs-0 gy-4">
                        <thead class="{{ $allUser->count() == 0 ? 'd-none' : '' }}">
                            <tr class="fw-bolder bg-success">
                                <th class="ps-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="" style="width: 7% !important;">Mã</th>
                                <th class="" style="width: 10% !important;">Ảnh</th>
                                <th class="" style="width: 15% !important;">Họ Tên</th>
                                <th class="" style="width: 25% !important;">Email</th>
                                <th class="" style="width: 12% !important;">Số Điện Thoại</th>
                                <th class="" style="width: 8% !important;">Giới Tính</th>
                                <th class="" style="width: 12% !important;">Trạng Thái</th>
                                <th class="pe-3 text-center" style="width: 20% !important;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allUser as $item)
                                <tr class="hover-table pointer">
                                    <td>
                                        <input type="checkbox" name="user_codes[]" value="{{ $item->code }}"
                                            class="row-checkbox" />
                                    </td>
                                    <td>
                                        #{{ $item->code }}
                                    </td>
                                    <td>
                                        <img class="rounded-circle border border-dark"
                                            src="{{ $item->avatar ? asset('storage/' . $item->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                            style="width: 70px !important; height: 70px !important;" alt="">
                                    </td>
                                    <td>
                                        {{ $item->last_name . ' ' . $item->first_name }}
                                    </td>
                                    <td>
                                        {{ $item->email }}
                                    </td>
                                    <td>
                                        {{ $item->phone }}
                                    </td>
                                    <td>
                                        {{ $item->gender }}
                                    </td>
                                    <td>
                                        @if ($item['status'] == 0)
                                            <span class="text-danger">Không hoạt động</span>
                                        @elseif($item['status'] == 1)
                                            <span class="text-success">Đang hoạt động</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $item->code }}" aria-expanded="false"
                                        aria-controls="collapse_{{ $item->code }}">
                                        Chi Tiết<i class="fa fa-caret-right pointer ms-2"></i>
                                    </td>
                                </tr>

                                <!-- Collapse content -->
                                <tr>
                                    <td class="p-0" colspan="12">
                                        <div class="collapse multi-collapse" id="collapse_{{ $item->code }}">
                                            <div class="flex-lg-row-fluid border-2">
                                                <div class="card card-flush p-2"
                                                    style="padding-top: 0px !important; padding-bottom: 0px !important;">
                                                    <div class="card-header justify-content-center p-2"
                                                        style="padding-top: 0 !important; padding-bottom: 0px !important;">
                                                        <div class="row px-5 w-100">
                                                            <div class="col-md-12 my-3">
                                                                <h4 class="fw-bold mt-3">Thông Tin Chi Tiết</h4>
                                                            </div>

                                                            <div class="row mb-5 justify-content-center">
                                                                <div class="col-md-3">
                                                                    <img src="{{ $item->avatar ? asset('storage/' . $item->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                                                        class="rounded border border-dark"
                                                                        style="width: 175px !important; height: 175px !important;"
                                                                        alt="">
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <table class="table table-borderless">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Họ Và Tên:
                                                                                        </td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->last_name . ' ' . $item->first_name }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Email:</td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->email }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Phone:</td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->phone }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Địa Chỉ:
                                                                                        </td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->address }}
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <table class="table table-borderless">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Năm Sinh:
                                                                                        </td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->birth_day }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Giới Tính:
                                                                                        </td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->gender }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Vai Trò:
                                                                                        </td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->position }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fw-semibold">Ngày Tạo
                                                                                            Tài
                                                                                            Khoản:</td>
                                                                                        <td class="text-dark">
                                                                                            {{ $item->created_at->format('d-m-Y') }}
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body py-3 border-top-0 border-2 text-end">
                                                <div class="button-group">
                                                    <a href="{{ route('user.edit', $item->code) }}?{{ request()->getQueryString() }}"
                                                        class="btn btn-sm btn-info me-2 rounded-pill"><i
                                                            class="fa fa-edit me-1"></i>Sửa</a></li>

                                                    <button type="button" class="btn btn-sm btn-danger rounded-pill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal_{{ $item->code }}">
                                                        <i class="fa fa-trash" style="margin-bottom: 2px;"></i> Xóa
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataAlert">
                                    <td colspan="10" class="text-center">
                                        <div class="alert alert-secondary d-flex flex-column align-items-center justify-content-center p-4"
                                            role="alert"
                                            style="border: 2px dashed #6c757d; background-color: #f8f9fa; color: #495057;">
                                            <div class="mb-3">
                                                <i class="fas fa-search" style="font-size: 36px; color: #6c757d;"></i>
                                            </div>
                                            <div class="text-center">
                                                <h5 style="font-size: 16px; font-weight: 600; color: #495057;">Không có kết
                                                    quả tìm kiếm</h5>
                                                <p style="font-size: 14px; color: #6c757d; margin: 0;">
                                                    Không tìm thấy kết quả phù hợp với yêu cầu tìm kiếm của bạn. Vui lòng
                                                    thử lại với từ khóa khác.
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($allUser->count() > 0)
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
                        {{ $allUser->links('pagination::bootstrap-5') }}
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

    @foreach ($allUser as $item)
        {{-- Xóa --}}
        <div class="modal fade" id="deleteModal_{{ $item->code }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteLabel">Xác Nhận Xóa người dùng</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center" style="padding-bottom: 0px;">
                        <p class="text-danger mb-4">Bạn có chắc chắn muốn xóa người dùng đã chọn?</p>
                    </div>
                    <form action="{{ route('user.index') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_code_delete" value="{{ $item->code }}">
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

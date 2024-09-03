@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
@endsection

@section('content')
    <div class="card mb-5 pb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Danh Sách Người Dùng</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('user.user_trash') }}" class="btn btn-sm btn-danger me-2">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-trash me-1"></i>
                        Thùng Rác
                    </span>
                </a>
                <a href="{{ route('user.add') }}" class="btn btn-sm btn-twitter">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-plus me-1"></i>
                        Thêm Người Dùng
                    </span>
                </a>
            </div>
        </div>
        <div class="card-body py-1 me-6">
            <form action="" class="row align-items-center">
                <div class="col-4">
                    <select name="ur" class="mt-2 mb-2 form-select form-select-sm form-select-solid setupSelect2">
                        <option value="" selected>--Theo Vai Trò--</option>
                        <option value="">Admin</option>
                        <option value="">Kho</option>
                        <option value="">Kế Toán</option>
                        <option value="">Mua Hàng</option>
                    </select>
                </div>
                <div class="col-4">
                    <select name="stt" class="mt-2 mb-2 form-select form-select-sm form-select-solid setupSelect2">
                        <option value="" selected>--Theo Trạng Thái--</option>
                        <option value="1" {{ request()->stt == 1 ? 'selected' : '' }}>Không</option>
                        <option value="2" {{ request()->stt == 2 ? 'selected' : '' }}>Có</option>
                    </select>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div class="col-10">
                            <input type="search" name="kw" placeholder="Tìm Kiếm Mã, Tên, Email Người Dùng.."
                                class="mt-2 mb-2 form-control form-control-sm form-control-solid border border-success"
                                value="{{ request()->kw }}">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-dark btn-sm mt-2 mb-2" type="submit">Tìm</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-striped align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bolder bg-success">
                            <th class="ps-4">Mã Người Dùng</th>
                            <th class="">Tên</th>
                            <th class="">Email</th>
                            <th class="">Số Điện Thoại</th>
                            <th class="" style="width: 120px !important;">Vai Trò</th>
                            <th class="" style="width: 120px !important;">Trạng Thái</th>
                            <th class="pe-3">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i <= 3; $i++)
                            <tr class="text-center">
                                <td>
                                    #ND007
                                </td>
                                <td>
                                    Lữ Phát Huy
                                </td>
                                <td>
                                    lphdev04@gmail.com
                                </td>
                                <td>
                                    0945567048
                                </td>
                                <td>
                                    @if ($i == 0)
                                        <div class="rounded px-2 py-1 text-white bg-danger" title="">Admin</div>
                                    @elseif ($i == 1)
                                        <div class="rounded px-2 py-1 text-white bg-dark" title="">Kho</div>
                                    @elseif ($i == 2)
                                        <div class="rounded px-2 py-1 text-white bg-info" title="">Kế Toán</div>
                                    @else
                                        <div class="rounded px-2 py-1 text-white bg-primary" title="">Mua Hàng</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($i > 3)
                                        <div class="rounded px-2 py-1 text-white bg-danger">Không</div>
                                    @else
                                        <div class="rounded px-2 py-1 text-white bg-success">Có</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" data-bs-toggle="dropdown">
                                            <i class="fa fa-ellipsis-h me-2"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('user.edit') }}">
                                                    <i class="fa fa-edit me-1"></i>Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item pointer" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal_{{ $i }}">
                                                    <i class="fa fa-trash me-1"></i>Xóa
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Xóa --}}
                                    <div class="modal fade" id="deleteModal_{{ $i }}" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="deleteModalLabel">Xóa Người Dùng
                                                    </h3>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="">
                                                        @csrf
                                                        <h4 class="text-danger">Xóa Người Dùng Này?</h4>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="button" class="btn btn-sm btn-twitter">Xóa</button>
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
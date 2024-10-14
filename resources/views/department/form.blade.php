@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
@endsection

@php
    if ($config == 'create') {
        $config = route('department.create');
        $button_text = 'Thêm';
        $hidden = '';
        $required = 'required';
    } else {
        $config = route('department.update');
        $button_text = 'Cập nhật';
        $hidden = 'd-none';
        $required = '';
    }
@endphp

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">{{ $title_form }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('department.index') }}?{{ request()->getQueryString() }}"
                    class="btn rounded-pill btn-sm btn-dark">
                    <span class="align-items-center d-flex" style="font-size: 10px;">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <form class="form" method="post" action="{{ $config }}" enctype="multipart/form-data">
            @csrf
            <div class="py-5 px-lg-17">

                <div class="me-n7 pe-7">

                    <div class="row mb-5">
                        <div class="mb-5 col-6">

                            <label class="required fs-6 fw-bold mb-3">Tên Phòng Ban</label>

                            <input type="text"
                                class="form-control form-control-sm border border-success rounded-pill"
                                placeholder="Tên phòng ban.." name="name"
                                value="{{ !empty($firstDepartment->name) ? $firstDepartment->name : old('name') }}" />
                            @error('name')
                                <div class="message_error">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="mb-5 col-6">

                            <label class="required fs-6 fw-bold mb-3">Vị trí</label>

                            <input type="text"
                                class="form-control form-control-sm border border-success rounded-pill"
                                placeholder="Vị trí phòng ban.." name="location"
                                value="{{ !empty($firstDepartment->location) ? $firstDepartment->location : old('location') }}" />
                            @error('location')
                                <div class="message_error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 col-12">
                            <label class="required fs-6 fw-bold mb-3">Mô tả</label>
                        
                            <textarea class="form-control form-control-sm border border-success"
                                placeholder="Mô tả phòng ban.." name="description"
                                cols="" rows="5">{{ !empty($firstDepartment->description) ? $firstDepartment->description : old('description') }}</textarea>
                        
                            @error('description')
                                <div class="message_error">{{ $message }}</div>
                            @enderror
                        </div>                        
                    </div>
                </div>
                <div class="modal-footer pe-0">
                    <button type="submit" class="btn rounded-pill btn-twitter btn-sm load_animation">
                        {{ $button_text }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

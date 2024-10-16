@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
    <script>
        ClassicEditor
            .create(document.querySelector('#content'))
            .catch(error => {
                console.error(error);
            });

        document.getElementById('notification_type').addEventListener('change', function(event) {
            const selectedValue = event.target.value;

            if (selectedValue == 1) {
                document.getElementById('cb1-6').checked = true;
            } else {
                document.getElementById('cb1-6').checked = false;
            }
        });
    </script>
@endsection

@php
    if ($action == 'create') {
        $action = route('notification.notification_create');

        $button_text = 'Thêm';

        $required = 'required';
    } else {
        $action = route('notification.notification_update', request('code'));

        $button_text = 'Cập Nhật';

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
                <a href="{{ route('notification.index') }}" class="btn rounded-pill btn-sm btn-dark">
                    <span class="align-items-center d-flex">
                        <i class="fa fa-arrow-left me-1"></i>
                        Trở Lại
                    </span>
                </a>
            </div>
        </div>
        <form class="form" action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="py-5 px-lg-17">

                <div class="me-n7 pe-7">

                    <div class="mb-5">

                        <label class="fs-5 fw-bold mb-3">Chọn Loại Thông Báo</label>

                        <div class="d-flex align-items-center">

                            <select name="notification_type" id="notification_type"
                                class="form-select form-select-sm rounded-pill border-success ps-4">
                                <option value="0"
                                    {{ (!empty($firstNotification) && !empty($firstNotification->notification_type == 0)) || old('notification_type') == 0 ? 'selected' : '' }}>
                                    Mặc Định</option>
                                <option value="1"
                                    {{ (!empty($firstNotification) && !empty($firstNotification->notification_type == 1)) || old('notification_type') == 1 ? 'selected' : '' }}>
                                    Kiểm Kho - Khóa Kho</option>
                            </select>
                        </div>

                        @error('notification_type')
                            <div class="message_error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-5">

                        <label class="{{ $required }} fs-5 fw-bold mb-3">Nội Dung Thông Báo</label>

                        <textarea name="content" id="content">
                            {{ !empty($firstNotification) && !empty($firstNotification->content) ? $firstNotification->content : old('content') }}
                        </textarea>

                        @error('content')
                            <div class="message_error">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="action d-flex">
                        <div class="form-group me-20">
                            <label class="fs-5 fw-bold mb-2">Thông Báo Quan Trọng</label>
                            <div class="checkbox-wrapper-6">
                                <input class="tgl tgl-light" id="cb1-6" type="checkbox" value="1" name="important"
                                    {{ (isset($firstNotification) && $firstNotification->important == 1) || old('important') == 1 ? 'checked' : '' }} />
                                <label class="tgl-btn" for="cb1-6"></label>
                            </div>
                        </div>

                        <div class="form-group me-20">
                            <label class="fs-5 fw-bold mb-2">Trạng thái</label>
                            <div class="checkbox-wrapper-6">
                                <input class="tgl tgl-light" id="cb1-7" type="checkbox" value="1" name="status"
                                    {{ (isset($firstNotification) && $firstNotification->status == 1) || old('status') == 1 ? 'checked' : '' }} />
                                <label class="tgl-btn" for="cb1-7"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal-footer flex-right">
                <button type="submit" class="btn rounded-pill btn-twitter btn-sm load_animation">
                    {{ $button_text }}
                </button>
            </div>
        </form>
    </div>
@endsection

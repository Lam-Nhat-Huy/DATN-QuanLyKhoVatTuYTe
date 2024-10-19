@extends('master_layout.layout')

@section('styles')
    <style>
        .form-control-sm {
            font-size: 12px !important;
        }

        .image-overlay {
            position: relative;
        }

        .image-overlay img {
            width: 150px;
            height: 150px;
            transition: filter 0.3s;
        }

        .image-overlay .darken {
            position: absolute;
            border-radius: 50rem !important;
            top: 0;
            left: 0;
            width: 150px;
            height: 150px;
            background-color: rgba(44, 44, 44, 0.5);
            object-fit: cover
        }

        .darken.show {
            display: block;
            /* Hiển thị khi có lớp show */
        }

        .change-avatar {
            position: absolute;
            bottom: 7.5%;
            right: 40%;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s;
            z-index: 10;
            /* Để đảm bảo biểu tượng nằm trên cùng */
        }

        .change-avatar i {
            font-size: 1.5em;
        }
    </style>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editFormButton = document.getElementById('edit_form');
            const cancelEditFormButton = document.getElementById('cancel_edit_form');
            const spanShowElements = document.querySelectorAll('.span-show');
            const inputEditElements = document.querySelectorAll('.input-edit');
            const darkenElement = document.querySelector('.darken'); // Lớp tối
            const avatarInput = document.getElementById('avatar-input'); // Input file
            const imgElement = document.querySelector('.image-overlay img'); // Phần tử img
            const message_error = document.querySelectorAll('.message_error');
            const defaultAvatar =
                "{{ !empty($getUserProfile->avatar) ? asset('storage/' . $getUserProfile->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"; // Avatar mặc định

            // Hàm chuyển đổi hiển thị từ span sang input
            function switchToEditMode() {
                spanShowElements.forEach(element => element.classList.add('d-none'));
                inputEditElements.forEach(element => element.classList.remove('d-none'));

                editFormButton.classList.add('d-none');
                cancelEditFormButton.classList.remove('d-none');

                // Hiển thị lớp tối
                darkenElement.classList.remove('d-none');
            }

            // Hàm chuyển đổi từ input về span
            function switchToViewMode() {
                spanShowElements.forEach(element => element.classList.remove('d-none'));
                inputEditElements.forEach(element => element.classList.add('d-none'));

                editFormButton.classList.remove('d-none');
                cancelEditFormButton.classList.add('d-none');

                // Ẩn lớp tối
                darkenElement.classList.add('d-none');
                // Reset ảnh về mặc định khi quay lại
                imgElement.src = defaultAvatar;
                avatarInput.value = ""; // Xóa giá trị của input file
                message_error.forEach(function(element) {
                    element.innerText = "";
                });
            }

            // Sự kiện khi bấm vào nút "Chỉnh sửa"
            editFormButton.addEventListener('click', switchToEditMode);

            // Sự kiện khi bấm vào nút "Trở lại"
            cancelEditFormButton.addEventListener('click', switchToViewMode);

            // Sự kiện khi nhấn vào biểu tượng camera để mở input file
            const changeAvatarIcon = document.getElementById('change-avatar-icon');
            changeAvatarIcon.addEventListener('click', function() {
                avatarInput.click(); // Kích hoạt input file
            });

            // Sự kiện khi người dùng chọn tệp ảnh
            avatarInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgElement.src = e.target.result; // Cập nhật ảnh mới
                    // Hiển thị lớp tối khi có ảnh được chọn
                    darkenElement.classList.add('d-none');
                };
                reader.readAsDataURL(file); // Đọc tệp dưới dạng URL
            });

            // Bắt lỗi
            const form = document.getElementById('profile-form');

            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission first

                let isValid = true; // Flag to check if the form is valid

                // Clear previous error messages
                const errorElements = form.querySelectorAll('.message_error');
                errorElements.forEach(element => element.textContent = '');

                const inputs = form.querySelectorAll(
                    'input[type="text"], input[type="date"]'); // Select text and date inputs
                const errorMessages = { // Object to hold error messages for each field
                    first_name: '',
                    last_name: '',
                    address: '',
                    birth_day: '',
                };

                inputs.forEach(input => {
                    // Trim the input value and check if it's empty
                    if (input.value.trim() === '') {
                        isValid = false; // Set the flag to false
                        errorMessages[input.name] =
                            `${input.placeholder} không được để trống.`; // Push error message to the object
                    }
                });

                // Display error messages below the respective input fields
                for (const [field, message] of Object.entries(errorMessages)) {
                    if (message) {
                        const errorElement = document.getElementById(`${field}_error`);
                        if (errorElement) {
                            errorElement.textContent = message; // Set the error message
                        }
                    }
                }

                // Check if form is valid
                if (isValid) {
                    document.getElementById('loading').style.display = 'block';
                    document.getElementById('loading-overlay').style.display = 'block';

                    const submitButton = form.querySelector('button[type="submit"]');
                    submitButton.disabled = true;

                    form.submit();
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="card mb-md-5 pb-15 mb-xl-10" id="kt_profile_details_view">
        <div class="px-10 py-5 border-bottom d-flex justify-content-between align-items-center cursor-pointer">
            <h4 class="fw-bolder m-0">Thông Tin Cá Nhân</h4>
            <span class="btn rounded-pill btn-dark btn-sm pointer" id="edit_form">
                <i class="fas fa-edit mb-md-1"></i> <span>Chỉnh sửa</span>
            </span>
            <span class="btn rounded-pill btn-secondary btn-sm pointer d-none" id="cancel_edit_form">
                <i class="fas fa-arrow-left mb-md-1"></i> <span>Trở lại</span>
            </span>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" id="profile-form" enctype="multipart/form-data">
            @csrf
            <div class="row p-10">
                <div class="col-md-2 col-sm-12">
                    <div class="mt-md-7 ms-md-7 position-relative image-overlay">
                        <img class="border border-dark rounded-pill shadow mt-5 mt-md-0"
                            src="{{ !empty($getUserProfile->avatar) ? asset('storage/' . $getUserProfile->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                            alt="Image">

                        <div class="darken rounded-3 d-none"></div>
                        <div class="change-avatar d-none input-edit">
                            <input type="file" name="avatar" accept="image/*" style="display: none;" id="avatar-input">
                            <i class="fas fa-camera-rotate text-white" id="change-avatar-icon"></i>
                        </div>
                    </div>
                    <div class="ms-md-7 text-center mt-3 d-none input-edit">
                        <strong class="text-danger">Ảnh Phải Là NxN Pixel</strong>
                    </div>
                </div>
                <div class="col-10">
                    <div class="card-body p-9 ps-15">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <label class="col-md-2 col-sm-12 fw-bold text-muted">Họ Và Tên</label>
                                    <div class="col-10">
                                        <span
                                            class="fw-bolder fs-6 text-gray-800 span-show">{{ !empty($getUserProfile->last_name) && !empty($getUserProfile->first_name) ? $getUserProfile->last_name . ' ' . $getUserProfile->first_name : 'N/A' }}</span>

                                        <div class="row">
                                            <div class="col-6">
                                                <input type="text"
                                                    class="form-control form-control-sm rounded-pill border border-success d-none input-edit"
                                                    placeholder="Họ người dùng" name="last_name"
                                                    value="{{ old('last_name', $getUserProfile->last_name) }}" />

                                                <div class="message_error" id="last_name_error"></div>
                                            </div>

                                            <div class="col-6">
                                                <input type="text"
                                                    class="form-control form-control-sm rounded-pill border border-success d-none input-edit"
                                                    placeholder="Tên người dùng" name="first_name"
                                                    value="{{ old('first_name', $getUserProfile->first_name) }}" />

                                                <div class="message_error" id="first_name_error"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row mb-6">
                                    <label class="col-md-2 col-sm-12 fw-bold text-muted">Vai Trò</label>
                                    <div class="col-10 fv-row">
                                        <span
                                            class="fw-bolder text-gray-800 fs-6 span-show">{{ !empty($getUserProfile->isAdmin) == 1 ? 'Admin' : 'Nhân Viên' }}
                                        </span>

                                        <input type="text"
                                            class="form-control form-control-sm rounded-pill border border-success bg-secondary d-none input-edit"
                                            value="{{ !empty($getUserProfile->isAdmin) == 1 ? 'Admin' : 'Nhân Viên' }}"
                                            disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row mb-6">
                                    <label class="col-md-2 col-sm-12 fw-bold text-muted">Email</label>
                                    <div class="col-10">
                                        <span
                                            class="fw-bolder text-gray-800 fs-6 span-show text-lowercase">{{ !empty($getUserProfile->email) ? $getUserProfile->email : 'N/A' }}
                                        </span>

                                        <input type="text"
                                            class="form-control form-control-sm rounded-pill border border-success bg-secondary d-none input-edit"
                                            style="text-transform: lowercase !important;"
                                            value="{{ $getUserProfile->email }}" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row mb-6">
                                    <label class="col-md-2 col-sm-12 fw-bold text-muted">Số Điện Thoại
                                    </label>
                                    <div class="col-10">
                                        <span
                                            class="fw-bolder text-gray-800 fs-6 span-show">{{ !empty($getUserProfile->phone) ? $getUserProfile->phone : 'N/A' }}
                                        </span>

                                        <input type="text"
                                            class="form-control form-control-sm rounded-pill border border-success bg-secondary d-none input-edit"
                                            value="{{ $getUserProfile->phone }}" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <label class="col-md-2 col-sm-12 fw-bold text-muted">Ngày Sinh</label>
                                    <div class="col-10">
                                        <span class="fw-bolder fs-6 text-gray-800 span-show">
                                            {{ !empty($getUserProfile->birth_day)
                                                ? \Carbon\Carbon::parse($getUserProfile->birth_day)->format('d-m-Y')
                                                : 'N/A' }}
                                        </span>

                                        <input type="date"
                                            class="form-control form-control-sm rounded-pill border border-success d-none input-edit"
                                            name="birth_day" placeholder="Ngày sinh"
                                            value="{{ old('birth_day', $getUserProfile->birth_day) }}" />

                                        <div class="message_error" id="birth_day_error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <label class="col-md-2 col-sm-12 fw-bold text-muted">Địa Chỉ</label>
                                    <div class="col-10">
                                        <span
                                            class="fw-bolder fs-6 text-gray-800 span-show">{{ !empty($getUserProfile->address) ? $getUserProfile->address : 'N/A' }}</span>

                                        <input type="text"
                                            class="form-control form-control-sm rounded-pill border border-success d-none input-edit"
                                            placeholder="Địa chỉ" name="address"
                                            value="{{ old('address', $getUserProfile->address) }}" />

                                        <div class="message_error" id="address_error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-none input-edit mt-3">
                                <div class="text-end">
                                    <button type="submit" class="btn rounded-pill btn-dark btn-sm">Cập Nhật</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

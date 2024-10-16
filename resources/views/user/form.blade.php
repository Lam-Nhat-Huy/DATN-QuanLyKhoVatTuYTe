@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@php
    if ($action == 'create') {
        $action = route('user.create');

        $button_text = 'Thêm';

        $hidden = '';

        $required = 'required';
    } else {
        $action = route('user.update');

        $button_text = 'Cập Nhật';

        $hidden = 'd-none';

        $required = '';
    }
@endphp

@section('content')
    <form class="form" method="post" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        <div class="card mb-5 mb-xl-8 pb-5">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">{{ $title_form }}</span>
                </h3>
                <div class="card-toolbar">
                    <button type="button" id="random-btn" class="btn rounded-pill btn-sm btn-info me-2 {{ $hidden }}">
                        <span class="align-items-center d-flex">
                            <i class="fa fa-random me-1"></i>
                            Dữ Liệu Mẫu
                        </span>
                    </button>
                    <a href="{{ route('user.index') }}?{{ request()->getQueryString() }}"
                        class="btn rounded-pill btn-sm btn-dark">
                        <span class="align-items-center d-flex">
                            <i class="fa fa-arrow-left me-1"></i>
                            Trở Lại
                        </span>
                    </a>
                </div>
            </div>
            <div class="py-5 px-lg-17">

                <div class="me-n7 pe-7">

                    <div class="row mb-5">

                        <div class="col-2">
                            <div class="mb-5">
                                <img id="preview-avatar" class="border border-dark rounded-circle"
                                    src="{{ !empty($firstUser->avatar) ? asset('storage/' . $firstUser->avatar) : 'https://static-00.iconduck.com/assets.00/avatar-default-symbolic-icon-2048x1949-pq9uiebg.png' }}"
                                    style="width: 155px !important; height: 155px !important;" alt="image">
                            </div>

                            <div class="fv-row mb-5 text-center">

                                <label for="avatar-input" class="pointer"><i class="fa fa-upload"></i> Tải Ảnh Lên</label>

                                <input type="file"
                                    class="form-control form-control-sm rounded-pill border border-success" name="avatar"
                                    accept="image/*" id="avatar-input" style="display: none;">

                                @error('avatar')
                                    <div class="message_error">{{ $message }}</div>
                                @enderror

                            </div>
                        </div>

                        <div class="col-10 ps-5">
                            <div class="row">
                                <div class="col-6 fv-row mb-5">

                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Họ</label>

                                    <input type="text"
                                        class="form-control form-control-sm rounded-pill border border-success"
                                        placeholder="Họ Người Dùng.." name="last_name"
                                        value="{{ !empty($firstUser->last_name) ? $firstUser->last_name : old('last_name') }}" />
                                    @error('last_name')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror

                                </div>

                                <div class="col-6 fv-row mb-5">

                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Tên</label>

                                    <input type="text"
                                        class="form-control form-control-sm rounded-pill border border-success"
                                        placeholder="Tên Người Dùng.." name="first_name"
                                        value="{{ !empty($firstUser->first_name) ? $firstUser->first_name : old('first_name') }}" />
                                    @error('first_name')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror

                                </div>

                                <div class="col-6 fv-row mb-5">

                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Email</label>

                                    <input type="text"
                                        class="form-control form-control-sm rounded-pill border border-success lowercase-text"
                                        placeholder="Email người dùng.." name="email"
                                        value="{{ !empty($firstUser->email) ? $firstUser->email : old('email') }}" />

                                    @error('email')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror

                                </div>

                                <div class="col-6 fv-row mb-5">

                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Số Điện Thoại</label>

                                    <input type="text"
                                        class="form-control form-control-sm rounded-pill border border-success"
                                        placeholder="Số Điện Thoại.." name="phone"
                                        value="{{ !empty($firstUser->phone) ? $firstUser->phone : old('phone') }}" />
                                    @error('phone')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror

                                </div>

                                <div class="col-6 fv-row mb-5">

                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Mật Khẩu</label>

                                    <input type="password"
                                        class="form-control form-control-sm rounded-pill border border-success"
                                        placeholder="Mật Khẩu.." name="password" />
                                    @error('password')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror

                                </div>

                                <div class="col-6 fv-row mb-5">

                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Năm Sinh</label>

                                    <input type="date"
                                        class="form-control form-control-sm rounded-pill border border-success"
                                        name="birth_day"
                                        value="{{ !empty($firstUser->birth_day) ? $firstUser->birth_day : old('birth_day') }}" />

                                    @error('birth_day')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror

                                </div>

                                <div class="col-12 fv-row mb-5">
                                    <label class="{{ $required }} fs-5 fw-bold mb-2">Địa Chỉ</label>

                                    <textarea type="text" class="form-control form-control-sm border border-success" placeholder="Địa Chỉ.."
                                        name="address" rows="5">{{ !empty($firstUser->address) ? $firstUser->address : old('address') }}</textarea>
                                    @error('address')
                                        <div class="message_error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-4 fv-row mb-5">

                                            <label class="{{ $required }} fs-5 fw-bold mb-1">Giới Tính</label>

                                            <div>
                                                <label for="male" class="me-2" style="font-size: 15px;">
                                                    <input type="radio" id="male" name="gender" value="Nam"
                                                        {{ (!empty($firstUser->gender) && $firstUser->gender == 'Nam') || old('gender') == 'Nam' ? 'checked' : '' }}>
                                                    Nam
                                                </label>
                                                <label for="female" style="font-size: 15px;">
                                                    <input type="radio" id="female" name="gender" value="Nữ"
                                                        {{ (!empty($firstUser->gender) && $firstUser->gender == 'Nữ') || old('gender') == 'Nữ' ? 'checked' : '' }}>
                                                    Nữ
                                                </label>
                                            </div>

                                            @error('gender')
                                                <div class="message_error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-4 fv-row mb-5">
                                            <label class="fs-5 fw-bold mb-2">Tài Khoản Admin</label>
                                            <div class="checkbox-wrapper-6">
                                                <input class="tgl tgl-light" id="cb1-7" type="checkbox"
                                                    value="1" name="isAdmin"
                                                    {{ (!empty($firstUser->isAdmin) && $firstUser->isAdmin == 1) || old('isAdmin') == 1 ? 'checked' : '' }} />
                                                <label class="tgl-btn" for="cb1-7"></label>
                                            </div>
                                        </div>

                                        <div class="col-4 fv-row mb-5">
                                            <label class="fs-5 fw-bold mb-2">Trạng Thái Tài Khoản</label>

                                            <div class="checkbox-wrapper-6">
                                                <input class="tgl tgl-light" id="cb1-6" type="checkbox"
                                                    value="1" name="status"
                                                    {{ (!empty($firstUser->status) && $firstUser->status == 1) || old('status') == 1 ? 'checked' : '' }} />
                                                <label class="tgl-btn" for="cb1-6"></label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer flex-right pe-0">
                        <button type="submit" class="btn rounded-pill btn-twitter btn-sm load_animation">
                            {{ $button_text }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        function removeDiacritics(str) {
            const diacriticsMap = {
                'a': 'a',
                'à': 'a',
                'á': 'a',
                'ả': 'a',
                'ã': 'a',
                'ạ': 'a',
                'â': 'a',
                'ă': 'a',
                'ầ': 'a',
                'ấ': 'a',
                'ẩ': 'a',
                'ẫ': 'a',
                'ậ': 'a',
                'ä': 'a',
                'å': 'a',
                'e': 'e',
                'è': 'e',
                'é': 'e',
                'ẻ': 'e',
                'ẽ': 'e',
                'ẹ': 'e',
                'ê': 'e',
                'ế': 'e',
                'ề': 'e',
                'ể': 'e',
                'ễ': 'e',
                'ệ': 'e',
                'i': 'i',
                'ì': 'i',
                'í': 'i',
                'ỉ': 'i',
                'ĩ': 'i',
                'ị': 'i',
                'o': 'o',
                'ò': 'o',
                'ó': 'o',
                'ỏ': 'o',
                'õ': 'o',
                'ọ': 'o',
                'ô': 'o',
                'ồ': 'o',
                'ố': 'o',
                'ổ': 'o',
                'ỗ': 'o',
                'ộ': 'o',
                'ơ': 'o',
                'ờ': 'o',
                'ớ': 'o',
                'ở': 'o',
                'ỡ': 'o',
                'ợ': 'o',
                'u': 'u',
                'ù': 'u',
                'ú': 'u',
                'ủ': 'u',
                'ũ': 'u',
                'ụ': 'u',
                'ư': 'u',
                'ừ': 'u',
                'ứ': 'u',
                'ử': 'u',
                'ữ': 'u',
                'ự': 'u',
                'y': 'y',
                'ỳ': 'y',
                'ý': 'y',
                'ỷ': 'y',
                'ỹ': 'y',
                'ỵ': 'y',
                'A': 'A',
                'À': 'A',
                'Á': 'A',
                'Ả': 'A',
                'Ã': 'A',
                'Ạ': 'A',
                'Â': 'A',
                'Ầ': 'A',
                'Ấ': 'A',
                'Ẩ': 'A',
                'Ẫ': 'A',
                'Ậ': 'A',
                'Ä': 'A',
                'Å': 'A',
                'E': 'E',
                'È': 'E',
                'É': 'E',
                'Ẻ': 'E',
                'Ẽ': 'E',
                'Ẹ': 'E',
                'Ê': 'E',
                'Ế': 'E',
                'Ề': 'E',
                'Ể': 'E',
                'Ễ': 'E',
                'Ệ': 'E',
                'I': 'I',
                'Ì': 'I',
                'Í': 'I',
                'Ỉ': 'I',
                'Ĩ': 'I',
                'Ị': 'I',
                'O': 'O',
                'Ò': 'O',
                'Ó': 'O',
                'Ỏ': 'O',
                'Õ': 'O',
                'Ọ': 'O',
                'Ô': 'O',
                'Ồ': 'O',
                'Ố': 'O',
                'Ổ': 'O',
                'Ỗ': 'O',
                'Ộ': 'O',
                'Ơ': 'O',
                'Ờ': 'O',
                'Ớ': 'O',
                'Ở': 'O',
                'Ỡ': 'O',
                'Ợ': 'O',
                'U': 'U',
                'Ù': 'U',
                'Ú': 'U',
                'Ủ': 'U',
                'Ũ': 'U',
                'Ụ': 'U',
                'Ư': 'U',
                'Ừ': 'U',
                'Ứ': 'U',
                'Ử': 'U',
                'Ữ': 'U',
                'Ự': 'U',
                'Y': 'Y',
                'ỳ': 'Y',
                'Ý': 'Y',
                'Ỷ': 'Y',
                'Ỹ': 'Y',
                'Ỵ': 'Y'
            };

            return str.split('').map(char => diacriticsMap[char] || char).join('');
        }

        document.getElementById('random-btn').addEventListener('click', function() {
            // Tạo dữ liệu ngẫu nhiên
            const randomLastName = 'Nguyễn';

            const randomFirstName = ['Văn A', 'Thị B', 'Thị C', 'Văn D', 'Văn E'].sort(() => Math
                .random() - 0.5)[0];

            const cleanFirstName = removeDiacritics(randomFirstName);
            const cleanLastName = removeDiacritics(randomLastName);

            const randomEmail = (cleanFirstName + cleanLastName + Math.floor(Math.random() * 100))
                .replace(/\s+/g, '').toLowerCase() + '@gmail.com';

            const randomPhone = '0' + Math.floor(Math.random() * 1000000000);

            // Tạo ngày sinh ngẫu nhiên từ 1990 đến 2005
            const randomBirthYear = Math.floor(Math.random() * (2005 - 1990 + 1)) + 1990;
            const randomBirthMonth = Math.floor(Math.random() * 12); // 0-11
            const randomBirthDay = Math.floor(Math.random() * 28) + 1; // 1-28

            // Tạo đối tượng Date cho ngày sinh
            const randomBirthDate = new Date(randomBirthYear, randomBirthMonth, randomBirthDay);

            // Định dạng ngày sinh cho input (YYYY-MM-DD)
            const formattedDate = randomBirthDate.toISOString().split('T')[0];

            // Tạo giới tính ngẫu nhiên
            const genders = ['Nam', 'Nữ'];
            const randomGender = genders[Math.floor(Math.random() * genders.length)];

            // Điền vào form
            document.querySelector('input[name="first_name"]').value = randomFirstName;
            document.querySelector('input[name="last_name"]').value = randomLastName;
            document.querySelector('input[name="email"]').value = randomEmail;
            document.querySelector('input[name="phone"]').value = randomPhone;
            document.querySelector('input[name="password"]').value = randomPhone;
            document.querySelector('input[name="birth_day"]').value = formattedDate;
            document.querySelector('textarea[name="address"]').value = 'Cần Thơ';
            document.querySelector('textarea[name="address"]').value = 'Cần Thơ';

            // Gán giới tính ngẫu nhiên vào radio button
            document.querySelector(`input[name="gender"][value="${randomGender}"]`).checked = true;
        });

        // document.getElementById('cb1-7').addEventListener('change', function() {
        //     const chucVuInput = document.getElementById('chucVuInput');
        //     if (this.checked) {
        //         chucVuInput.disabled = true;
        //         chucVuInput.value = 'Admin';
        //     } else {
        //         chucVuInput.disabled = false;
        //         chucVuInput.value = '';
        //     }
        // });

        // // Khi load trang, kiểm tra checkbox đã checked chưa
        // window.onload = function() {
        //     const checkbox = document.getElementById('cb1-7');
        //     const chucVuInput = document.getElementById('chucVuInput');
        //     if (checkbox.checked) {
        //         chucVuInput.disabled = true;
        //         chucVuInput.value = 'Admin';
        //     }
        // };

        document.getElementById('avatar-input').addEventListener('change', function(event) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function(e) {
                // Hiển thị ảnh trong thẻ img
                document.getElementById('preview-avatar').src = e.target.result;
            }

            // Nếu có file được chọn, đọc file
            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endsection

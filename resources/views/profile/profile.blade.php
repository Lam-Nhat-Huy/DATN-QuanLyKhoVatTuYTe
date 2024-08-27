@extends('master_layout.layout')

@section('styles')
@endsection

@section('title')
    {{ $title }}
@endsection

@section('scripts')
@endsection

@section('content')
    <div>
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">

                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">

                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <img src="https://1.bp.blogspot.com/-CsImmR4DBCI/Xh_fvrHfMrI/AAAAAAAAU2U/OSVSFbuvLDoAKadvyAkRhl4Y2aDGjzqIgCLcBGAsYHQ/s1600/hinh-anh-trai-dep%253Ddau-nam-hot-boy-2k-Wap102%2B%252825%2529.jpg"
                                alt="image" />
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px">
                            </div>
                        </div>
                    </div>


                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">

                            <div class="d-flex flex-column">

                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1">Max
                                        Smith</a>
                                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z"
                                                fill="#00A3FF" />
                                            <path class="permanent"
                                                d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z"
                                                fill="white" />
                                        </svg>
                                    </span>
                                    <span href="#" class="btn btn-sm btn-light-success fw-bolder ms-2 fs-8 py-1 px-3"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_upgrade_plan">Admin</span>
                                </div>


                                <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                    <span class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">

                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z"
                                                    fill="black" />
                                                <path
                                                    d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z"
                                                    fill="black" />
                                            </svg>
                                        </span>
                                        Kiên Giang</span>
                                    <span class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">

                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z"
                                                    fill="black" />
                                                <path
                                                    d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z"
                                                    fill="black" />
                                            </svg>
                                        </span>
                                        lphdev04@gmail.com</span>
                                </div>

                            </div>


                            <div class="d-flex my-4">
                                <a href="#" class="btn btn-sm btn-dark me-2" id="kt_user_follow_button">

                                    <span class="svg-icon svg-icon-3 d-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3"
                                                d="M10 18C9.7 18 9.5 17.9 9.3 17.7L2.3 10.7C1.9 10.3 1.9 9.7 2.3 9.3C2.7 8.9 3.29999 8.9 3.69999 9.3L10.7 16.3C11.1 16.7 11.1 17.3 10.7 17.7C10.5 17.9 10.3 18 10 18Z"
                                                fill="black" />
                                            <path
                                                d="M10 18C9.7 18 9.5 17.9 9.3 17.7C8.9 17.3 8.9 16.7 9.3 16.3L20.3 5.3C20.7 4.9 21.3 4.9 21.7 5.3C22.1 5.7 22.1 6.30002 21.7 6.70002L10.7 17.7C10.5 17.9 10.3 18 10 18Z"
                                                fill="black" />
                                        </svg>
                                    </span>


                                    <span class="indicator-label">Nhắn Tin</span>
                                    {{-- <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> --}}

                                </a>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>


        <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">

            <div class="card-header cursor-pointer">

                <div class="card-title m-0">
                    <h4 class="fw-bolder m-0">Chi Tiết Hồ Sơ</h4>
                </div>


                <a href="" class="btn btn-primary btn-sm align-self-center">Chỉnh Sửa</a>

            </div>


            <div class="card-body p-9">

                <div class="row mb-7">

                    <label class="col-lg-4 fw-bold text-muted">Họ Và Tên</label>


                    <div class="col-lg-8">
                        <span class="fw-bolder fs-6 text-gray-800">Max Smith</span>
                    </div>

                </div>


                <div class="row mb-7">

                    <label class="col-lg-4 fw-bold text-muted">Vị Trí Công Việc</label>


                    <div class="col-lg-8 fv-row">
                        <span class="fw-bolder text-gray-800 fs-6">Quản Trị Viên</span>
                    </div>

                </div>


                <div class="row mb-7">

                    <label class="col-lg-4 fw-bold text-muted">Số Điện Thoại
                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                            title="Phone number must be active"></i></label>


                    <div class="col-lg-8 d-flex align-items-center">
                        <span class="fw-bolder fs-6 text-gray-800 me-2">0945567048</span>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

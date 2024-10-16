@extends('master_layout.layout')

@section('styles')
    <style>
        #notification_content p {
            font-size: 14px;
        }
    </style>
    <!-- Tải jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Tải Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    @if (!empty($getImportantNotification))
        {{-- Modal Thông Báo Quan Trọng --}}
        <div class="modal fade" id="importantNotificationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="DetailModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h3 class="modal-title text-danger" id="DetailModal">BEESOFT THÔNG BÁO</h3>
                    </div>
                    <div class="modal-body text-center" id="notification_content">
                        {!! $getImportantNotification->content !!}
                    </div>
                    <div class="modal-footer">
                        <button class="position-absolute btn btn-sm btn-warning m-0 py-2 px-3" style="left: 30px;"
                            title="Đọc Văn Bản">
                            <i id="speaker-icon" class="fa-solid fa-volume-high text-dark" onclick="speak()"
                                style="font-size: 18px;"></i>
                            <img id="speaker-gif" class="d-none" src="{{ asset('image/speaker.gif') }}"
                                style="width: 27px; height: 18px;" alt="" onclick="cancelSpeak()">
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function cancelSpeak() {
                window.speechSynthesis.cancel();
                document.getElementById('speaker-icon').classList.remove('d-none'); // Hiện icon lại
                document.getElementById('speaker-gif').classList.add('d-none'); // Ẩn gif đi
            }

            function speak() {
                window.speechSynthesis.cancel();
                var content = document.getElementById('notification_content').innerText;
                var msg = new SpeechSynthesisUtterance();
                msg.text = content;
                msg.lang = 'vi-VN'; // Chọn ngôn ngữ, ở đây là tiếng Việt
                msg.rate = 0.8; // Tốc độ đọc
                msg.pitch = 1.5; // Cao độ giọng nói

                // Khi bắt đầu nói
                msg.onstart = function() {
                    document.getElementById('speaker-icon').classList.add('d-none'); // Ẩn icon
                    document.getElementById('speaker-gif').classList.remove('d-none'); // Hiện gif
                };

                // Khi kết thúc nói
                msg.onend = function() {
                    document.getElementById('speaker-icon').classList.remove('d-none'); // Hiện icon lại
                    document.getElementById('speaker-gif').classList.add('d-none'); // Ẩn gif đi
                };

                // Bắt đầu đọc giọng nói
                window.speechSynthesis.speak(msg);
            }

            $(document).ready(function() {
                $('#importantNotificationModal').modal('show');
            });
        </script>
    @endif

    <div class="card mb-5 mb-xl-8">
        <div class="row gy-5 g-xl-8">
            <!--begin::Col-->
            <div class="col-xxl-12">
                <!--begin::Mixed Widget 2-->
                <div class="card card-xxl-stretch">
                    <!--begin::Header-->
                    <div class="card-header border-0 bg-danger py-5">
                        <h3 class="card-title fw-bolder text-white">Thông Tin Thống Kê</h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Chart-->
                        <div class="mixed-widget-2-chart card-rounded-bottom bg-danger" data-kt-color="danger"
                            style="height: 110px"></div>
                        <!--end::Chart-->
                        <!--begin::Stats-->
                        <div class="card-p mt-n20 position-relative" style="padding-bottom: 0px !important">
                            <!--begin::Row-->
                            <div class="d-flex justify-content-between flex-wrap">
                                <!--begin::Col-->
                                <div class="bg-light-warning px-6 py-8 rounded-2 me-7 mb-7 flex-fill col-md-3">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
                                    <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <rect x="8" y="9" width="3" height="10" rx="1.5" fill="black" />
                                            <rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5"
                                                fill="black" />
                                            <rect x="18" y="11" width="3" height="8" rx="1.5"
                                                fill="black" />
                                            <rect x="3" y="13" width="3" height="6" rx="1.5"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <a href="#" class="text-warning fw-bold fs-6">Đơn Hàng: <span>56</span></a>
                                </div>
                                <!--end::Col-->

                                <!--begin::Col-->
                                <div class="bg-light-danger px-6 py-8 rounded-2 me-7 mb-7 flex-fill col-md-3">
                                    <!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
                                    <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3"
                                                d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z"
                                                fill="black" />
                                            <path
                                                d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z"
                                                fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <a href="#" class="text-danger fw-bold fs-6 mt-2">Đơn Đặt Hàng: 11</a>
                                </div>

                                <!--end::Col-->
                                <div class="bg-light-success  px-6 py-8 rounded-2 me-7 mb-7 flex-fill col-md-3">
                                    <!--begin::Svg Icon | path: icons/duotune/communication/com010.svg-->
                                    <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M6 8.725C6 8.125 6.4 7.725 7 7.725H14L18 11.725V12.925L22 9.725L12.6 2.225C12.2 1.925 11.7 1.925 11.4 2.225L2 9.725L6 12.925V8.725Z"
                                                fill="black"></path>
                                            <path opacity="0.3"
                                                d="M22 9.72498V20.725C22 21.325 21.6 21.725 21 21.725H3C2.4 21.725 2 21.325 2 20.725V9.72498L11.4 17.225C11.8 17.525 12.3 17.525 12.6 17.225L22 9.72498ZM15 11.725H18L14 7.72498V10.725C14 11.325 14.4 11.725 15 11.725Z"
                                                fill="black"></path>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <a href="#" class="text-success fw-bold fs-6 mt-2">Tổng Chi: 100,000VNĐ</a>
                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Mixed Widget 2-->
            </div>
            <!--end::Col-->
            <div class="col-xxl-7">
                <!--begin::List Widget 5-->
                <div class="card card-xxl-stretch">
                    <!--begin::Header-->
                    <div class="card-header align-items-center border-0 bg-light rounded shadow-sm">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="fw-bolder mb-2 text-dark fs-4">Cảnh Báo Tồn Kho</span>
                            <span class="text-danger fw-bold fs-6">14 sản phẩm</span>
                        </h3>
                    </div>
                    <!--end::Header-->

                    <!--begin::Body-->
                    <div class="card-body pt-5">
                        <!--begin::Timeline-->
                        <div class="timeline-label">
                            <!--begin::Item-->
                            <div class="timeline-item">
                                <!--begin::Label-->
                                <div class="timeline-label fw-bolder text-gray-800 fs-6">08:42</div>
                                <!--end::Label-->
                                <!--begin::Badge-->
                                <div class="timeline-badge">
                                    <i class="fa fa-exclamation-circle text-warning fs-2"></i>
                                </div>
                                <!--end::Badge-->
                                <!--begin::Text-->
                                <div class="fw-normal timeline-content text-muted ps-3">Sản phẩm XYZ (Mã SP: 12345) đang
                                    gần hết hàng. Cần nhập thêm để tránh hết hàng.</div>
                                <!--end::Text-->
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="timeline-item">
                                <!--begin::Label-->
                                <div class="timeline-label fw-bolder text-gray-800 fs-6">09:15</div>
                                <!--end::Label-->
                                <!--begin::Badge-->
                                <div class="timeline-badge">
                                    <i class="fa fa-exclamation-circle text-warning fs-2"></i>
                                </div>
                                <!--end::Badge-->
                                <!--begin::Text-->
                                <div class="fw-normal timeline-content text-muted ps-3">Sản phẩm ABC (Mã SP: 67890) đã hết
                                    hàng trong kho.</div>
                                <!--end::Text-->
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="timeline-item">
                                <!--begin::Label-->
                                <div class="timeline-label fw-bolder text-gray-800 fs-6">10:30</div>
                                <!--end::Label-->
                                <!--begin::Badge-->
                                <div class="timeline-badge">
                                    <i class="fa fa-exclamation-circle text-warning fs-2"></i>
                                </div>
                                <!--end::Badge-->
                                <!--begin::Text-->
                                <div class="fw-normal timeline-content text-muted ps-3">Cảnh báo: Sản phẩm DEF (Mã SP:
                                    11223) chỉ còn 5 đơn vị trong kho.</div>
                                <!--end::Text-->
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="timeline-item">
                                <!--begin::Label-->
                                <div class="timeline-label fw-bolder text-gray-800 fs-6">11:45</div>
                                <!--end::Label-->
                                <!--begin::Badge-->
                                <div class="timeline-badge">
                                    <i class="fa fa-exclamation-circle text-warning fs-2"></i>
                                </div>
                                <!--end::Badge-->
                                <!--begin::Text-->
                                <div class="fw-normal timeline-content text-muted ps-3">Sản phẩm GHI (Mã SP: 44556) tồn kho
                                    đang dưới mức an toàn.</div>
                                <!--end::Text-->
                            </div>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <div class="timeline-item">
                                <!--begin::Label-->
                                <div class="timeline-label fw-bolder text-gray-800 fs-6">13:20</div>
                                <!--end::Label-->
                                <!--begin::Badge-->
                                <div class="timeline-badge">
                                    <i class="fa fa-exclamation-circle text-warning fs-2"></i>
                                </div>
                                <!--end::Badge-->
                                <!--begin::Text-->
                                <div class="fw-normal timeline-content text-muted ps-3">Thông báo: Hết hàng sản phẩm JKL
                                    (Mã SP: 78901). Hãy liên hệ nhà cung cấp.</div>
                                <!--end::Text-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Timeline-->
                    </div>
                    <!--end: Card Body-->
                </div>
                <!--end: List Widget 5-->

            </div>
            <!--end::Col-->

            <div class="col-xl-5">
                <!--begin::List Widget 3-->
                <div class="card card-xl-stretch mb-xl-8">
                    <!--begin::Header-->
                    <div class="card-header border-0">
                        <h3 class="card-title fw-bolder text-dark">Nhật ký xuất kho</h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-2">
                        <!--begin::Item-->
                        <div class="d-flex align-items-center mb-8">
                            <!--begin::Bullet-->
                            <span class="bullet bullet-vertical h-40px bg-warning"></span>
                            <!--end::Bullet-->
                            <!--begin::Checkbox-->
                            <div class="form-check form-check-custom form-check-solid mx-5">
                                <input class="form-check-input" type="checkbox" value="">
                            </div>
                            <!--end::Checkbox-->
                            <!--begin::Description-->
                            <div class="flex-grow-1">
                                <a href="#" class="text-gray-800 text-hover-primary fw-bolder fs-6">Xuất hàng theo
                                    đơn 123</a>
                                <span class="text-muted fw-bold d-block">Hạn trong 5 ngày</span>
                            </div>
                            <!--end::Description-->
                            <span class="badge badge-light-warning fs-8 fw-bolder">Mới</span>
                        </div>
                        <!--end:Item-->
                        <!--begin::Item-->
                        <div class="d-flex align-items-center mb-8">
                            <!--begin::Bullet-->
                            <span class="bullet bullet-vertical h-40px bg-primary"></span>
                            <!--end::Bullet-->
                            <!--begin::Checkbox-->
                            <div class="form-check form-check-custom form-check-solid mx-5">
                                <input class="form-check-input" type="checkbox" value="">
                            </div>
                            <!--end::Checkbox-->
                            <!--begin::Description-->
                            <div class="flex-grow-1">
                                <a href="#" class="text-gray-800 text-hover-primary fw-bolder fs-6">Xuất hàng theo
                                    đơn 456</a>
                                <span class="text-muted fw-bold d-block">Hạn trong 2 ngày</span>
                            </div>
                            <!--end::Description-->
                            <span class="badge badge-light-primary fs-8 fw-bolder">Mới</span>
                        </div>
                        <!--end:Item-->
                        <!--begin::Item-->
                        <div class="d-flex align-items-center mb-8">
                            <!--begin::Bullet-->
                            <span class="bullet bullet-vertical h-40px bg-danger"></span>
                            <!--end::Bullet-->
                            <!--begin::Checkbox-->
                            <div class="form-check form-check-custom form-check-solid mx-5">
                                <input class="form-check-input" type="checkbox" value="">
                            </div>
                            <!--end::Checkbox-->
                            <!--begin::Description-->
                            <div class="flex-grow-1">
                                <a href="#" class="text-gray-800 text-hover-primary fw-bolder fs-6">Xuất hàng theo
                                    đơn 789</a>
                                <span class="text-muted fw-bold d-block">Hạn trong 12 ngày</span>
                            </div>
                            <!--end::Description-->
                            <span class="badge badge-light-danger fs-8 fw-bolder">Mới</span>
                        </div>
                        <!--end:Item-->
                        <!--begin::Item-->
                        <div class="d-flex align-items-center">
                            <!--begin::Bullet-->
                            <span class="bullet bullet-vertical h-40px bg-success"></span>
                            <!--end::Bullet-->
                            <!--begin::Checkbox-->
                            <div class="form-check form-check-custom form-check-solid mx-5">
                                <input class="form-check-input" type="checkbox" value="">
                            </div>
                            <!--end::Checkbox-->
                            <!--begin::Description-->
                            <div class="flex-grow-1">
                                <a href="#" class="text-gray-800 text-hover-primary fw-bolder fs-6">Xuất hàng theo
                                    đơn 101112</a>
                                <span class="text-muted fw-bold d-block">Hạn trong 1 tuần</span>
                            </div>
                            <!--end::Description-->
                            <span class="badge badge-light-success fs-8 fw-bolder">Mới</span>
                        </div>
                        <!--end:Item-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end:List Widget 3-->
            </div>

        </div>

        <div class="row mb-5 mt-5 mb-xl-8" style="padding: 0 25px">
            <!--begin::Col-->
            <div class="col-xxl-6">
                <div class="card mb-5 mb-xl-8">
                    <div class="chart-container">
                        <h2>Biểu đồ tồn kho theo thời gian</h2>
                        <canvas id="inventoryChart"></canvas>
                    </div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xxl-6">
                <div class="card mb-5 mb-xl-8">
                    <div class="forecast-container">
                        <h2>Dự Báo Tồn Kho Tương Lai</h2>
                        <canvas id="forecastChart"></canvas>
                    </div>
                </div>
            </div>
            <!--end::Col-->
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inventory Chart data and configuration
            const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
            const data = {
                labels: labels,
                datasets: [{
                    label: 'Tồn Kho',
                    data: [12, 19, 3, 5, 2, 3, 7],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            const ctx = document.getElementById('inventoryChart').getContext('2d');
            new Chart(ctx, config);

            // Low Stock Alerts
            const lowAlerts = [{
                    text: 'Thiết bị A đã đạt ngưỡng thấp',
                    quantity: 5
                },
                {
                    text: 'Thiết bị B sắp hết hàng',
                    quantity: 2
                }
            ];

            const lowAlertList = document.getElementById('lowAlertList');
            lowAlerts.forEach(alert => {
                const li = document.createElement('li');
                li.textContent = `${alert.text} (${alert.quantity} items)`;
                li.style.color = '#721c24';
                lowAlertList.appendChild(li);
            });

            // High Stock Alerts
            const highAlerts = [{
                    text: 'Thiết bị C vượt mức tồn kho tối đa',
                    quantity: 100
                },
                {
                    text: 'Thiết bị D tồn kho cao bất thường',
                    quantity: 150
                }
            ];

            const highAlertList = document.getElementById('highAlertList');
            highAlerts.forEach(alert => {
                const li = document.createElement('li');
                li.textContent = `${alert.text} (${alert.quantity} items)`;
                li.style.color = '#856404';
                highAlertList.appendChild(li);
            });
        });

        const forecastLabels = @json(array_column($forecastData, 'month'));
        const forecastValues = @json(array_column($forecastData, 'inventory'));

        const forecastData = {
            labels: forecastLabels,
            datasets: [{
                label: 'Dự Báo Tồn Kho',
                data: forecastValues,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        };

        const forecastConfig = {
            type: 'line',
            data: forecastData,
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const forecastCtx = document.getElementById('forecastChart').getContext('2d');
        new Chart(forecastCtx, forecastConfig);
    </script>
@endsection

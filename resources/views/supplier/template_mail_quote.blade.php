<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu Cầu Báo Giá</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .email-container {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: #333;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .total {
            font-weight: bold;
        }

        .pointer {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="email-container">
        <h1>Kính gửi: {{ $supplierName }}</h1>

        <p>
            Chúng tôi hiện đang có nhu cầu nhập các thiết bị từ quý công ty. Vui lòng cung cấp báo
            giá cho các thiết bị sau:
        </p>

        <p>
            Xin vui lòng kiểm tra thông tin và gửi lại báo giá trong thời gian sớm nhất.
        </p>

        <div class="text-center">
            <a href="{{ asset('storage/' . $fileExcel) }}" class="pointer button" style="color: rgb(255, 255, 255);"
                download="{{ basename($fileExcel) }}">
                <i class="fa fa-download me-1"></i>Tải Danh Sách
            </a>
        </div>

        <div class="footer">
            <p>Trân trọng,<br>
                Bệnh Viện Đa Khoa Beesoft</p>
            <p><a href="#">www.beesoft.com</a></p>
        </div>
    </div>

</body>

</html>

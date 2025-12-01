<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Phiếu nghiệm thu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .company-name { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .title { font-size: 18px; font-weight: bold; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .info-section { margin-bottom: 15px; }
        .signature { margin-top: 30px; display: flex; justify-content: space-between; }
        .sign-box { float: left; width: 33%; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">CÔNG TY GIẢI PHÁP CÔNG NGHỆ CNET</div>
        <div>Địa chỉ: Bắc Ninh - Hotline: 1900 xxxx</div>
    </div>

    <div class="text-center">
        <div class="title">BIÊN BẢN NGHIỆM THU & BÀN GIAO THIẾT BỊ</div>
        <div>Mã phiếu: <strong>{{ $order->code }}</strong> - Ngày in: {{ $date }}</div>
    </div>

    <div class="info-section">
        <strong>1. Khách hàng:</strong> {{ $order->customer->name }} <br>
        <strong>Điện thoại:</strong> {{ $order->customer->contacts->where('type','phone')->first()->value ?? '...' }} <br>
        <strong>Địa chỉ:</strong> {{ $order->customer->contacts->where('type','address')->first()->value ?? '...' }} <br>
        <strong>Nội dung yêu cầu:</strong> {{ $order->title }}
    </div>

    <div class="info-section">
        <strong>2. Chi tiết thực hiện & Vật tư bàn giao:</strong>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">STT</th>
                    <th style="width: 40%">Nội dung / Tên thiết bị</th>
                    <th style="width: 25%">Serial / IMEI</th>
                    <th style="width: 10%">SL</th>
                    <th style="width: 20%">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @php $stt = 1; @endphp
                @foreach($order->tasks as $task)
                    {{-- Dòng công việc --}}
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td><b>Dịch vụ:</b> {{ $task->report_content }}</td>
                        <td></td>
                        <td class="text-center">1</td>
                        <td>{{ $task->performer->name ?? 'KTV' }}</td>
                    </tr>
                    {{-- Dòng vật tư --}}
                    @foreach($task->items as $item)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-center">{{ $item->serial_number ?: '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td>Bảo hành</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    @if($totalAmount > 0)
    <div class="info-section" style="margin-top: 10px;">
        <strong>3. Thanh toán:</strong>
        <div style="font-size: 14px; margin-top: 5px;">
            Tổng cộng tiền mặt: <strong>{{ number_format($totalAmount) }} VNĐ</strong>
        </div>
    </div>
    @endif

    <div class="signature" style="width: 100%; overflow: hidden;">
        <div class="sign-box">
            <strong>Khách hàng</strong><br>
            (Ký và ghi rõ họ tên)<br><br><br><br>
        </div>
        <div class="sign-box">
            <strong>Nhân viên kỹ thuật</strong><br>
            (Ký xác nhận)<br><br><br><br>
        </div>
        <div class="sign-box">
            <strong>Đại diện công ty</strong><br>
            (Nếu có)<br><br><br><br>
        </div>
    </div>

    <div style="font-style: italic; font-size: 10px; margin-top: 20px; text-align: center;">
        * Quý khách vui lòng giữ phiếu này để bảo hành. Tra cứu bảo hành online tại: cnetpos.test/tra-cuu-bao-hanh
    </div>
</body>
</html>
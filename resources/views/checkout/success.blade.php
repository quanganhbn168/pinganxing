@extends('layouts.master')
@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container py-5 text-center">
    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
    <h2>Đặt hàng thành công!</h2>
    <p>Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là <strong>#{{ $order->code }}</strong>.</p>
    <p>Chúng tôi sẽ liên hệ với bạn để xác nhận đơn hàng trong thời gian sớm nhất.</p>

    {{-- Hiển thị QR Code nếu là chuyển khoản --}}
    @if($order->payment_method == 'bank_transfer')
        <div class="mt-5 p-4 border rounded" style="max-width: 450px; margin: auto;">
            @php
                $bankId = "970436"; // Ví dụ: Vietcombank
                $accountNo = "105867163975"; // SỐ TÀI KHOẢN CỦA BẠN
                $accountName = "TRAN QUANG ANH"; // TÊN CHỦ TK CỦA BẠN
                $amount = $order->total_price;
                $note = "NLMT " . $order->code; // Nội dung chuyển khoản ngắn gọn
                $qrCodeUrl = "https://api.vietqr.io/image/{$bankId}-{$accountNo}-print.png?amount={$amount}&addInfo=" . urlencode($note) . "&accountName=" . urlencode($accountName);
            @endphp
            <h4>Quét mã QR để thanh toán</h4>
            <p class="mb-1"><strong>Nội dung:</strong> <span class="text-danger">{{ $note }}</span></p>
            <p><strong>Số tiền:</strong> <span class="text-danger">{{ number_format($amount) }}đ</span></p>
            <img src="{{ $qrCodeUrl }}" alt="Mã QR thanh toán" class="img-fluid">
        </div>
    @endif

    <a href="/" class="btn bg-main mt-4">Tiếp tục mua sắm</a>
</div>
<script>
  try {
    localStorage.removeItem('guest_cart');
    // nếu có key khác thì xoá thêm:
    // localStorage.removeItem('guest_cart_count');
    // Phát sự kiện để mini-cart UI bên ngoài có thể lắng nghe và reset:
    window.dispatchEvent(new CustomEvent('cart:cleared'));
  } catch(e){}
</script>

@endsection

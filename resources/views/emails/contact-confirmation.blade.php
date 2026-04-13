<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận thông tin liên hệ</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
        h2 { color: #007bff; }
        strong { color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cảm ơn bạn đã liên hệ!</h2>
        <p>Chào <strong>{{ $contact->name }}</strong>,</p>
        <p>Chúng tôi đã nhận được thông tin liên hệ của bạn và sẽ phản hồi trong thời gian sớm nhất. Dưới đây là thông tin bạn đã cung cấp:</p>
        <ul>
            <li><strong>Họ tên:</strong> {{ $contact->name }}</li>
            <li><strong>Điện thoại:</strong> {{ $contact->phone }}</li>
            <li><strong>Nội dung:</strong><br>{!! nl2br(e($contact->message)) !!}</li>
        </ul>
        <p>Trân trọng,<br>Đội ngũ {{ config('app.name') }}</p>
    </div>
</body>
</html>

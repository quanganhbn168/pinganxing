@push("css")
<style>
    #popup-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background-color: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 16px;
    }

    .popup-modal {
        position: relative;
        background: #fff;
        padding: 32px 24px;
        max-width: 500px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        animation: popup-fade-in 0.4s ease;
    }

    .popup-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: transparent;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #333;
    }

    .popup-content h2 {
        font-size: 22px;
        margin-bottom: 16px;
        text-align: center;
        color: var(--primary-color);
    }

    .popup-content .form-group {
        margin-bottom: 14px;
    }

    .popup-content input[type="text"],
    .popup-content textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .popup-content input[type="checkbox"] {
        margin-right: 6px;
    }

    .popup-content button[type="submit"] {
        background-color: var(--primary-color, #1e88e5);
        color: #fff;
        border: none;
        padding: 10px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    .popup-content button[type="submit"]:hover {
        background-color: var(--primary-color-hover, #1565c0);
    }

    .popup-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #333;
        justify-content: center;
    }

    @keyframes popup-fade-in {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

</style>
@endpush
<div id="popup-overlay">
    <div class="popup-modal">
        <button class="popup-close" onclick="closePopup()">×</button>
        <div class="popup-content">
            <h2>Tư vấn miễn phí từ chuyên gia, bác sĩ</h2>
            <form id="contact-form-popup" action="{{ route('contact.store') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Họ và tên *" required>
                </div>
                <div class="form-group">
                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại *" required>
                </div>
                <div class="form-group">
                    <textarea name="message" class="form-control" rows="3" placeholder="Tình trạng của bạn (tùy chọn)"></textarea>
                </div>
                <div class="form-group">
                    <label>Vấn đề quan tâm:</label><br>
                    <div>
                        <label><input type="checkbox" name="interests[]" value="Răng sứ thẩm mỹ"> Răng sứ thẩm mỹ</label><br>
                        <label><input type="checkbox" name="interests[]" value="Chỉnh nha / Niềng răng"> Chỉnh nha / Niềng răng</label><br>
                        <label><input type="checkbox" name="interests[]" value="Trồng răng implant"> Trồng răng implant</label><br>
                        <label><input type="checkbox" name="interests[]" value="Bệnh lý khác"> Bệnh lý khác</label>
                    </div>
                </div>
                <div class="form-group popup-checkbox">
                    <input type="checkbox" id="dontShowToday"> Hôm nay không hiện nữa
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">Gửi tư vấn</button>
                </div>
                <p style="font-size: 13px; text-align: center; color: #666;">
                    Thông tin của bạn được bảo mật. <a href="/chinh-sach-bao-mat" target="_blank">Xem chính sách bảo mật</a>
                </p>
            </form>
        </div>
    </div>
</div>

@push("js")
<script>
    function showPopup() {
        const overlay = document.getElementById('popup-overlay');
        if (overlay) overlay.style.display = 'flex';
    }

    function closePopup() {
        if (document.getElementById('dontShowToday')?.checked) {
            const now    = new Date();
            const expiry = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1); // hết hôm nay
            localStorage.setItem('hidePopupUntil', expiry.getTime());
        }
        document.getElementById('popup-overlay').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const hideUntil = parseInt(localStorage.getItem('hidePopupUntil'), 10) || 0;
        if (Date.now() > hideUntil) {
            setTimeout(showPopup, 10000); // 15 000 ms = 15 s
        }
    });
</script>

@endpush

<div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl p-6 shadow-lg text-white">
    <h3 class="text-2xl font-bold mb-2 flex items-center">
        <i class="fas fa-paper-plane mr-3"></i> Đăng ký tư vấn
    </h3>
    <p class="text-blue-100 text-sm mb-6">
        Vui lòng điền thông tin vào form bên dưới để nhận được báo giá và hỗ trợ sớm nhất *
    </p>
    
    <form action="{{ route('contact.store') }}" method="POST" id="sidebarContactForm" class="space-y-4">
        @csrf
        <div>
            <input type="text" name="name" 
                   class="bg-white/10 border border-blue-400/30 text-white text-sm rounded-lg focus:ring-white focus:border-white block w-full p-3 placeholder-blue-200 transition-colors"
                   placeholder="Họ và tên *" required>
        </div>
        <div>
            <input type="tel" name="phone" 
                   class="bg-white/10 border border-blue-400/30 text-white text-sm rounded-lg focus:ring-white focus:border-white block w-full p-3 placeholder-blue-200 transition-colors"
                   placeholder="Số điện thoại *" required>
        </div>
        <div>
            <textarea name="message" rows="3"
                      class="bg-white/10 border border-blue-400/30 text-white text-sm rounded-lg focus:ring-white focus:border-white block w-full p-3 placeholder-blue-200 transition-colors"
                      placeholder="Nội dung cần tư vấn..."></textarea>
        </div>
        <button type="submit" 
                class="w-full text-blue-900 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-sm px-5 py-3 text-center transition-colors shadow-md">
            Gửi thông tin ngay
        </button>
    </form>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarForm = document.getElementById('sidebarContactForm');
        if (sidebarForm) {
            sidebarForm.addEventListener('submit', function(e) {
                const phoneInput = sidebarForm.querySelector('input[name="phone"]');
                if (phoneInput) {
                    const phone = phoneInput.value;
                    const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/;
                    if (!phoneRegex.test(phone)) {
                        e.preventDefault();
                        alert('Số điện thoại không hợp lệ! Vui lòng nhập đúng định dạng.');
                        phoneInput.focus();
                    }
                }
            });
        }
    });
</script>
@endpush

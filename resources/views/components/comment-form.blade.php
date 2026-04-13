<div class="mt-10 bg-gray-50 dark:bg-gray-800/80 rounded-sm p-6 md:p-8 border border-gray-100 dark:border-gray-700 shadow-inner">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-wider text-center">Đăng bình luận / Đánh giá</h3>
    
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-sm border border-green-200 dark:border-green-800 flex items-start">
            <i class="fas fa-check-circle mt-1 mr-3 text-lg"></i>
            <div>
                <strong class="font-bold block">Thành công!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-sm border border-red-200 dark:border-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('comments.store') }}" method="POST">
        @csrf
        <input type="hidden" name="commentable_id" value="{{ $commentable->id }}">
        <input type="hidden" name="commentable_type" value="{{ get_class($commentable) }}">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2 text-sm">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nhập họ tên của bạn" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-sm px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-shadow outline-none">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2 text-sm">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Để lại SĐT nếu cần hỗ trợ" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-sm px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-shadow outline-none">
            </div>
        </div>
        
        <div class="mb-6 bg-white dark:bg-gray-900 p-4 border border-gray-200 dark:border-gray-600 rounded-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <label class="block text-gray-900 dark:text-white font-bold mb-0">Đánh giá sao:</label>
            <div class="flex items-center gap-2 flex-row-reverse justify-end star-rating">
                @for($i=5; $i>=1; $i--)
                    <input type="radio" name="rating" id="rating-{{$i}}" value="{{ $i }}" class="peer hidden" {{ (old('rating', 5) == $i) ? 'checked' : '' }}>
                    <label for="rating-{{$i}}" class="cursor-pointer text-3xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition-colors" title="{{ $i }} sao">
                        <i class="fas fa-star"></i>
                    </label>
                @endfor
            </div>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-gray-700 dark:text-gray-300 font-medium text-sm">Nội dung <span class="text-red-500">*</span></label>
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-brand-600 font-medium">
                    <i class="fas fa-magic"></i> Bạn có thể chọn mẫu nhanh:
                </div>
            </div>
            
            <textarea id="comment-content" name="content" required placeholder="Nhập đánh giá và bình luận của bạn về {{ $type == 'project' ? 'dự án' : ($type == 'product' ? 'sản phẩm' : 'bài viết') }} này..." rows="4" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-sm px-4 py-3 text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-shadow outline-none">{{ old('content') }}</textarea>
            
            <div class="mt-3 flex flex-wrap gap-2" id="quick-samples-container">
                <!-- Nút mẫu được JS chèn vào đây -->
            </div>
        </div>
        
        <div class="text-center">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-10 rounded-sm uppercase tracking-wide transition-colors shadow-md hover:shadow-lg">
                Gửi Đánh Giá <i class="fas fa-paper-plane ml-2"></i>
            </button>
        </div>
    </form>
</div>

<style>
/* CSS thuần để hover sao từ trái qua phải xài ~ selector */
.star-rating > input:checked ~ label,
.star-rating > label:hover,
.star-rating > label:hover ~ label {
    color: #facc15 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sampleReviewsData = @json($groupedSampleReviews);

    function renderSamples(rating) {
        const container = document.getElementById('quick-samples-container');
        if(!container) return;
        
        container.innerHTML = '';
        const samples = sampleReviewsData[rating] || sampleReviewsData['5'];
        
        samples.forEach(sample => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'sample-review-btn text-xs bg-gray-100 dark:bg-gray-800 hover:bg-brand-50 dark:hover:bg-brand-900/30 text-gray-600 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 border border-gray-200 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-700 px-3 py-1.5 rounded-full transition-transform active:scale-95 duration-200';
            btn.innerText = sample;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const textarea = document.getElementById('comment-content');
                if(textarea) {
                    const currentText = textarea.value.trim();
                    const newText = this.innerText.trim();
                    textarea.value = currentText.length > 0 ? (currentText + '\n' + newText) : newText;
                    textarea.focus();
                }
            });
            container.appendChild(btn);
        });
    }

    // Initialize with 5 stars or old rating
    const checkedRadio = document.querySelector('input[name="rating"]:checked');
    renderSamples(checkedRadio ? checkedRadio.value : '5');

    // Add listener to radios
    const ratingRadios = document.querySelectorAll('input[name="rating"]');
    ratingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            renderSamples(this.value);
        });
    });
});
</script>

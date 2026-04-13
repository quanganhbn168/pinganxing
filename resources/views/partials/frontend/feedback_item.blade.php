{{-- resources/views/partials/frontend/feedback_item.blade.php --}}

<div class="feedback-item">
    <div class="avatar">
        {{-- Lấy ảnh đại diện của người đánh giá --}}
        <img src="{{ asset($feedback->image ?? 'images/setting/no-image.png') }}" 
             alt="{{ $feedback->name }}" 
             width="200" 
             height="200" 
             class="lazyload">
    </div>
    <div class="block-content">
        <b>
            {{-- Lấy tên người đánh giá --}}
            {{ $feedback->name }}
        </b>
        <span>
            {{-- Lấy chức vụ hoặc thông tin thêm --}}
            {{ $feedback->position }}
        </span>
        <div class="feedback-content">
            {{-- Lấy nội dung đánh giá --}}
            "{{ $feedback->content }}"
        </div>
    </div>
</div>

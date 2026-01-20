@extends('layouts.admin')

@section('title', 'Quản lý nội dung Trang chủ')

@push('styles')
<style>
    .section-list {
        margin-bottom: 1rem;
    }
    .section-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    .section-item:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .section-item.is-dragging {
        opacity: 0.5;
        background: #e9ecef;
    }
    .section-item.is-inactive {
        opacity: 0.6;
        background: #f8f9fa;
    }
    .section-drag-handle {
        cursor: grab;
        color: #6c757d;
        padding: 0.5rem;
        margin-right: 0.5rem;
    }
    .section-drag-handle:active {
        cursor: grabbing;
    }
    .section-info {
        flex: 1;
    }
    .section-name {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .section-key {
        font-size: 0.75rem;
        color: #6c757d;
        font-family: monospace;
    }
    .section-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 24px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: #28a745;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
</style>
@endpush

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-home mr-2"></i>Quản lý nội dung Trang chủ</h1>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Các sections trên Trang chủ</h3>
        <div class="card-tools">
            <span class="text-muted">Kéo thả để sắp xếp • Bật/tắt để ẩn/hiện</span>
        </div>
    </div>
    <div class="card-body">
        <div class="section-list" id="sectionList">
            @foreach($sections as $section)
            <div class="section-item {{ !$section->is_active ? 'is-inactive' : '' }}" 
                 data-id="{{ $section->id }}">
                <div class="section-drag-handle">
                    <i class="fas fa-grip-vertical fa-lg"></i>
                </div>
                <div class="section-info">
                    <div class="section-name">{{ $section->name }}</div>
                    <div class="section-key">{{ $section->key }}</div>
                </div>
                <div class="section-actions">
                    <label class="toggle-switch" title="{{ $section->is_active ? 'Đang bật' : 'Đang tắt' }}">
                        <input type="checkbox" 
                               class="toggle-active" 
                               data-id="{{ $section->id }}" 
                               {{ $section->is_active ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <a href="{{ route('admin.homepage-sections.edit', $section->id) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Lưu ý:</strong> 
    Các sections sử dụng dữ liệu từ hệ thống (Dự án, Tin tức, Đánh giá...) sẽ tự động cập nhật khi bạn thay đổi nội dung ở phần quản lý tương ứng.
    Bạn chỉ cần chỉnh sửa tiêu đề, mô tả và ảnh của từng section tại đây.
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Drag & Drop sorting
    const sectionList = document.getElementById('sectionList');
    
    new Sortable(sectionList, {
        handle: '.section-drag-handle',
        animation: 150,
        ghostClass: 'is-dragging',
        onEnd: function(evt) {
            // Lấy thứ tự mới
            const order = Array.from(sectionList.querySelectorAll('.section-item'))
                .map(item => item.dataset.id);
            
            // Gửi AJAX
            fetch('{{ route("admin.homepage-sections.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message);
                }
            })
            .catch(err => {
                toastr.error('Có lỗi xảy ra khi sắp xếp');
                console.error(err);
            });
        }
    });

    // Toggle active/inactive
    document.querySelectorAll('.toggle-active').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const item = this.closest('.section-item');
            
            fetch(`/admin/homepage-sections/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    item.classList.toggle('is-inactive', !data.is_active);
                    toastr.success(data.message);
                }
            })
            .catch(err => {
                toastr.error('Có lỗi xảy ra');
                console.error(err);
            });
        });
    });
});
</script>
@endpush

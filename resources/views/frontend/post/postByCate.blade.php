@extends('layouts.master')
@section('title', $category->name ?? 'Tin tức')

@push('css')
<style>
    /* --- 1. Cấu hình Card bài viết --- */
    .post-item {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%; /* Để các card cao bằng nhau */
        display: flex;
        flex-direction: column;
    }

    .post-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* --- 2. Xử lý ảnh 4:3 --- */
    .post-item .post-item_image {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3; /* Tỷ lệ vàng 4:3 */
        overflow: hidden;
        flex-shrink: 0;
    }

    .post-item .post-item_image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .post-item:hover .post-item_image img {
        transform: scale(1.05);
    }

    /* --- 3. Nội dung bài viết --- */
    .post-item_info {
        padding: 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .post-item_title a {
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Giới hạn 2 dòng tiêu đề */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .info_description {
        font-size: 0.9rem;
        color: #666;
        margin: 10px 0;
        flex-grow: 1;
    }

    .read-more-link {
        color: #007bff;
        font-weight: 600;
        text-decoration: none;
        margin-top: auto;
    }

    /* --- 4. Nút chuyển đổi giao diện --- */
    .view-switcher {
        display: flex;
        gap: 10px;
    }

    .btn-view {
        background: #f8f9fa;
        border: 1px solid #ddd;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 4px;
        color: #555;
        transition: all 0.2s;
    }

    .btn-view.active, .btn-view:hover {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    /* --- 5. Styles cho chế độ LIST (Logic chính) --- */
    /* Khi container cha có class 'layout-list' */
    .post-container.layout-list .col-lg-4,
    .post-container.layout-list .col-md-6 {
        width: 100%; 
        flex: 0 0 100%;
        max-width: 100%;
    }

    .post-container.layout-list .post-item {
        flex-direction: row; /* Xếp ngang */
        align-items: center;
    }

    .post-container.layout-list .post-item_image {
        width: 35%; /* Ảnh 35% */
        aspect-ratio: 4 / 3;
    }

    .post-container.layout-list .post-item_info {
        width: 65%; /* Chữ 65% */
        padding: 20px;
    }

    .post-container.layout-list .post-item_title a {
        font-size: 1.3rem;
    }

    /* --- 6. Mobile First Override --- */
    /* Trên mobile, luôn hiển thị dạng Grid dọc dù đang chọn List */
    @media (max-width: 768px) {
        .post-container.layout-list .post-item {
            flex-direction: column;
        }
        .post-container.layout-list .post-item_image {
            width: 100%;
        }
        .post-container.layout-list .post-item_info {
            width: 100%;
        }
        /* Ẩn nút chuyển đổi trên mobile cho gọn (tuỳ chọn) */
        .view-switcher {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="post-wrapper">
    <div class="collection-banner">
        {{-- Banner Image --}}
        <img
            src="{{ optional($category->bannerImage())->url() ?: ($category->banner ? asset($category->banner) : asset($setting->banner)) }}"
            alt="{{ $category->name }}"
            style="width: 100%; object-fit: cover;"
            loading="lazy">
    </div>

    <div class="container py-5">
        <div class="category-section mb-5">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="custom-section-title m-0">
                    <a href="{{ route('frontend.slug.handle', $category->slug) }}" class="text-dark text-decoration-none">
                        {{ $category->name }}
                    </a>
                </h2>

                <div class="view-switcher">
                    <button class="btn-view active" onclick="setView('grid')" title="Dạng lưới">
                        <i class="fa-solid fa-border-all"></i>
                    </button>
                    <button class="btn-view" onclick="setView('list')" title="Dạng danh sách">
                        <i class="fa-solid fa-list"></i>
                    </button>
                </div>
            </div>

            <div class="postByCateList post-container">
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="post-item">
                                <div class="post-item_image">
                                    <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                                        <img
                                            src="{{ optional($post->mainImage())->url()
                                                ?? optional($post->bannerImage())->url()
                                                ?? ($post->image ? asset($post->image) : asset('images/no-image.png')) }}"
                                            alt="{{ $post->title }}"
                                            loading="lazy">
                                    </a>
                                </div>
                                <div class="post-item_info">
                                    <h3 class="post-item_title">
                                        <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    <div class="info_description">
                                        {{ Str::limit($post->description, 120, '...') }}
                                    </div>
                                    <a class="read-more-link" href="{{ route('frontend.slug.handle', $post->slug) }}">
                                        Xem thêm <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Nếu có phân trang thì hiển thị ở đây --}}
                @if(method_exists($posts, 'links'))
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function setView(mode) {
        const container = document.querySelector('.post-container');
        const buttons = document.querySelectorAll('.btn-view');

        // 1. Update Button UI
        buttons.forEach(btn => btn.classList.remove('active'));
        if (mode === 'grid') {
            document.querySelector('button[onclick="setView(\'grid\')"]').classList.add('active');
        } else {
            document.querySelector('button[onclick="setView(\'list\')"]').classList.add('active');
        }

        // 2. Update Layout
        if (container) {
            if (mode === 'list') {
                container.classList.add('layout-list');
            } else {
                container.classList.remove('layout-list');
            }
        }

        // 3. Save Preference
        localStorage.setItem('cateViewMode', mode);
    }

    // Load trạng thái cũ khi vào trang
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('cateViewMode');
        if (savedMode) {
            setView(savedMode);
        }
    });
</script>
@endpush
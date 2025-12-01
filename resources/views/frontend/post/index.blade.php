@extends('layouts.master')
@section('title', 'Tin tức')

@push('css')
<style>
    /* --- Cấu hình chung --- */
    .post-item {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%; /* Để các card bằng nhau trong Grid */
        display: flex;
        flex-direction: column;
    }

    .post-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Ảnh luôn giữ tỷ lệ 4:3 */
    .post-item .post-item_image {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        flex-shrink: 0; /* Không bị co lại trong flex */
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

    /* Phần nội dung */
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
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .info_description {
        font-size: 0.9rem;
        color: #666;
        margin: 10px 0;
        flex-grow: 1; /* Đẩy nút xem thêm xuống đáy */
    }

    .read-more-link {
        color: #007bff;
        font-weight: 600;
        text-decoration: none;
        margin-top: auto;
    }

    /* --- Nút chuyển đổi giao diện --- */
    .view-switcher {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        justify-content: flex-end;
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

    /* --- Styles cho chế độ LIST (Quan trọng) --- */
    /* Khi container cha có class 'layout-list' */
    .post-container.layout-list .col-lg-4,
    .post-container.layout-list .col-md-6 {
        /* Ghi đè bootstrap để chiếm 100% chiều ngang */
        width: 100%; 
        flex: 0 0 100%;
        max-width: 100%;
    }

    .post-container.layout-list .post-item {
        flex-direction: row; /* Xếp ngang: Ảnh trái - Tin phải */
        align-items: center; /* Căn giữa theo chiều dọc */
    }

    .post-container.layout-list .post-item_image {
        width: 35%; /* Ảnh chiếm 35% chiều rộng */
        aspect-ratio: 4 / 3; /* Vẫn giữ tỷ lệ 4:3 */
    }

    .post-container.layout-list .post-item_info {
        width: 65%; /* Nội dung chiếm phần còn lại */
        padding: 20px;
    }
    
    .post-container.layout-list .post-item_title a {
        font-size: 1.4rem; /* Chữ to hơn chút ở chế độ list */
    }

    /* --- Mobile First Override --- */
    /* Trên màn hình nhỏ (dưới 768px), dù đang chọn List thì vẫn hiển thị kiểu Grid dọc cho đẹp */
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
        .view-switcher {
            display: none; /* Ẩn nút chuyển đổi trên mobile nếu muốn, hoặc để lại cũng được */
        }
    }
</style>
@endpush

@section('content')
<div class="post-wrapper">
    <div class="collection-banner">
        <img src="{{ optional($page->bannerImage())->url() ?: asset($setting->banner) }}" alt="{{ $page->title }}" style="width: 100%; object-fit: cover;">
    </div>
    
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="title m-0">Tất cả tin tức</h1>
            
            <div class="view-switcher">
                <button class="btn-view active" onclick="setView('grid')" title="Dạng lưới">
                    <i class="fa-solid fa-border-all"></i>
                </button>
                <button class="btn-view" onclick="setView('list')" title="Dạng danh sách">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>
        </div>

        @foreach($postCategories as $category)
            @if($category->posts->isNotEmpty())
                <div class="category-section mb-5">
                    <h2 class="custom-section-title mb-3">
                        <a href="{{ route('frontend.slug.handle', $category->slugValue) }}" class="text-dark text-decoration-none">{{ $category->name }}</a>
                    </h2>
                    
                    <div class="postByCateList post-container" id="container-{{ $category->id }}">
                        <div class="row">
                            @foreach($category->posts as $post)
                                <div class="col-12 col-md-6 col-lg-4 mb-4 item-column">
                                    <div class="post-item">
                                        <div class="post-item_image">
                                            <a href="{{ route('frontend.slug.handle', $post->slugValue) }}">
                                                <img
                                                    src="{{ optional($post->mainImage())->url()
                                                        ?? optional($post->bannerImage())->url()
                                                        ?? ($post->image ? asset($post->image) : asset('images/no-image.png')) }}"
                                                    alt="{{ $post->title }}">
                                            </a>
                                        </div>
                                        <div class="post-item_info">
                                            <h3 class="post-item_title">
                                                <a href="{{ route('frontend.slug.handle', $post->slugValue) }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h3>
                                            <div class="info_description">
                                                {{ Str::limit($post->description, 120, '...') }}
                                            </div>
                                            <a class="read-more-link" href="{{ route('frontend.slug.handle', $post->slugValue) }}">
                                                Xem thêm <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

@push('js')
<script>
    // Hàm setView để chuyển đổi class
    function setView(mode) {
        const containers = document.querySelectorAll('.post-container');
        const buttons = document.querySelectorAll('.btn-view');

        // Xử lý active button
        buttons.forEach(btn => btn.classList.remove('active'));
        if (mode === 'grid') {
            document.querySelector('button[onclick="setView(\'grid\')"]').classList.add('active');
        } else {
            document.querySelector('button[onclick="setView(\'list\')"]').classList.add('active');
        }

        // Xử lý layout
        containers.forEach(container => {
            if (mode === 'list') {
                container.classList.add('layout-list');
            } else {
                container.classList.remove('layout-list');
            }
        });

        // (Tùy chọn) Lưu trạng thái vào LocalStorage để nhớ khi load lại trang
        localStorage.setItem('newsViewMode', mode);
    }

    // Tự động load trạng thái đã lưu khi vào trang
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('newsViewMode');
        if (savedMode) {
            setView(savedMode);
        }
    });
</script>
@endpush

@endsection
{{-- Cấu trúc 2 cột: col-md-4 cho ảnh và col-md-8 cho nội dung --}}
<div class="mb-4 border-bottom pb-3 row">
    <div class="col-md-4">
        <a href="{{ route('frontend.slug.handle', $project->slug) }}">
            <img src="{{ asset($project->image ?? 'images/setting/no-image.png') }}" alt="{{ $project->name }}" class="img-fluid w-100" style="object-fit: cover; max-height: 200px;">
        </a>
    </div>
    <div class="col-md-8">
        <a href="{{ route('frontend.slug.handle', $project->slug) }}" class="d-block mb-2">
            <h4 class="mb-1">{{ $project->name }}</h4>
        </a>
        <div class="small text-muted mb-2">
            {{-- Giả sử model Project có cột 'created_at'. Nếu không có, anh có thể xóa dòng này. --}}
            @if(isset($project->created_at))
                {{ $project->created_at->format('d/m/Y') }}
            @endif
        </div>
        <p>{{ Str::limit(strip_tags($project->description), 200) }}</p>
    </div>
</div>
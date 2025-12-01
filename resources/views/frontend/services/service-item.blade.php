{{-- resources/views/frontend/services/partials/service-item.blade.php --}}
<div class="mb-4 border-bottom pb-3 row">
    <div class="col-md-4">
        <a href="{{ route('frontend.slug.handle', $service->slug) }}">
            <img src="{{ asset($service->image ?? 'images/setting/no-image.png') }}" alt="{{ $service->name }}" class="img-fluid w-100" style="object-fit: cover; max-height: 180px;">
        </a>
    </div>
    <div class="col-md-8">
        <a href="{{ route('frontend.slug.handle', $service->slug) }}" class="d-block mb-2">
            <h4 class="mb-1">{{ $service->name }}</h4>
        </a>
        <div class="small text-muted mb-2">
            {{ $service->created_at->format('d/m/Y') }}
        </div>
        <p>{{ Str::limit(strip_tags($service->description), 150) }}</p>
    </div>
</div>
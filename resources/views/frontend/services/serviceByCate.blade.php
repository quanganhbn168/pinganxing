@extends('layouts.master')
@section('title',$category->name)
@push('css')
<link rel="stylesheet" href="{{asset('css/product.css')}}">
@endpush
@section('content')
	<div id="category-wrapper" class="bg-light">
        <x-frontend.leaderboard
            :image="$category->banner?->url ?? $category->banner ?? $pageSettings->services_banner"
            :title="$category->name"
            subline="Danh mục dịch vụ"
            :description="$category->description ?? null"
            :breadcrumb="[
                ['label' => 'Dịch vụ', 'url' => route('frontend.services.index')],
                ['label' => $category->name],
            ]"
        />

		<div class="container">
			<div class="row">
				<div class="col-12 col-md-9 bg-white">
					<h1 class="text-primary">{{$category->name}}</h1>
					<div class="description">
						{!!$category->content!!}
					</div>
					<div class="services-wrapper">
						<h3 class="services-title text-center">Các dịch vụ</h3>
						<div class="row services-list">
							@foreach($services as $key => $service)
							<div class="col-md-4 col-sm-6 mb-4">
								<div class="services-list_item">
									<a href="{{route('slug.resolve',$service->slug)}}">
										<div class="item-image">
											<img src="{{ $service->image?->url }}" alt="{{ $service->name }}">
										</div>
										<div class="item-description">
											{{ $service->name }}
										</div>
									</a>
								</div>
							</div>
							@endforeach
						</div>
						<h3 class="services-title text-center mt-4">Bảng giá</h3>
						
							<h3>{{$category->name}}</h3>
							<div class="banggia">
							    <table class="table table-bordered banggia-table">
							        <thead class="thead-primary bg-primary text-white">
							            <tr>
							                <th scope="col">STT</th>
							                <th scope="col">Tên dịch vụ</th>
							                <th scope="col">Đơn vị tính</th>
							                <th scope="col">Giá tiền</th>
							            </tr>
							        </thead>
							        <tbody>
							            @foreach($services as $serviceKey => $child)
							            <tr>
							                <td>{{ $serviceKey + 1 }}</td>
							                <td>
							                    <a href="{{route('slug.resolve',$child->slug)}}">
							                        {{ $child->name }}
							                    </a>
							                </td>
							                <td>{{ $child->unit->name ?? '' }}</td>
							                <td>{{ number_format($child->price, 0, ',', '.') }}₫</td>
							            </tr>
							            @endforeach
							        </tbody>
							    </table>
							</div>          
							
					</div>
		
					<div class="social-share">
						<span class="social-share_label">Chia sẻ:</span>
						<a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="social-share_item facebook" title="Chia sẻ Facebook"><i class="fab fa-facebook-f"></i></a>
						<a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($service->name) }}" target="_blank" rel="noopener noreferrer" class="social-share_item twitter" title="Chia sẻ Twitter"><i class="fab fa-twitter"></i></a>
						<a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($service->name) }}" target="_blank" rel="noopener noreferrer" class="social-share_item linkedin" title="Chia sẻ LinkedIn"><i class="fab fa-linkedin-in"></i></a>
					</div>

				</div>
				<x-frontend.aside />
			</div>
		</div>
	</div>			
@endsection
@push('js')
@endpush

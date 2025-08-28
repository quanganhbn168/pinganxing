@extends('layouts.admin')

@section('title','Danh sách danh mục')

@section('content_header_title','Danh sách danh mục')

@section('content')



<div class="card">

	<div class="card-header">

		<h3 class="card-title"></h3>

		<div class="card-tools">

			<a href="{{ route('admin.categories.create') }}" class="btn btn-primary">

				<i class="fas fa-plus"></i> Thêm Danh mục

			</a>

		</div>

	</div>

	<div class="card-body table-responsive p-0">

		<table class="table table-hover text-nowrap">

			<thead>

				<tr>

					<th>#</th>

					<th>Tên danh mục</th>

					<th>Ảnh</th>

					<th>Danh mục cha</th>

					<th>Trạng thái</th>

					<th>H.Trang chủ</th>

					<th>H.Menu</th>

					<th>Ngày tạo</th>

					<th>Thao tác</th>

				</tr>

			</thead>

			<tbody>

				@foreach($categories as $key => $category)

				<tr>

					<td>{{ $key + 1 }}</td>

					<td>{{ $category->name }}</td>

					<td>

						<img src="{{ asset($category->image) }}" alt="Image" style="height: 60px;">

					</td>

					<td>

						{{ $category->parent?->name ?? '---' }}

					</td>

					<td>

						<x-boolean-toggle model="Category" :record="$category" field="status" />

					</td>

					<td>

						<x-boolean-toggle model="Category" :record="$category" field="is_home" />

					</td>

					<td>

						<x-boolean-toggle model="Category" :record="$category" field="is_menu" />

					</td>

					

					<td>{{ $category->created_at->format('d/m/Y') }}</td>

					<td>

						<a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">

							<i class="far fa-edit"></i>

						</a>



						<form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline form-delete">

							@csrf

							@method('DELETE')

							<button type="submit" class="btn btn-danger delete">

								<i class="far fa-trash-alt"></i>

							</button>

						</form>

					</td>

				</tr>

				@endforeach

			</tbody>

		</table>

	</div>
	<div class="card-footer clearfix">

            {{-- Hiển thị link phân trang --}}

            {{ $categories->links() }}

        </div>
</div>



@endsection



@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

	const forms = document.querySelectorAll('.form-delete');



	forms.forEach(function(form) {

		form.addEventListener('submit', function (e) {

			e.preventDefault();



			Swal.fire({

				title: 'Bạn chắc chắn?',

				text: 'Hành động này không thể hoàn tác!',

				icon: 'warning',

				showCancelButton: true,

				confirmButtonColor: '#d33',

				cancelButtonColor: '#6c757d',

				confirmButtonText: 'Xoá',

				cancelButtonText: 'Huỷ'

			}).then((result) => {

				if (result.isConfirmed) {

					form.submit();

				}

			});

		});

	});

});

</script>

@endpush


{{-- resources/views/admin/service_categories/index.blade.php --}}

@extends('layouts.admin')
@section('title','Danh mục dịch vụ')
@section('content_header','Danh mục dịch vụ')
@section('content')
<div class="row">
    <div class="col-9">
        <div class="card">
            <div class="card-header">
                <div class="card-tools">
                    <a href="{{ route('admin.service_categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm danh mục
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Ảnh</th>
                            <th>Trạng thái</th>
                            <th>Hiện Menu</th>
                            <th>Hiện Footer</th>
                            <th>Hiện Trang chủ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td><img src="{{ asset($item->image) }}" alt="Image" style="height: 60px;"></td>
                            <td>
                                <x-boolean-toggle model="ServiceCategory" :record="$item" field="status" />
                            </td>
                            <td>
                                <x-boolean-toggle model="ServiceCategory" :record="$item" field="is_menu" />
                            </td>
                            <td>
                                <x-boolean-toggle model="ServiceCategory" :record="$item" field="is_footer" />
                            </td>
                            <td>
                                <x-boolean-toggle model="ServiceCategory" :record="$item" field="is_home" />
                            </td>
                            <td>
                                <a href="{{ route('admin.service_categories.edit', $item) }}" class="btn btn-sm btn-warning"><i class="far fa-edit"></i></a>
                                <form action="{{ route('admin.service_categories.destroy', $item) }}" method="POST" class="d-inline-block form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger delete"><i class="far fa-trash-alt"></i></button>
                                </form>
                                <x-admin.duplicate-button model="service_categories" :id="$item->id"
                            label="Clone" icon="bi bi-copy" confirm="Clone dịch vụ này?" class="ms-1" />
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-3">
            <x-categories.tree-card :categories="$categories" routeName="admin.service_categories.edit" />
        </div>
    </div>

    @endsection

    @push('scripts')
    <script>
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Bạn chắc chắn?',
                    text: 'Hành động này không thể hoàn tác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xoá',
                    cancelButtonText: 'Huỷ'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
    @endpush


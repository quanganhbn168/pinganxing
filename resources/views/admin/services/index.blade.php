@extends('layouts.admin')
@section('title','Danh sách dịch vụ')
@section('content_header','Danh sách dịch vụ')
@section('content')

<div class="card">
    <div class="card-header">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm dịch vụ
        </a>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên dịch vụ</th>
                    <th>Ảnh</th>
                    <th>Danh mục</th>
                    <th>Trạng thái</th>
                    <th>Hiện Menu</th>
                    <th>Hiện Footer</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $key => $service)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $service->name }}</td>
                    <td><img src="{{ asset($service->image) }}" style="height:60px;"></td>
                    <td>{{ $service->category->name ?? '-' }}</td>
                    <td>
                        <x-boolean-toggle model="service" :record="$service" field="status" />
                    </td>
                    <td>
                        <x-boolean-toggle model="service" :record="$service" field="is_menu" />
                    </td>
                    <td>
                        <x-boolean-toggle model="service" :record="$service" field="is_footer" />
                    </td>
                    
                    <td>{{ $service->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning btn-sm"><i class="far fa-edit"></i></a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display:inline-block;" class="form-delete">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>
                        </form>
                        <x-admin.duplicate-button model="services" :id="$service->id"
                            label="Clone" icon="bi bi-copy" confirm="Clone dịch vụ này?" class="ms-1" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

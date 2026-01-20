@extends('layouts.admin')

@section('title', 'Sửa thuộc tính')
@section('content_header_title')
    <h1>Sửa thuộc tính: {{ $attribute->name }}</h1>
@endsection

@section('content')
    {{-- Form sửa tên thuộc tính --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Thông tin chung</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                @method('PUT')
                @include('admin.attributes._form', ['attribute' => $attribute, 'buttonText' => 'Cập nhật'])
            </form>
        </div>
    </div>

    {{-- Phần quản lý giá trị --}}
    <div class="card mt-4">
        <div class="card-header"><h3 class="card-title">Quản lý Giá trị</h3></div>
        <div class="card-body">
            {{-- Form thêm giá trị mới --}}
            <form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST" class="mb-4 border-bottom pb-3">
                @csrf
                <h5>Thêm giá trị mới</h5>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="value">Tên giá trị</label>
                        <input type="text" name="value" id="value" class="form-control @error('value') is-invalid @enderror" placeholder="VD: Đỏ, Xanh, Size L...">
                        @error('value') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Chỉ hiện các ô đặc biệt tùy theo type của thuộc tính cha --}}
                    @if($attribute->type == 'color_swatch')
                    <div class="form-group col-md-4">
                        <label for="color_code">Mã màu</label>
                        <input type="color" name="color_code" id="color_code" class="form-control @error('color_code') is-invalid @enderror">
                        @error('color_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    @elseif($attribute->type == 'image_swatch')
                    <div class="col-md-4">
                        <x-form.image-picker name="image" label="Ảnh minh họa" />
                    </div>
                    @endif
                    
                    <div class="form-group col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success">Thêm</button>
                    </div>
                </div>
            </form>

            {{-- Bảng liệt kê các giá trị đã có --}}
            <h5>Các giá trị hiện có</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Giá trị</th>
                        @if($attribute->type == 'color_swatch') <th>Màu</th> @endif
                        @if($attribute->type == 'image_swatch') <th>Ảnh</th> @endif
                        <th style="width: 100px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attribute->values as $value)
                    <tr>
                        <td>{{ $value->value }}</td>
                        @if($attribute->type == 'color_swatch')
                            <td><div style="width: 25px; height: 25px; background-color: {{ $value->color_code }}; border: 1px solid #ccc;"></div></td>
                        @endif
                        @if($attribute->type == 'image_swatch')
                            <td><img src="{{ $value->image_url }}" alt="" height="40"></td>
                        @endif
                        <td>
                             <form action="{{ route('admin.values.destroy', $value) }}" method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="100%" class="text-center">Chưa có giá trị nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
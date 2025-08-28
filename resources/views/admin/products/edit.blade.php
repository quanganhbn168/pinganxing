@extends('layouts.admin')
@section('title','Chỉnh sửa sản phẩm')
@section('content_header_title', 'Chỉnh sửa sản phẩm')
@push('css')
<style>
    #variants_wrap.d-none { display: none !important; }
    .variants__block {
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: .25rem;
        margin-bottom: 1rem;
    }
    .variants__block-title {
        font-weight: 600;
        margin-bottom: .5rem;
    }
    .variants__hr {
        margin: 2rem 0;
    }
    .variants__bulk-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .variants__bulk-input {
        width: 120px;
        font-size: .875rem;
        padding: .25rem .5rem;
        border: 1px solid #ced4da;
        border-radius: .2rem;
    }
    #variants_table td, #variants_table th { 
        vertical-align: middle;
    }
</style>
@endpush
@section('content')
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Đã có lỗi xảy ra:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Chỉnh sửa sản phẩm: {{ $product->name }}</h3>
        </div>
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
              id="product_form"
              data-validate-url="{{ route('admin.products.validate_uniqueness') }}"
              data-product-id="{{ $product->id }}">
            @csrf
            @method('PUT') 
            @include('admin.products._form')
            <div class="card-footer d-flex justify-content-end">
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-dark">Quay lại</a>
                    <button class="btn btn-primary" type="submit" name="action" value="save">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script src="{{ asset('js/admin/variant-form.js') }}"></script>
    <script src="{{ asset('js/admin/product-validator.js') }}"></script>
@endpush
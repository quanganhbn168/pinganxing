@extends('layouts.admin')

@section('title', 'Thêm tag')
@section('content_header', 'Thêm tag')

@section('content')
<form action="{{ route('admin.tags.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <x-form.input name="name" label="Tên tag" required />
            <x-form.input name="slug" label="Slug (tuỳ chọn)" />
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Lưu</button>
        </div>
    </div>
</form>
@endsection

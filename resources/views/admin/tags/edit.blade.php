@extends('layouts.admin')

@section('title', 'Cập nhật tag')
@section('content_header', 'Cập nhật tag')

@section('content')
<form action="{{ route('admin.tags.update', $tag) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <x-form.input name="name" label="Tên tag" :value="$tag->name" required />
            <x-form.input name="slug" label="Slug" :value="$tag->slug" />
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Lưu</button>
        </div>
    </div>
</form>
@endsection

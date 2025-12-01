@extends('layouts.admin')



@section('title', 'Chỉnh sửa: ' . $postCategory->name)

@section('content_header', 'Chỉnh sửa: ' . $postCategory->name)



@section('content')

<form action="{{ route('admin.post-categories.update', $postCategory) }}" method="POST">

    @csrf

    @method('PUT')

    <div class="row">

        <div class="col-lg-8">

            <div class="card shadow mb-4">

                <div class="card-header">

                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chính</h6>

                </div>

                <div class="card-body">

                    <x-form.input name="name" label="Tên danh mục" :value="old('name', $postCategory->name)" required />
                    <x-form.slug

                        name="slug"

                        label="Đường dẫn (slug)"

                        :value="old('slug', $postCategory->slug)"

                        source="#name"

                        table="post_categories"

                        field="slug"

                        :current-id="$postCategory->id"

                    />


                    <div class="form-group">

                        <label for="parent_id">Danh mục cha</label>

                        <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">

                            <option value="">— Là danh mục gốc —</option>

                            <x-form.category-tree 

                                :categories="$parentCategories"

                                :selectedId="old('parent_id', $postCategory->parent_id)"

                                :excludeId="$postCategory->id"

                            />

                        </select>

                        @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    </div>



                    

                </div>

            </div>

        </div>



        <div class="col-lg-4">

            <div class="card shadow mb-4">

                <div class="card-header">

                    <h6 class="m-0 font-weight-bold text-primary">Thông tin phụ</h6>

                </div>

                <div class="card-body">

                    <x-form.switch name="status" label="Trạng thái" :checked="old('status', $postCategory->status)" />

                    <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="old('is_home', $postCategory->is_home)" />

                    

                    <hr>



                    <x-admin.form.media-input

                        name="image_original_path"

                        label="Ảnh đại diện"

                        :multiple="false"

                        :value="$postCategory->mainImage() ? $postCategory->mainImage()->original_path : old('image_original_path')"

                    />



                    <x-admin.form.media-input

                        name="banner_original_path"

                        label="Banner"

                        :multiple="false"

                        :value="$postCategory->bannerImage() ? $postCategory->bannerImage()->original_path : old('banner_original_path')"

                    />

                </div>

            </div>

        </div>

    </div>



    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-footer text-right">

                    <button type="submit" class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i> Cập nhật

                    </button>

                    <a href="{{ route('admin.post-categories.index') }}" class="btn btn-secondary">

                        <i class="fas fa-arrow-left mr-1"></i> Quay lại

                    </a>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection
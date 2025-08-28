<form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">

    @csrf

    @if($category->exists)

        @method('PUT')

    @endif



    <div class="row">

        <div class="col-md-8">

            {{-- Tên và Slug --}}

            <x-form.input name="name" label="Tên danh mục" :value="$category->name" required />




            {{-- Gán thuộc tính bằng Duallistbox --}}

            <x-form.duallistbox

                name="attributes"

                label="Gán thuộc tính cho danh mục"

                :options="$attributes"

                :selected="$selectedAttributes ?? []"

            />

            

            {{-- Mô tả --}}

            <x-form.ckeditor name="meta_description" label="Mô tả ngắn" :value="$category->meta_description" />

        </div>

        <div class="col-md-4">

            {{-- Các thông tin khác không đổi --}}

            <x-form.select 

                name="cate_type" 

                id="cate_type"

                label="Loại danh mục"

                :options="[\App\Models\Category::TYPE_PHYSICS => 'Sản phẩm', \App\Models\Category::TYPE_SERVICE => 'Dịch vụ']"

                :selected="$category->cate_type"

                required

            />

            {{-- CHÚ Ý: Component này phải có id="parent_id" để JS hoạt động --}}

            <x-form.category-select

                name="parent_id" 

                label="Danh mục cha" 

                :options="$categories" 

                :selected="$category->parent_id" 

            />

            <x-form.switch name="status" label="Trạng thái" :checked="$category->status ?? true" />

            

            {{-- Upload Ảnh --}}

            <div class="form-group">

                <x-form.image-input name="image" label="Ảnh đại diện" :value="$category->image"/>

            </div>

            <div class="form-group">

                <x-form.image-input name="icon" label="Icon" :value="$category->icon"/>

            </div>
            <div class="form-group">

                <x-form.image-input name="banner" label="Banner" :value="$category->banner"/>

            </div>
            
            

        </div>

    </div>



    <div class="mt-3">

        <button type="submit" class="btn btn-primary">

            {{ $category->exists ? 'Cập nhật' : 'Lưu' }}

        </button>

        <a href="route('admin.categories.index')" class="btn btn-secondary">Hủy</a>

    </div>

</form>
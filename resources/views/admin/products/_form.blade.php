<div class="card-body">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Thông tin chung</h3></div>
                <div class="card-body">
                    <x-form.input name="name" label="Tên sản phẩm" :value="old('name', $product->name ?? '')" required/>
                    <div class="row">
                        <div class="col-6">
                            <x-form.input name="code" label="Mã sản phẩm" :value="old('code', $product->code ?? '')" />
                        </div>
                        <div class="col-6">
                            <x-form.select name="brand_id" label="Thương hiệu" :options="$brands" :selected="old('brand_id', $product->brand_id ?? null)" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <x-form.money-input name="price_discount" label="Giá bán" :value="old('price_discount', $product->price_discount ?? '')" />
                        </div>
                        <div class="col-6">
                            <x-form.money-input name="price" label="Giá so sánh" :value="old('price', $product->price ?? '')" />
                        </div>
                    </div>
                    <x-form.image-input name="image" label="Ảnh đại diện" :value="$product->image ?? ''" />
                    <x-form.image-multi-input name="gallery" label="Ảnh chi tiết" :images="$product->images ?? []" />
                    <x-form.ckeditor name="description" label="Mô tả" :value="old('description', $product->description ?? '')" />
                    <x-form.ckeditor name="content" label="Nội dung" :value="old('content', $product->content ?? '')" />
                </div>
            </div>
            
            <div class="card">
                <div class="card-header"><h3 class="card-title">Biến thể</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="has_variants" name="has_variants" value="1"
                                {{ old('has_variants', $product->has_variants ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="has_variants">Sản phẩm có nhiều biến thể</label>
                        </div>
                        <small class="text-muted">Bật để tạo tối đa 3 thuộc tính (VD: Màu, Size, Chất liệu).</small>
                    </div>

                    <section id="variants_wrap" 
                             class="{{ old('has_variants', $product->has_variants ?? false) ? '' : 'd-none' }}"
                             data-attributes='@json($attributes)'
                             @if(session()->has('errors'))
                                 data-old-input='@json(session()->getOldInput())'
                             @endif
                             @if(isset($product) && $product->has_variants)
                                 data-existing-variants='@json($product->variantsWithValues())'
                             @endif
                    >
                        <div class="variants__attr-select">
                            <label class="variants__label">Thuộc tính</label>
                            <select id="attribute-select2" class="variants__select" multiple data-placeholder="--- Chọn tối đa 3 thuộc tính ---"></select>
                            <small class="text-muted">Chọn thuộc tính → Hệ thống sinh block giá trị bên dưới.</small>
                        </div>

                        <div id="attr-value-blocks" class="variants__blocks mt-3">
                        </div>

                        <hr class="variants__hr"/>

                        <div id="bulkbar" class="variants__bulk d-none">
                            <div class="variants__bulk-row">
                                <strong>Sửa nhanh:</strong>
                                <input type="text" class="variants__bulk-input" id="bulk_price" placeholder="Giá">
                                <input type="text" class="variants__bulk-input" id="bulk_compare" placeholder="Giá so sánh">
                                <input type="number" class="variants__bulk-input" id="bulk_stock" placeholder="Tồn kho">
                                <button type="button" class="btn btn-sm btn-primary" id="bulk_apply">Áp dụng</button>
                                <button type="button" class="btn btn-sm btn-light" id="bulk_clear">Bỏ chọn</button>
                            </div>
                        </div>

                        <div class="variants__table-wrap">
                            <table class="table table-bordered table-sm" id="variants_table">
                                <thead>
                                    <tr>
                                        <th style="width:36px"><input type="checkbox" id="check_all"></th>
                                        <th style="min-width:240px">Tên biến thể</th>
                                        <th>SKU</th>
                                        <th class="text-right">Giá <span class="text-danger">*</span></th>
                                        <th class="text-right">Giá so sánh</th>
                                        <th class="text-right">Tồn kho</th>
                                        <th>Mặc định</th>
                                        <th>Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="variants_tbody">
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Thông tin bổ sung</h3></div>
                <div class="card-body">
                    <x-form.switch name="status" label="Trạng thái" :checked="old('status', $product->status ?? true)" />
                    <hr>
                    <x-form.select
                        name="category_id"
                        id="category_id"
                        label="Danh mục sản phẩm"
                        :options="$categories ?? []"
                        :selected="old('category_id', $product->category_id ?? null)"
                        required
                    />
                </div>
            </div>
        </div>
    </div>
</div>
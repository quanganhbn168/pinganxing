{{-- File: resources/views/components/form/image-picker.blade.php --}}
@props([
    'name',
    'label',
    'value' => '',
    'required' => false,
    'placeholder' => 'Chọn ảnh...',
    'help' => null,
    'type' => 'image', // 'image' hoặc 'file'
    'multiple' => false,
])

@php
    $inputId = 'input_' . str_replace(['[', ']', '.'], '_', $name);
    $previewId = 'preview_' . str_replace(['[', ']', '.'], '_', $name);
    
    // Xử lý value
    $currentValue = old(str_replace(['[', ']'], ['.', ''], $name), $value);
    
    // Nếu multiple, đảm bảo value là chuỗi JSON hoặc mảng đã convert
    // Logic giống media-input cũ để tương thích
    $displayValue = $currentValue;
    $previewImages = [];

    if ($multiple) {
        if (is_string($currentValue) && $currentValue != '') {
             // Try to decode
             $decoded = json_decode($currentValue, true);
             if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                 $previewImages = $decoded;
                 // Keep currentValue as string for input
             } else {
                 // Single string but mode multiple? Treat as array of 1
                 $previewImages = [$currentValue];
                 $currentValue = json_encode($previewImages);
             }
        } elseif (is_array($currentValue) || is_object($currentValue)) {
            if (is_object($currentValue) && method_exists($currentValue, 'toArray')) {
                $previewImages = $currentValue->toArray();
            } else {
                $previewImages = (array) $currentValue;
            }
            $currentValue = json_encode($previewImages);
        } else {
            // Null or empty
            $currentValue = '[]';
        }
    } else {
        // Single mode
        if ($type == 'image' && $currentValue) {
            $previewImages = [$currentValue];
        }
    }
@endphp

<div class="form-group image-picker-wrapper" 
     id="{{ $inputId }}_container"
     data-multiple="{{ $multiple ? 'true' : 'false' }}">
    
    <label for="{{ $inputId }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>

    <div class="input-group">
        {{-- Input chứa đường dẫn (hoặc JSON array) --}}
        <input type="text"
               name="{{ $name }}"
               id="{{ $inputId }}"
               class="form-control @error(str_replace(['[', ']'], ['.', ''], $name)) is-invalid @enderror"
               value="{{ $currentValue }}"
               placeholder="{{ $multiple ? 'Đã chọn ' . count($previewImages) . ' files' : $placeholder }}"
               {{ $multiple ? 'readonly' : '' }}>
               
        <div class="input-group-append">
            <button type="button" 
                    class="btn btn-primary btn-lfm-picker" 
                    data-input="{{ $inputId }}"
                    data-preview="{{ $previewId }}"
                    data-type="{{ $type }}"
                    data-multiple="{{ $multiple ? 1 : 0 }}">
                <i class="fas fa-folder-open"></i> {{ $multiple ? 'Thêm ảnh' : 'Chọn' }}
            </button>
            @if(!$multiple && $currentValue)
            <button type="button" 
                    class="btn btn-outline-danger btn-clear-image" 
                    data-input="{{ $inputId }}"
                    data-preview="{{ $previewId }}">
                <i class="fas fa-times"></i>
            </button>
            @endif
        </div>
    </div>

    @error(str_replace(['[', ']'], ['.', ''], $name))
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    @if($help)
        <small class="form-text text-muted">{{ $help }}</small>
    @endif

    {{-- Preview Area --}}
    @if($type == 'image')
        <div class="mt-2 media-preview-grid" id="{{ $previewId }}_wrapper" style="{{ count($previewImages) > 0 ? '' : 'display: none;' }}">
            <div class="row" id="{{ $previewId }}_list">
                @foreach($previewImages as $img)
                    @php 
                        $imgUrl = asset($img);
                        // Clean domain if double (rare but possible)
                        // Simple asset() is usually fine
                    @endphp
                    <div class="col-md-3 col-4 mb-2 preview-item position-relative">
                        <div class="img-thumbnail" style="height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8f9fa;">
                            <img src="{{ $imgUrl }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                        @if($multiple)
                            <button type="button" class="btn btn-sm btn-danger position-absolute btn-remove-item" 
                                    style="top: 5px; right: 20px;"
                                    data-url="{{ $img }}"
                                    data-input="{{ $inputId }}">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@pushOnce('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global State
    let currentInputId = null;
    let currentPreviewId = null;
    let isMultiple = false;

    // 1. Open LFM
    window.openLFM = function(inputId, previewId, type, multiple) {
        currentInputId = inputId;
        currentPreviewId = previewId;
        isMultiple = multiple == 1;
        
        // Cấu hình URL của LFM
        let route_prefix = '/laravel-filemanager';
        // Nếu multiple, LFM bản này có thể không hỗ trợ tham số 'multiple' qua URL để select nhiều
        // Nhưng ta cứ truyền type. Logic select nhiều thường nằm ở JS của LFM.
        // Tuy nhiên, callback SetUrl nhận về 1 mảng items.
        window.open(route_prefix + '?type=' + type, 'FileManager', 'width=900,height=600');
    };

    // 2. Click Handler
    document.querySelectorAll('.btn-lfm-picker').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openLFM(this.dataset.input, this.dataset.preview, this.dataset.type || 'image', this.dataset.multiple);
        });
    });

    // 3. Callback from LFM
    window.SetUrl = function(items) {
        if (!items || items.length === 0 || !currentInputId) return;

        const input = document.getElementById(currentInputId);
        const previewList = document.getElementById(currentPreviewId + '_list');
        const previewWrapper = document.getElementById(currentPreviewId + '_wrapper');

        // Helper: Clean URL
        const cleanUrl = (url) => {
            try {
                const u = new URL(url);
                return u.pathname.replace(/^\//, ''); // Bỏ slash đầu
            } catch (e) {
                return url.replace(/^\//, '');
            }
        };

        if (isMultiple) {
            // --- LOGIC MULTIPLE ---
            let currentPaths = [];
            try {
                currentPaths = JSON.parse(input.value) || [];
            } catch (e) {
                currentPaths = [];
            }
            if (!Array.isArray(currentPaths)) currentPaths = [];

            // Add new items
            items.forEach(item => {
                const path = cleanUrl(item.url);
                if (!currentPaths.includes(path)) {
                    currentPaths.push(path);
                }
            });

            // Update Input
            input.value = JSON.stringify(currentPaths);
            input.dispatchEvent(new Event('change'));

            // Re-render Preview
            renderPreviewGrid(previewList, currentPaths, currentInputId);
            if (previewWrapper) previewWrapper.style.display = 'block';

        } else {
            // --- LOGIC SINGLE ---
            const path = cleanUrl(items[0].url);
            
            // Update Input
            input.value = path;
            input.dispatchEvent(new Event('change'));

            // Update Preview (Reload page logic or DOM update)
            // Vì ta render bằng Blade loop, ở đây cần dùng JS update DOM cho single
            // Tuy nhiên format DOM của single trong code blade trên là logic foreach cho cả single/multiple?
            // Không, single loop 1 item.
            // Để đơn giản, ta dùng renderPreviewGrid luôn cho single (mảng 1 phần tử)
            renderPreviewGrid(previewList, [path], currentInputId, false);
            
            if (previewWrapper) previewWrapper.style.display = 'block';

            // Add clear btn logic specific to single (re-bind or check existence)
            ensureClearButton(input, currentInputId, currentPreviewId);
        }
    };

    // 4. Render Preview Helper
    function renderPreviewGrid(container, paths, inputId, showRemove = true) {
        if (!container) return;
        container.innerHTML = ''; // Clear

        paths.forEach(path => {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-4 mb-2 preview-item position-relative';
            
            // Asset URL logic: giả định path là từ root storage
            // Trong blade ta dùng asset(), js ta dùng / prefix
            const fullUrl = '/' + path.replace(/^\//, '');

            let html = `
                <div class="img-thumbnail" style="height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8f9fa;">
                    <img src="${fullUrl}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                </div>
            `;
            
            if (showRemove) { // Chỉ hiện nút xóa nếu là multiple hoặc logic xóa riêng
                // Single mode dùng nút clear input-group, multiple dùng nút xóa từng ảnh
                // Ở đây ta có biến global isMultiple nhưng hàm này có thể chạy khi init.
                // Check attribute container
                const isMulti = document.getElementById(inputId + '_container').dataset.multiple === 'true';
                if (isMulti) {
                     html += `
                        <button type="button" class="btn btn-sm btn-danger position-absolute btn-remove-item" 
                                style="top: 5px; right: 20px;"
                                data-url="${path}"
                                data-input="${inputId}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                }
            }
            col.innerHTML = html;
            container.appendChild(col);
        });

        // Re-bind remove buttons
        bindRemoveButtons();
    }

    // 5. Remove Item (Multiple)
    function bindRemoveButtons() {
        document.querySelectorAll('.btn-remove-item').forEach(btn => {
            btn.onclick = function() {
                const pathToRemove = this.dataset.url;
                const inputId = this.dataset.input;
                const input = document.getElementById(inputId);
                
                if (input) {
                    try {
                        let paths = JSON.parse(input.value);
                        paths = paths.filter(p => p !== pathToRemove);
                        input.value = JSON.stringify(paths);
                        input.dispatchEvent(new Event('change'));
                        
                        // Re-render
                        const previewList = document.querySelector(`#${inputId}_container .row`);
                        renderPreviewGrid(previewList, paths, inputId);
                        
                        if (paths.length === 0) {
                             document.querySelector(`#${inputId}_container .media-preview-grid`).style.display = 'none';
                        }
                    } catch(e) { console.error(e); }
                }
            };
        });
    }

    // 6. Clear Button (Single)
    function ensureClearButton(input, inputId, previewId) {
        // Chỉ cho single mode
        const wrapper = document.getElementById(inputId + '_container');
        if (wrapper && wrapper.dataset.multiple === 'true') return;

        let clearBtn = wrapper.querySelector('.btn-clear-image');
        if (!clearBtn) {
            const grp = wrapper.querySelector('.input-group-append');
            clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'btn btn-outline-danger btn-clear-image';
            clearBtn.dataset.input = inputId;
            clearBtn.dataset.preview = previewId;
            clearBtn.innerHTML = '<i class="fas fa-times"></i>';
            grp.appendChild(clearBtn);
            
            clearBtn.onclick = function() {
                input.value = '';
                input.dispatchEvent(new Event('change'));
                document.getElementById(previewId + '_wrapper').style.display = 'none';
                this.remove();
            };
        }
    }

    // Initial binding
    bindRemoveButtons();
    
    // Bind existing clear buttons
    document.querySelectorAll('.btn-clear-image').forEach(btn => {
        btn.addEventListener('click', function() {
            const inputId = this.dataset.input;
            const previewId = this.dataset.preview;
            const input = document.getElementById(inputId);
            
            if (input) {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            }
            const wrapper = document.getElementById(previewId + '_wrapper');
            if(wrapper) wrapper.style.display = 'none';
            this.remove();
        });
    });
});
</script>
@endpushOnce

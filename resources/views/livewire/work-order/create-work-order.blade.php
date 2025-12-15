<div>
    {{-- Header --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tools"></i> Tạo Phiếu Việc Mới</h1>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ session('success') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ session('error') }}
            </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row">
                    {{-- CỘT TRÁI: THÔNG TIN KHÁCH & ĐỊA ĐIỂM --}}
                    <div class="col-md-5">
                        {{-- 1. Khách hàng (Tùy chọn) --}}
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user"></i> 1. Khách hàng <small class="text-muted">(Tùy chọn)</small></h3>
                            </div>
                            <div class="card-body">
                                @if($selected_customer_id)
                                <div class="callout callout-success">
                                    <h5><i class="fas fa-user-check mr-1"></i> {{ $selected_customer_name }}</h5>
                                    <button type="button" wire:click="clearSelectedCustomer" class="btn btn-xs btn-outline-danger">
                                        <i class="fas fa-times mr-1"></i>Bỏ chọn (trở về Khách lẻ)
                                    </button>
                                </div>
                                @else
                                <div class="callout callout-info mb-3">
                                    <small><i class="fas fa-info-circle mr-1"></i> <strong>Mặc định: Khách lẻ</strong> - Hệ thống sẽ tự tạo khách hàng mới từ thông tin liên hệ bên dưới.</small>
                                </div>
                                <div class="form-group position-relative mb-0">
                                    <label class="text-muted small">Hoặc tìm khách có sẵn:</label>
                                    <input type="text" wire:model.live.debounce.300ms="search_customer" class="form-control" placeholder="Nhập tên hoặc SĐT để tìm...">
                                    @if(strlen($search_customer) > 1 && count($customers) > 0)
                                    <div class="list-group position-absolute w-100 shadow" style="z-index: 999; top: 100%;">
                                        @foreach($customers as $cus)
                                        <a href="#" wire:click.prevent="selectCustomer({{ $cus->id }}, '{{ $cus->name }}')" class="list-group-item list-group-item-action">
                                            <strong>{{ $cus->name }}</strong> - 
                                            @foreach($cus->contacts as $c) {{ $c->value }} @endforeach
                                        </a>
                                        @endforeach
                                    </div>
                                    @elseif(strlen($search_customer) > 1 && count($customers) == 0)
                                    <small class="text-muted"><i class="fas fa-search"></i> Không tìm thấy - sẽ tạo mới khi lưu</small>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. Địa điểm thi công + Thông tin liên hệ --}}
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> 2. Địa điểm & Liên hệ thi công <span class="text-danger">*</span></h3>
                            </div>
                            <div class="card-body">
                                @if(count($suggestedSites) > 0)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1"><i class="fas fa-history"></i> Lịch sử gần đây:</label>
                                        <div class="d-flex flex-wrap" style="gap: 5px;">
                                            @foreach($suggestedSites as $index => $site)
                                                <button type="button" wire:click="fillSiteInfo({{ $index }})" 
                                                        class="btn btn-xs btn-outline-secondary text-left" 
                                                        style="max-width: 100%; white-space: normal;">
                                                    <strong>{{ $site['contact_person'] }}</strong> - {{ $site['contact_phone'] }}<br>
                                                    <small>{{ \Illuminate\Support\Str::limit($site['site_address'], 30) }}</small>
                                                </button>
                                            @endforeach
                                        </div>
                                        <hr>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>Người phụ trách tại chỗ <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="contact_person" class="form-control" placeholder="VD: Anh bảo vệ, Chị giúp việc...">
                                    @error('contact_person') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>SĐT Liên hệ <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="contact_phone" class="form-control" placeholder="Số điện thoại...">
                                    @error('contact_phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Địa chỉ thi công <span class="text-danger">*</span></label>
                                    <textarea wire:model="site_address" class="form-control" rows="2" placeholder="Số nhà, tầng, tên tòa nhà..."></textarea>
                                    @error('site_address') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: THÔNG TIN CÔNG VIỆC --}}
                    <div class="col-md-7">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> 3. Thông tin công việc</h3>
                            </div>
                            <div class="card-body">
                                
                                {{-- Tags chọn loại công việc --}}
                                <div class="form-group">
                                    <label><i class="fas fa-tags text-info mr-1"></i> Loại công việc</label>
                                    <div class="d-flex flex-wrap" style="gap: 8px;">
                                        @foreach($availableTags as $tag)
                                            @php
                                                $isSelected = in_array($tag->id, $selected_tags);
                                            @endphp
                                            <span wire:click="toggleTag({{ $tag->id }})" 
                                                  wire:key="tag-{{ $tag->id }}"
                                                  class="badge p-2"
                                                  style="
                                                      background-color: {{ $isSelected ? $tag->color : '#e9ecef' }}; 
                                                      color: {{ $isSelected ? $tag->text_color : '#495057' }};
                                                      cursor: pointer;
                                                      font-size: 0.9rem;
                                                      border: 2px solid {{ $isSelected ? $tag->color : '#ced4da' }};
                                                      user-select: none;
                                                  ">
                                                @if($isSelected)
                                                    <i class="fas fa-check mr-1"></i>
                                                @endif
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">Chọn một hoặc nhiều loại</small>
                                </div>

                                {{-- Độ ưu tiên --}}
                                <div class="form-group">
                                    <label>Độ ưu tiên <span class="text-danger">*</span></label>
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-info {{ $priority == 'low' ? 'active' : '' }}">
                                            <input type="radio" wire:model="priority" value="low"> Thấp
                                        </label>
                                        <label class="btn btn-outline-primary {{ $priority == 'medium' ? 'active' : '' }}">
                                            <input type="radio" wire:model="priority" value="medium"> Trung bình
                                        </label>
                                        <label class="btn btn-outline-warning {{ $priority == 'high' ? 'active' : '' }}">
                                            <input type="radio" wire:model="priority" value="high"> Cao
                                        </label>
                                        <label class="btn btn-outline-danger {{ $priority == 'urgent' ? 'active' : '' }}">
                                            <input type="radio" wire:model="priority" value="urgent"> Gấp
                                        </label>
                                    </div>
                                </div>

                                {{-- THỜI GIAN BẮT ĐẦU --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-play-circle text-success mr-1"></i> Thời gian bắt đầu <span class="text-danger">*</span></label>
                                            <input type="datetime-local" wire:model.live="started_at" class="form-control">
                                            @error('started_at') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- DEADLINE OPTIONS --}}
                                        <div class="form-group">
                                            <label><i class="far fa-calendar-check text-info mr-1"></i> Thời hạn hoàn thành</label>
                                            <div class="btn-group btn-group-toggle w-100 mb-2" data-toggle="buttons">
                                                <label class="btn btn-sm btn-outline-secondary {{ $deadline_option == '1' ? 'active' : '' }}">
                                                    <input type="radio" wire:model.live="deadline_option" value="1"> 1 ngày
                                                </label>
                                                <label class="btn btn-sm btn-outline-secondary {{ $deadline_option == '2' ? 'active' : '' }}">
                                                    <input type="radio" wire:model.live="deadline_option" value="2"> 2 ngày
                                                </label>
                                                <label class="btn btn-sm btn-outline-secondary {{ $deadline_option == '3' ? 'active' : '' }}">
                                                    <input type="radio" wire:model.live="deadline_option" value="3"> 3 ngày
                                                </label>
                                                <label class="btn btn-sm btn-outline-secondary {{ $deadline_option == 'custom' ? 'active' : '' }}">
                                                    <input type="radio" wire:model.live="deadline_option" value="custom"> Chọn
                                                </label>
                                            </div>
                                            
                                            @if($deadline_option == 'custom')
                                            <input type="datetime-local" wire:model.live="deadline" class="form-control">
                                            @endif
                                            
                                            @if($deadline)
                                            <div class="mt-2">
                                                <span class="badge badge-info">
                                                    <i class="fas fa-clock mr-1"></i> {{ $days_count }} ngày
                                                </span>
                                                <small class="text-muted ml-2">
                                                    Deadline: {{ \Carbon\Carbon::parse($deadline)->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Gán nhân viên --}}
                                <div class="form-group" wire:ignore>
                                    <label>Đội ngũ thực hiện <span class="text-danger">*</span></label>
                                    <select id="staff-select" class="form-control select2" multiple="multiple">
                                        @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('assignee_ids') <span class="text-danger text-sm">{{ $message }}</span> @enderror

                                {{-- Tên phiếu việc --}}
                                <div class="form-group">
                                    <label>Tên phiếu việc <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="title" class="form-control" placeholder="VD: Lắp camera nhà anh Minh, Sửa điện công ty ABC...">
                                    @error('title') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>

                                {{-- Danh sách hạng mục --}}
                                <div class="form-group mt-4">
                                    <label class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-list-ul"></i> Hạng mục công việc</span>
                                        <button type="button" wire:click="addTaskRow" class="btn btn-xs btn-primary">
                                            <i class="fas fa-plus"></i> Thêm hạng mục
                                        </button>
                                    </label>
                                    
                                    <div class="border rounded p-3 bg-light">
                                        @foreach($task_list as $index => $task)
                                        <div class="card mb-3 shadow-sm" wire:key="task-{{ $index }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge badge-secondary">Hạng mục {{ $index + 1 }}</span>
                                                    @if(count($task_list) > 1)
                                                    <button type="button" wire:click="removeTaskRow({{ $index }})" class="btn btn-xs btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                                
                                                <div class="form-group mb-2">
                                                    <input type="text" wire:model="task_list.{{ $index }}.title" 
                                                        class="form-control" 
                                                        placeholder="Tên hạng mục (VD: Khảo sát, Kéo dây tầng 1, Lắp đầu ghi...)">
                                                    @error('task_list.'.$index.'.title') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                
                                                <div class="form-group mb-2">
                                                    <textarea wire:model="task_list.{{ $index }}.description" 
                                                        class="form-control form-control-sm" rows="2"
                                                        placeholder="Mô tả chi tiết (tùy chọn)..."></textarea>
                                                </div>
                                                
                                                {{-- Người thực hiện (Select2 từ assignees) --}}
                                                @if(count($assignee_ids) > 0)
                                                <div class="form-group mb-0">
                                                    <label class="text-muted small mb-1"><i class="fas fa-user-cog mr-1"></i> Người thực hiện:</label>
                                                    <select class="form-control performer-select" 
                                                            id="performer-select-{{ $index }}"
                                                            data-task-index="{{ $index }}"
                                                            multiple="multiple">
                                                        @foreach(\App\Models\Admin::whereIn('id', $assignee_ids)->get() as $staff)
                                                            <option value="{{ $staff->id }}" 
                                                                {{ in_array((string)$staff->id, $task['performer_ids'] ?? []) ? 'selected' : '' }}>
                                                                {{ $staff->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @else
                                                <small class="text-warning"><i class="fas fa-info-circle"></i> Chọn đội ngũ thực hiện trước</small>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Ghi chú --}}
                                <div class="form-group">
                                    <label>Ghi chú thêm</label>
                                    <textarea wire:model="description" class="form-control" rows="2" placeholder="Lưu ý đặc biệt, yêu cầu của khách..."></textarea>
                                </div>

                                {{-- File đính kèm (Di chuyển xuống đây) --}}
                                <div class="form-group mt-3 border-top pt-3">
                                    <label><i class="fas fa-paperclip text-secondary mr-1"></i> Tài liệu đính kèm</label>
                                    <input type="file" wire:model="attachments" multiple class="form-control-file" 
                                           accept="image/*,.pdf,.doc,.docx,.dwg,.dxf">
                                    <small class="text-muted">Ảnh, PDF, Word, CAD. Tối đa 10MB/file</small>
                                    @error('attachments.*') <span class="text-danger text-sm d-block">{{ $message }}</span> @enderror
                                    
                                    {{-- Preview files --}}
                                    @if(count($attachments) > 0)
                                    <div class="mt-2 d-flex flex-wrap" style="gap: 8px;">
                                        @foreach($attachments as $index => $file)
                                            <div class="position-relative border rounded p-2" style="min-width: 80px;">
                                                @if(str_starts_with($file->getMimeType(), 'image/'))
                                                    <img src="{{ asset('storage/livewire-tmp/' . $file->getFilename()) }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.style.display='none'">
                                                @else
                                                    <div class="text-center">
                                                        <i class="fas fa-file fa-2x text-muted"></i>
                                                        <div class="text-xs text-truncate" style="max-width: 60px;">{{ $file->getClientOriginalName() }}</div>
                                                    </div>
                                                @endif
                                                <button type="button" wire:click="removeAttachment({{ $index }})" 
                                                        class="btn btn-xs btn-danger position-absolute" 
                                                        style="top: -5px; right: -5px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    <div wire:loading wire:target="attachments" class="text-info mt-2">
                                        <i class="fas fa-spinner fa-spin"></i> Đang tải file...
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane"></i> TẠO PHIẾU
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // Staff select (đội ngũ thực hiện Work Order)
        $('#staff-select').select2({ theme: 'bootstrap4', placeholder: 'Chọn nhân viên...' });
        $('#staff-select').on('change', function (e) {
            @this.set('assignee_ids', $(this).val() || []);
        });
        @this.on('clear-select2', () => {
            $('#staff-select').val(null).trigger('change');
            // Destroy performer selects khi clear
            $('.performer-select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
        });

        // Init performer select2 sau khi Livewire update
        function initPerformerSelects() {
            $('.performer-select').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({ 
                        theme: 'bootstrap4', 
                        placeholder: 'Chọn người thực hiện...',
                        width: '100%'
                    });
                    
                    $(this).on('change', function (e) {
                        const index = $(this).data('task-index');
                        const values = $(this).val() || [];
                        @this.set('task_list.' + index + '.performer_ids', values);
                    });
                }
            });
        }

        // Init sau khi trang load
        setTimeout(initPerformerSelects, 100);
        
        // Re-init khi Livewire update DOM
        Livewire.hook('morph.updated', ({ el, component }) => {
            setTimeout(initPerformerSelects, 50);
        });

        // SweetAlert Toast
        @this.on('swal', (event) => {
            const data = event[0] || event;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.icon || 'success',
                title: data.title || 'Thông báo',
                text: data.text || '',
                showConfirmButton: false,
                timer: data.timer || 3000,
                timerProgressBar: true
            });
        });
    });
</script>
@endpush
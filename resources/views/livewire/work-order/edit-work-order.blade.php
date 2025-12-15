<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-edit"></i> Cập nhật Phiếu: <b>{{ $code }}</b></h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" wire:click="syncData" 
                            wire:confirm="Bạn có chắc muốn đồng bộ hóa lại dữ liệu cho phiếu này không? Hành động này sẽ cập nhật lại tiêu đề và nội dung báo cáo."
                            class="btn btn-warning mr-2">
                        <i class="fas fa-sync-alt"></i> Đồng bộ hóa
                    </button>
                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-default">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form wire:submit.prevent="update">
                <div class="row">
                    {{-- CỘT TRÁI: THÔNG TIN LIÊN HỆ --}}
                    <div class="col-md-5">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">1. Thông tin khách hàng</h3>
                            </div>
                            <div class="card-body">
                                {{-- Flash message --}}
                                @if(session()->has('customer_success'))
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        {{ session('customer_success') }}
                                    </div>
                                @endif

                                @if(auth('admin')->user()->hasRole('staff'))
                                    {{-- STAFF: Chỉ hiển thị thông tin khách hàng, không cho sửa --}}
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle mr-1"></i> Bạn không có quyền chỉnh sửa thông tin khách hàng.
                                    </div>
                                    <div class="form-group">
                                        <label>Tên khách hàng</label>
                                        <input type="text" class="form-control" value="{{ $customer_name }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Loại khách hàng</label>
                                        <input type="text" class="form-control" value="{{ $customer_type == 'company' ? 'Công ty' : 'Cá nhân' }}" disabled>
                                    </div>
                                @else
                                    {{-- ADMIN: Cho phép sửa thông tin khách hàng --}}
                                    {{-- Tên khách hàng --}}
                                    <div class="form-group">
                                        <label>Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="customer_name" class="form-control">
                                        @error('customer_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Loại khách --}}
                                    <div class="form-group">
                                        <label>Loại khách hàng</label>
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-info {{ $customer_type == 'individual' ? 'active' : '' }}">
                                                <input type="radio" wire:model="customer_type" value="individual"> <i class="fas fa-user mr-1"></i> Cá nhân
                                            </label>
                                            <label class="btn btn-outline-primary {{ $customer_type == 'company' ? 'active' : '' }}">
                                                <input type="radio" wire:model="customer_type" value="company"> <i class="fas fa-building mr-1"></i> Công ty
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Thông tin thêm nếu là Công ty --}}
                                    @if($customer_type == 'company')
                                    <div class="bg-light p-2 rounded border mb-3">
                                        <div class="form-group mb-2">
                                            <label class="text-xs text-muted font-weight-bold">Người đại diện</label>
                                            <input type="text" wire:model="customer_representative" class="form-control form-control-sm" placeholder="Họ tên người đại diện...">
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-0">
                                                    <label class="text-xs text-muted font-weight-bold">Mã số thuế</label>
                                                    <input type="text" wire:model="customer_tax_code" class="form-control form-control-sm" placeholder="MST...">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-0">
                                                    <label class="text-xs text-muted font-weight-bold">Email</label>
                                                    <input type="email" wire:model="customer_email" class="form-control form-control-sm" placeholder="email@...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <button type="button" wire:click="normalizeCustomer" class="btn btn-sm btn-outline-success w-100 mb-3">
                                        <i class="fas fa-save mr-1"></i> Lưu thông tin khách hàng
                                    </button>
                                @endif

                                <hr>
                                <h6 class="text-muted text-uppercase text-xs font-weight-bold mb-2">Thông tin liên hệ thi công</h6>

                                <div class="form-group">
                                    <label>Người phụ trách tại chỗ <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="contact_person" class="form-control">
                                    @error('contact_person') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label>SĐT Liên hệ <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="contact_phone" class="form-control">
                                    @error('contact_phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label>Địa chỉ thi công <span class="text-danger">*</span></label>
                                    <textarea wire:model="site_address" class="form-control" rows="3"></textarea>
                                    @error('site_address') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: THÔNG TIN CÔNG VIỆC --}}
                    <div class="col-md-7">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> 2. Thông tin công việc</h3>
                            </div>
                            <div class="card-body">
                                
                                {{-- Tags chọn loại công việc --}}
                                <div class="form-group">
                                    <label><i class="fas fa-tags text-info mr-1"></i> Loại công việc</label>
                                    <div class="d-flex flex-wrap" style="gap: 8px;">
                                        @foreach($availableTags as $tag)
                                            @php
                                                $isSelected = in_array($tag->id, $selectedTags);
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

                                {{-- THỜI GIAN BẮT ĐẦU & DEADLINE --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-play-circle text-success mr-1"></i> Thời gian bắt đầu <span class="text-danger">*</span></label>
                                            <input type="datetime-local" wire:model="started_at" class="form-control">
                                            @error('started_at') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="far fa-calendar-check text-info mr-1"></i> Thời hạn hoàn thành</label>
                                            <input type="datetime-local" wire:model="deadline" class="form-control">
                                            @error('deadline') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                            
                                            @if($deadline && $started_at)
                                            <div class="mt-2">
                                                <span class="badge badge-info">
                                                    <i class="fas fa-clock mr-1"></i> {{ max(0, \Carbon\Carbon::parse($started_at)->diffInDays(\Carbon\Carbon::parse($deadline))) }} ngày
                                                </span>
                                                <small class="text-muted ml-2">
                                                    Deadline: {{ \Carbon\Carbon::parse($deadline)->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Đội ngũ thực hiện --}}
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
                                <div class="form-group">
                                    <label class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-list-ul"></i> Hạng mục công việc <span class="text-danger">*</span></span>
                                        <button type="button" wire:click="addTask" class="btn btn-xs btn-primary">
                                            <i class="fas fa-plus"></i> Thêm hạng mục
                                        </button>
                                    </label>
                                    
                                    <div class="border rounded p-3 bg-light">
                                        @foreach($tasks as $index => $task)
                                            @if(!$task['is_deleted'])
                                            @php 
                                                $taskStatus = $task['status'];
                                                $statusEnum = \App\Enums\TaskStatus::tryFrom($taskStatus);
                                                $isLocked = in_array($taskStatus, ['completed', 'cancelled']);
                                            @endphp
                                            <div class="card mb-3 shadow-sm {{ $isLocked ? 'bg-light' : '' }}" wire:key="task-{{ $index }}">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span class="badge badge-secondary">Hạng mục {{ $loop->iteration }}</span>
                                                            @if($statusEnum)
                                                                <span class="badge badge-{{ $statusEnum->color() }} ml-1">
                                                                    {{ $statusEnum->label() }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if(!$isLocked)
                                                        <button type="button" wire:click="removeTask({{ $index }})" class="btn btn-xs btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($isLocked)
                                                    {{-- Task đã hoàn thành/hủy - chỉ hiển thị không cho sửa --}}
                                                    <div class="text-muted">
                                                        <strong>{{ $task['title'] }}</strong>
                                                        @if(!empty($task['description']))
                                                        <p class="mb-0 small">{{ $task['description'] }}</p>
                                                        @endif
                                                    </div>
                                                    @else
                                                    {{-- Task chưa hoàn thành - cho sửa --}}
                                                    <div class="form-group mb-2">
                                                        <input type="text" wire:model="tasks.{{ $index }}.title" 
                                                            class="form-control" 
                                                            placeholder="Tên hạng mục (VD: Khảo sát, Kéo dây tầng 1, Lắp đầu ghi...)">
                                                        @error("tasks.$index.title") <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                                    </div>
                                                    
                                                    <div class="form-group mb-2">
                                                        <textarea wire:model="tasks.{{ $index }}.description" 
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
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Mô tả chi tiết</label>
                                    <textarea wire:model="description" class="form-control" rows="4"></textarea>
                                </div>

                                {{-- File đính kèm --}}
                                <div class="form-group mt-3 border-top pt-3">
                                    <label><i class="fas fa-paperclip text-secondary mr-1"></i> Tài liệu đính kèm</label>
                                    
                                    {{-- Hiển thị file cũ --}}
                                    @if(count($existingAttachments) > 0)
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-2">File hiện có:</small>
                                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                                @foreach($existingAttachments as $attachment)
                                                    <div class="position-relative border rounded p-2" style="min-width: 80px;">
                                                        @if($attachment['type'] === 'image')
                                                            <a href="{{ asset('storage/' . $attachment['file_path']) }}" target="_blank">
                                                                <img src="{{ asset('storage/' . $attachment['file_path']) }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                            </a>
                                                        @else
                                                            <a href="{{ asset('storage/' . $attachment['file_path']) }}" target="_blank" class="text-center d-block">
                                                                <i class="fas fa-file fa-2x text-muted"></i>
                                                                <div class="text-xs text-truncate" style="max-width: 60px;">{{ $attachment['file_name'] }}</div>
                                                            </a>
                                                        @endif
                                                        <button type="button" wire:click="removeExistingAttachment({{ $attachment['id'] }})" 
                                                                wire:confirm="Bạn có chắc muốn xóa file này?"
                                                                class="btn btn-xs btn-danger position-absolute" 
                                                                style="top: -5px; right: -5px;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Upload file mới --}}
                                    <input type="file" wire:model="attachments" multiple class="form-control-file" 
                                           accept="image/*,.pdf,.doc,.docx,.dwg,.dxf">
                                    <small class="text-muted">Ảnh, PDF, Word, CAD. Tối đa 10MB/file</small>
                                    @error('attachments.*') <span class="text-danger text-sm d-block">{{ $message }}</span> @enderror
                                    
                                    {{-- Preview files mới --}}
                                    @if(count($attachments) > 0)
                                    <div class="mt-2 d-flex flex-wrap" style="gap: 8px;">
                                        @foreach($attachments as $index => $file)
                                            <div class="position-relative border rounded p-2 bg-light" style="min-width: 80px;">
                                                @if(str_starts_with($file->getMimeType(), 'image/'))
                                                    <img src="{{ $file->temporaryUrl() }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="text-center">
                                                        <i class="fas fa-file fa-2x text-muted"></i>
                                                        <div class="text-xs text-truncate" style="max-width: 60px;">{{ $file->getClientOriginalName() }}</div>
                                                    </div>
                                                @endif
                                                <button type="button" wire:click="removeAttachment({{ $index }})" 
                                                        class="btn btn-xs btn-warning position-absolute" 
                                                        style="top: -5px; right: -5px;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <span class="badge badge-info position-absolute" style="bottom: -5px; left: 50%; transform: translateX(-50%);">Mới</span>
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
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save"></i> CẬP NHẬT
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@push('js')
<script>
    document.addEventListener('livewire:initialized', () => {
        // 1. Khởi tạo Select2 cho staff
        let select = $('#staff-select').select2({
            theme: 'bootstrap4',
            placeholder: 'Chọn nhân viên...'
        });

        // 2. Pre-select dữ liệu cũ (QUAN TRỌNG)
        let selectedValues = @json($assignee_ids);
        select.val(selectedValues).trigger('change');

        // 3. Lắng nghe sự kiện thay đổi để cập nhật lại Livewire
        select.on('change', function (e) {
            @this.set('assignee_ids', $(this).val());
        });

        // 4. Init performer select2 sau khi Livewire update
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
                        @this.set('tasks.' + index + '.performer_ids', values);
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
    });
</script>
@endpush
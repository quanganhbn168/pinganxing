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
                        {{-- 1. Khách hàng --}}
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-user"></i> 1. Khách hàng</h3>
                                <div class="card-tools">
                                <button type="button" class="btn btn-xs {{ $is_new_customer ? 'btn-outline-primary' : 'btn-info' }}" 
                                    wire:click="toggleNewCustomer">
                                    {{ $is_new_customer ? '🔍 Đã có khách này?' : '✏️ Nhập khách mới' }}
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            {{-- Giữ nguyên phần tìm kiếm/nhập khách của anh --}}
                            @if($is_new_customer)
                            <div class="form-group">
                                <label>Tên khách <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="new_customer_name" class="form-control" placeholder="Tên khách hàng...">
                            </div>
                            <div class="form-group">
                                <label>Điện thoại <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="new_customer_phone" class="form-control" placeholder="Số điện thoại...">
                            </div>
                            <div class="form-group">
                                <label>Địa chỉ</label>
                                <textarea wire:model.live="new_customer_address" class="form-control" rows="2" placeholder="Địa chỉ..."></textarea>
                            </div>
                            @else
                            @if($selected_customer_id)
                            <div class="callout callout-success">
                                <h5>{{ $selected_customer_name }}</h5>
                                <button type="button" wire:click="clearSelectedCustomer" class="btn btn-xs btn-danger">Chọn lại</button>
                            </div>
                            @else
                            <div class="form-group position-relative">
                                <input type="text" wire:model.live.debounce.300ms="search_customer" class="form-control" placeholder="Tìm tên/SĐT...">
                                @if(strlen($search_customer) > 1 && count($customers) > 0)
                                <div class="list-group position-absolute w-100 shadow" style="z-index: 999; top: 100%;">
                                    @foreach($customers as $cus)
                                    <a href="#" wire:click.prevent="selectCustomer({{ $cus->id }}, '{{ $cus->name }}')" class="list-group-item list-group-item-action">
                                        <strong>{{ $cus->name }}</strong> - 
                                        @foreach($cus->contacts as $c) {{ $c->value }} @endforeach
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    {{-- 2. Địa điểm thi công & Liên hệ (MỚI) --}}
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> 2. Địa điểm & Liên hệ thi công</h3>
                        </div>
                        <div class="card-body">
                            {{-- GỢI Ý ĐỊA ĐIỂM CŨ --}}
                            @if(!$is_new_customer && count($suggestedSites) > 0)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1"><i class="fas fa-history"></i> Lịch sử gần đây (Chọn để điền nhanh):</label>
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
                                <label>SĐT Liên hệ tại chỗ <span class="text-danger">*</span></label>
                                <input type="text" wire:model="contact_phone" class="form-control" placeholder="Số điện thoại người nhận...">
                                @error('contact_phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Địa chỉ thi công chi tiết <span class="text-danger">*</span></label>
                                <textarea wire:model="site_address" class="form-control" rows="2" placeholder="Số nhà, tầng, tên tòa nhà..."></textarea>
                                @error('site_address') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: NỘI DUNG & NHIỆM VỤ --}}
                <div class="col-md-7">
                    <div class="card card-secondary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">3. Thông tin công việc</h3>
                        </div>
                        <div class="card-body">
                            {{-- THÊM PHẦN CHỌN ĐỘ ƯU TIÊN --}}
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
                                        <input type="radio" wire:model="priority" value="urgent"> Gấp/Cháy
                                    </label>
                                </div>
                            </div>
                            {{-- Gán thợ --}}
                            <div class="form-group" wire:ignore>
                                <label>Đội ngũ thực hiện <span class="text-danger">*</span></label>
                                <select id="staff-select" class="form-control select2" multiple="multiple">
                                    @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('assignee_ids') <span class="text-danger text-sm">{{ $message }}</span> @enderror

                            <div class="form-group">
                                <label>Tiêu đề chung <span class="text-danger">*</span></label>
                                <input type="text" wire:model="title" class="form-control">
                                @error('title') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- DANH SÁCH NHIỆM VỤ (TASK LIST) --}}
                            <div class="form-group mt-4">
                                <label class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-list-ul"></i> Danh sách các đầu việc cụ thể</span>
                                    <button type="button" wire:click="addTaskRow" class="btn btn-xs btn-primary">
                                        <i class="fas fa-plus"></i> Thêm việc
                                    </button>
                                </label>
                                
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Nội dung công việc</th>
                                            <th style="width: 50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($task_list as $index => $task)
                                        <tr>
                                            <td>
                                                <input type="text" wire:model="task_list.{{ $index }}.content" 
                                                class="form-control form-control-sm border-0" 
                                                placeholder="VD: Kéo dây tầng 1...">
                                                @error('task_list.'.$index.'.content') <span class="text-danger text-xs">Nhập nội dung</span> @enderror
                                            </td>
                                            <td class="text-center">
                                                @if(count($task_list) > 1)
                                                <button type="button" wire:click="removeTaskRow({{ $index }})" class="btn btn-xs btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group">
                                <label>Ghi chú thêm</label>
                                <textarea wire:model="description" class="form-control" rows="3"></textarea>
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
        $('#staff-select').select2({ theme: 'bootstrap4' });
        $('#staff-select').on('change', function (e) {
            @this.set('assignee_ids', $(this).val());
        });
        @this.on('clear-select2', () => {
            $('#staff-select').val(null).trigger('change');
        });

        // Listen for SweetAlert event
        @this.on('swal', (event) => {
            const data = event[0] || event; // Handle array wrapping if Livewire < 3.0 differs
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
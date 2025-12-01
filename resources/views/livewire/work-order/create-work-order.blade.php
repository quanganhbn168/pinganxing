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
                    <i class="icon fas fa-check"></i> {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="icon fas fa-ban"></i> {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row">
                    {{-- Cột Trái: KHÁCH HÀNG (Giữ nguyên code cũ của bạn) --}}
                    <div class="col-md-5">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">1. Thông tin Khách hàng</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm {{ $is_new_customer ? 'btn-default' : 'btn-success' }}" 
                                            wire:click="toggleNewCustomer">
                                        {{ $is_new_customer ? 'Quay lại tìm khách cũ' : 'Thêm khách mới' }}
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($is_new_customer)
                                    <div class="alert alert-warning text-sm">
                                        <i class="fas fa-info-circle"></i> Đang nhập thông tin cho <b>Khách hàng mới</b>.
                                    </div>
                                    <div class="form-group">
                                        <label>Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="new_customer_name" class="form-control" placeholder="Nhập tên khách...">
                                        @error('new_customer_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="new_customer_phone" class="form-control" placeholder="Nhập số điện thoại...">
                                        @error('new_customer_phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Địa chỉ</label>
                                        <textarea wire:model="new_customer_address" class="form-control" rows="2" placeholder="Số nhà, ngõ, phường..."></textarea>
                                    </div>
                                @else
                                    @if($selected_customer_id)
                                        <div class="callout callout-success">
                                            <h5>Đã chọn: {{ $selected_customer_name }}</h5>
                                            <p>Khách hàng đã có trong hệ thống.</p>
                                            <button type="button" wire:click="clearSelectedCustomer" class="btn btn-xs btn-danger">
                                                <i class="fas fa-times"></i> Chọn lại khách khác
                                            </button>
                                        </div>
                                    @else
                                        <div class="form-group position-relative">
                                            <label>Tìm kiếm khách hàng</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                </div>
                                                <input type="text" wire:model.live.debounce.300ms="search_customer" class="form-control" placeholder="Gõ tên hoặc SĐT để tìm...">
                                            </div>
                                            @error('selected_customer_id') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror

                                            @if(strlen($search_customer) > 1 && count($customers) > 0)
                                                <div class="list-group position-absolute w-100 shadow" style="z-index: 999; top: 100%;">
                                                    @foreach($customers as $cus)
                                                        <a href="#" wire:click.prevent="selectCustomer({{ $cus->id }}, '{{ $cus->name }}')" 
                                                           class="list-group-item list-group-item-action">
                                                            <div class="d-flex w-100 justify-content-between">
                                                                <h6 class="mb-1 font-weight-bold">{{ $cus->name }}</h6>
                                                            </div>
                                                            <small class="text-muted">
                                                                @foreach($cus->contacts as $contact)
                                                                    <i class="{{ $contact->type == 'phone' ? 'fas fa-phone' : 'fas fa-map-marker-alt' }}"></i> {{ $contact->value }} &nbsp;
                                                                @endforeach
                                                            </small>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @elseif(strlen($search_customer) > 1)
                                                <div class="list-group position-absolute w-100 shadow" style="z-index: 999; top: 100%;">
                                                    <div class="list-group-item text-center text-muted">
                                                        Không tìm thấy khách. <a href="#" wire:click.prevent="toggleNewCustomer">Thêm mới?</a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Cột Phải: NỘI DUNG YÊU CẦU & GÁN THỢ --}}
                    <div class="col-md-7">
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">2. Nội dung công việc</h3>
                            </div>
                            <div class="card-body">
                                
                                {{-- Phần Gán Thợ (Mới) --}}
                                <div class="form-group" wire:ignore>
                                    <label>Gán nhân viên phụ trách <span class="text-danger">*</span></label>
                                    {{-- Select2 Multi Select --}}
                                    <select id="staff-select" class="form-control select2" multiple="multiple" data-placeholder="Chọn nhân viên..." style="width: 100%;">
                                        @foreach($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('assignee_ids') <span class="text-danger text-sm d-block mb-3">{{ $message }}</span> @enderror

                                <div class="form-group">
                                    <label>Tiêu đề yêu cầu <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="title" class="form-control" placeholder="Ví dụ: Lắp camera tầng 1...">
                                    @error('title') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Mô tả chi tiết</label>
                                    <textarea wire:model="description" class="form-control" rows="5" placeholder="Ghi chú chi tiết..."></textarea>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save"></i> TẠO PHIẾU
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
        // Khởi tạo Select2
        $('#staff-select').select2({
            theme: 'bootstrap4'
        });

        // Khi Select2 thay đổi -> Bắn dữ liệu về Livewire biến assignee_ids
        $('#staff-select').on('change', function (e) {
            var data = $(this).val();
            @this.set('assignee_ids', data);
        });

        // Khi Livewire báo 'clear-select2' (sau khi save thành công) -> Xóa trắng ô chọn
        @this.on('clear-select2', () => {
            $('#staff-select').val(null).trigger('change');
        });
    });
</script>
@endpush
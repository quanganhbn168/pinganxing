<div>
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ $customer_id ? 'Cập nhật Khách hàng' : 'Thêm Khách hàng Mới' }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form wire:submit.prevent="save">
                <div class="row">
                    {{-- Cột Trái: Thông tin cơ bản --}}
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Thông tin chung</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Tên khách hàng <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control" placeholder="VD: Nguyễn Văn A">
                                    @error('name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea wire:model="notes" class="form-control" rows="3" placeholder="Ghi chú về khách..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cột Phải: Thông tin liên hệ (Dynamic) --}}
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Thông tin liên hệ (SĐT & Địa chỉ)</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach($contacts as $index => $contact)
                                            <tr>
                                                <td style="width: 40px" class="text-center align-middle">
                                                    @if($contact['type'] == 'phone')
                                                        <i class="fas fa-phone-alt text-success" title="Điện thoại"></i>
                                                    @else
                                                        <i class="fas fa-map-marker-alt text-danger" title="Địa chỉ"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" wire:model="contacts.{{ $index }}.value" class="form-control form-control-sm" placeholder="{{ $contact['type'] == 'phone' ? 'Nhập số điện thoại...' : 'Nhập địa chỉ...' }}">
                                                    @error('contacts.'.$index.'.value') <span class="text-danger text-xs">Không được để trống</span> @enderror
                                                </td>
                                                <td style="width: 150px">
                                                    <input type="text" wire:model="contacts.{{ $index }}.label" class="form-control form-control-sm" placeholder="Nhãn (VD: Nhà riêng)">
                                                </td>
                                                <td style="width: 40px" class="text-center align-middle">
                                                    <button type="button" wire:click="removeContact({{ $index }})" class="btn btn-tool text-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <button type="button" wire:click="addContact('phone')" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-plus"></i> Thêm SĐT
                                </button>
                                <button type="button" wire:click="addContact('address')" class="btn btn-sm btn-outline-danger ml-2">
                                    <i class="fas fa-plus"></i> Thêm Địa chỉ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center pb-4">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mr-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success px-5">
                            <i class="fas fa-save"></i> LƯU THÔNG TIN
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<div>
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-building"></i> Quản lý Nhà cung cấp</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#supplierModal" wire:click="resetForm">
                    <i class="fas fa-plus"></i> Thêm mới
                </button>
            </div>
        </div>
        <div class="card-body">
            {{-- Search --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="Tìm tên, mã, SĐT, email...">
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 80px;">Mã</th>
                            <th>Tên nhà cung cấp</th>
                            <th>Loại</th>
                            <th>Liên hệ</th>
                            <th class="text-center" style="width: 80px;">Trạng thái</th>
                            <th style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $s)
                            <tr>
                                <td><span class="badge badge-light border">{{ $s->code ?? '---' }}</span></td>
                                <td class="font-weight-bold">{{ $s->name }}</td>
                                <td>
                                    @if($s->typeTag)
                                        {!! $s->typeTag->badge_html !!}
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        @if($s->contact_name)<i class="fas fa-user fa-fw text-muted"></i> {{ $s->contact_name }}<br>@endif
                                        @if($s->phone)<i class="fas fa-phone fa-fw text-muted"></i> {{ $s->phone }}<br>@endif
                                        @if($s->email)<i class="fas fa-envelope fa-fw text-muted"></i> {{ $s->email }}@endif
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $s->status ? 'success' : 'secondary' }}" 
                                          wire:click="toggleStatus({{ $s->id }})" 
                                          style="cursor: pointer;">
                                        {{ $s->status ? 'Hoạt động' : 'Dừng' }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" wire:click="edit({{ $s->id }})" class="btn btn-xs btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" wire:click="delete({{ $s->id }})" 
                                            onclick="return confirm('Xác nhận xóa?')"
                                            class="btn btn-xs btn-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Chưa có nhà cung cấp nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="float-right">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Form --}}
    <div class="modal fade" id="supplierModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-building"></i> {{ $is_edit ? 'Cập nhật' : 'Thêm' }} Nhà cung cấp
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tên nhà cung cấp <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mã NCC</label>
                                    <input type="text" wire:model="code" class="form-control" placeholder="VD: NCC-001">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Loại nhà cung cấp</label>
                                    <select wire:model="type_tag_id" class="form-control">
                                        <option value="">-- Chọn loại --</option>
                                        @foreach($supplierTypes as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        <h6 class="text-muted mb-3"><i class="fas fa-address-card"></i> Thông tin liên hệ</h6>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Người liên hệ</label>
                                    <input type="text" wire:model="contact_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input type="text" wire:model="phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" wire:model="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <textarea wire:model="address" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <hr class="my-3">
                        <h6 class="text-muted mb-3"><i class="fas fa-university"></i> Thông tin thanh toán</h6>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mã số thuế</label>
                                    <input type="text" wire:model="tax_code" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Số tài khoản</label>
                                    <input type="text" wire:model="bank_account" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tên ngân hàng</label>
                                    <input type="text" wire:model="bank_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea wire:model="note" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="statusSwitch" wire:model="status">
                            <label class="custom-control-label" for="statusSwitch">Đang hoạt động</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal', (event) => {
                const data = event[0];
                Swal.fire({ title: data.title, text: data.text, icon: data.icon, confirmButtonText: 'OK' });
            });
            
            @this.on('close-modal', () => {
                $('#supplierModal').modal('hide');
            });
            
            @this.on('open-modal', () => {
                $('#supplierModal').modal('show');
            });
        });
    </script>
</div>

<div>
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Quản lý Kho Vật Tư</h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button wire:click="downloadTemplate" class="btn btn-sm btn-default" title="Tải file mẫu"><i class="fas fa-file-download"></i> File mẫu</button>
                    <button wire:click="export" class="btn btn-sm btn-success" title="Xuất dữ liệu"><i class="fas fa-file-excel"></i> Xuất Excel</button>
                    <label class="btn btn-sm btn-info mb-0" style="cursor: pointer;" title="Nhập dữ liệu">
                        <i class="fas fa-file-import"></i> Nhập Excel
                        <input type="file" wire:model="file_import" class="d-none" accept=".csv, .xlsx">
                    </label>
                </div>
            </div>
            
            <div wire:loading wire:target="file_import">
                <span class="text-primary text-sm ml-2"><i class="fas fa-spinner fa-spin"></i> Đang xử lý...</span>
            </div>
            <div wire:loading wire:target="export">
                <span class="text-success text-sm ml-2"><i class="fas fa-spinner fa-spin"></i> Đang xuất file...</span>
            </div>
        </div>
        <div class="card-body">
            {{-- Form nhập nhanh --}}
            <form wire:submit.prevent="save" class="row align-items-end mb-4 bg-light p-3 rounded mx-0">
                <div class="col-md-3">
                    <label>Mã SKU</label>
                    <input type="text" wire:model="code" class="form-control form-control-sm" placeholder="VD: CAM-01">
                </div>
                <div class="col-md-5">
                    <label>Tên vật tư <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="Tên đầy đủ...">
                </div>
                <div class="col-md-2">
                    <label>ĐVT</label>
                    <input type="text" wire:model="unit" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-primary w-100"><i class="fas fa-save"></i> Lưu</button>
                </div>
            </form>

            {{-- Danh sách --}}
            <div class="d-flex justify-content-between mb-3">
                <div class="text-muted small align-self-center">
                    Check chọn để xóa hàng loạt
                </div>
                <div style="width: 300px;">
                    <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="Tìm kiếm vật tư...">
                </div>
            </div>
            
            {{-- FORM GLOBAL ACTION --}}
            <form action="{{ route('admin.global.bulk_action') }}" method="POST" id="bulkActionForm">
                @csrf
                <input type="hidden" name="model" value="material">
                <input type="hidden" name="action" value="delete">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>Mã SKU</th>
                                <th>Tên vật tư</th>
                                <th>ĐVT</th>
                                <th style="width: 100px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $m)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $m->id }}" class="checkItem">
                                    </td>
                                    <td><span class="badge badge-light border">{{ $m->code ?? '---' }}</span></td>
                                    <td class="font-weight-bold">{{ $m->name }}</td>
                                    <td>{{ $m->unit }}</td>
                                    <td>
                                        <button type="button" wire:click="edit({{ $m->id }})" class="btn btn-xs btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Không tìm thấy dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa các mục đã chọn?')">
                         <i class="fas fa-trash"></i> Xóa đã chọn
                    </button>
                    <div class="float-right">
                        {{ $materials->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SweetAlert2 Script --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal', (event) => {
                const data = event[0]; 
                Swal.fire({
                    title: data.title,
                    text: data.html ? null : data.text,
                    html: data.html ? data.text : null, // Support HTML content
                    icon: data.icon,
                    confirmButtonText: 'OK'
                });
            });
        });

        // Script to handle 'Check All'
        document.getElementById('checkAll').onclick = function() {
            var checkboxes = document.querySelectorAll('.checkItem');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>
</div>
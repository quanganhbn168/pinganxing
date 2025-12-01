<div>
    {{-- Phần Header & Thông tin Job giữ nguyên như cũ --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h1>Phiếu việc: {{ $this->workOrder->code }}</h1>
                <a href="{{ route('admin.my-work-orders.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                {{-- Cột Trái: Thông tin khách (Giữ nguyên) --}}
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <h3 class="profile-username text-center">{{ $workOrder->customer->name }}</h3>
                            <ul class="list-group list-group-unbordered mb-3">
                                @foreach($workOrder->customer->contacts as $contact)
                                <li class="list-group-item">
                                    <b>{{ $contact->type == 'phone' ? 'Điện thoại' : 'Địa chỉ' }}</b> 
                                    <span class="float-right">{{ $contact->value }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Báo cáo --}}
                <div class="col-md-8">
                    @if(in_array($workOrder->status, ['completed', 'cancelled']))
                        {{-- Nếu đã đóng/hủy -> Hiện thông báo chặn --}}
                        <div class="alert alert-warning text-center">
                            <h4><i class="icon fas fa-lock"></i> Phiếu việc đã Đóng / Hủy</h4>
                            <p>Vui lòng liên hệ Admin mở lại phiếu nếu muốn báo cáo thêm.</p>
                        </div>
                    @else
                    <div class="mb-3">
                        <button wire:click="$toggle('showTaskForm')" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-plus-circle"></i> THÊM BÁO CÁO
                        </button>
                    </div>

                    @if($showTaskForm)
                    <div class="card card-success">
                        <div class="card-header"><h3 class="card-title">Báo cáo mới</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nội dung công việc</label>
                                <textarea wire:model="report_content" class="form-control" rows="2"></textarea>
                                @error('report_content') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- BẢNG VẬT TƯ --}}
                            <div class="form-group">
                                <label>Vật tư / Thiết bị (Quét mã vạch Serial)</label>
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tên thiết bị</th>
                                            <th style="width: 180px">Serial (Barcode/QR)</th>
                                            <th style="width: 60px">SL</th>
                                            <th style="width: 40px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $index => $item)
                                        <tr>
                                            <td>
                                                <input type="text" wire:model="items.{{ $index }}.name" class="form-control form-control-sm" placeholder="Tên...">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    {{-- Ô nhập Serial --}}
                                                    <input type="text" id="serial-input-{{ $index }}" wire:model="items.{{ $index }}.serial" class="form-control" placeholder="SN...">
                                                    <div class="input-group-append">
                                                        {{-- NÚT BẬT CAMERA: Gọi hàm JS startScan với index của dòng này --}}
                                                        <button class="btn btn-info" type="button" onclick="startScan({{ $index }})">
                                                            <i class="fas fa-qrcode"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" wire:model="items.{{ $index }}.qty" class="form-control form-control-sm text-center">
                                            </td>
                                            <td class="text-center">
                                                <button wire:click="removeItem({{ $index }})" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" wire:click="addItem" class="btn btn-sm btn-default text-primary"><i class="fas fa-plus"></i> Thêm dòng</button>
                            </div>
                            
                            {{-- Tổng tiền --}}
                            <div class="form-group">
                                <label>Thu tiền mặt</label>
                                <input type="text" wire:model="collected_amount" class="form-control" placeholder="0">
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button wire:click="saveTask" class="btn btn-success">Lưu Báo Cáo</button>
                        </div>
                    </div>
                    @endif
                    @endif
                    {{-- Timeline Lịch sử (Giữ nguyên code cũ) --}}
                    @include('livewire.work-order.partials.timeline') 
                </div>
            </div>
        </div>
    </section>

    {{-- === MODAL QUÉT MÃ VẠCH === --}}
    <div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quét Serial Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="stopScan()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    {{-- Khung hiển thị Camera --}}
                    <div id="reader" style="width: 100%;"></div>
                    <div class="p-3 text-center text-muted">
                        <small>Đưa Camera hoặc Mã vạch vào khung hình.<br>Hỗ trợ: QR Code, Code 128 (Serial), EAN...</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="stopScan()">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- === SCRIPTS XỬ LÝ QUÉT MÃ === --}}
@push('js')
{{-- Load thư viện html5-qrcode từ CDN --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    let html5QrcodeScanner = null;
    let currentScanningIndex = null; // Biến lưu xem đang quét cho dòng nào (0, 1, 2...)

    // Hàm khởi động quét
    function startScan(index) {
        currentScanningIndex = index;
        $('#scanModal').modal('show'); // Mở modal Bootstrap

        // Nếu đã bật rồi thì thôi
        if (html5QrcodeScanner) return;

        // Cấu hình Scanner
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 150 }, // Khung quét hình chữ nhật (phù hợp mã vạch dài)
            aspectRatio: 1.0
        };
        
        // Khởi tạo thư viện
        html5QrcodeScanner = new Html5Qrcode("reader");

        // Bắt đầu camera (camera sau 'environment')
        html5QrcodeScanner.start(
            { facingMode: "environment" }, 
            config, 
            onScanSuccess, 
            onScanFailure
        ).catch(err => {
            alert("Lỗi khởi động camera: " + err);
        });
    }

    // Khi quét thành công
    function onScanSuccess(decodedText, decodedResult) {
        // Phát âm thanh 'bíp' (nếu muốn)
        // new Audio('/beep.mp3').play();

        console.log(`Code scanned = ${decodedText}`, decodedResult);

        // 1. Điền giá trị vào ô input HTML để người dùng thấy ngay
        // let inputField = document.getElementById('serial-input-' + currentScanningIndex);
        // if(inputField) inputField.value = decodedText;

        // 2. Gửi dữ liệu vào Livewire Component
        // Cú pháp Livewire 3 để set biến: $wire.set('tên_biến', giá_trị)
        // Ở đây ta set vào mảng items tại vị trí index
        @this.set('items.' + currentScanningIndex + '.serial', decodedText);

        // 3. Tắt modal và dừng quét
        stopScan();
        $('#scanModal').modal('hide');
        
        // Thông báo nhỏ
        toastr.success('Đã quét: ' + decodedText);
    }

    function onScanFailure(error) {
        // Không làm gì cả để tránh spam log, vì nó gọi liên tục khi chưa bắt được mã
    }

    // Hàm dừng quét
    function stopScan() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then((ignore) => {
                html5QrcodeScanner.clear(); // Xóa khung hình
                html5QrcodeScanner = null;
            }).catch((err) => {
                console.log("Stop failed: ", err);
            });
        }
    }

    // Xử lý khi đóng modal bằng nút X hoặc click ra ngoài -> cũng phải tắt camera
    $('#scanModal').on('hidden.bs.modal', function () {
        stopScan();
    });
</script>
@endpush
<div>
    @if (session()->has('success'))
        <div class="alert alert-success mx-1 mb-3 shadow-sm border-0">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger mx-1 mb-3 shadow-sm border-0">{{ session('error') }}</div>
    @endif

    {{-- CASE 0: WORK ORDER ĐÃ KHÓA --}}
    @if($task->workOrder->isLocked())
        <div class="text-center py-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-lock text-secondary fa-5x"></i>
            </div>
            <h5 class="font-weight-bold text-dark">PHIẾU VIỆC ĐÃ ĐÓNG</h5>
            <p class="text-muted px-4 small">Phiếu việc này đã được Admin đóng hoàn thành.</p>
            <div class="alert alert-info mx-4 mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                Nếu cần bổ sung hoặc chỉnh sửa, vui lòng liên hệ Admin.
            </div>
            <a href="{{ route('admin.work-orders.show', $task->work_order_id) }}" class="btn btn-outline-primary mt-2">
                <i class="fas fa-arrow-left mr-1"></i> Về phiếu việc
            </a>
        </div>

    {{-- CASE 1: TASK ĐÃ HOÀN THÀNH --}}
    @elseif($task->status == \App\Enums\TaskStatus::COMPLETED)
        <div class="text-center py-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success fa-5x"></i>
            </div>
            <h5 class="font-weight-bold text-dark">ĐÃ HOÀN THÀNH</h5>
            <p class="text-muted px-4 small">Công việc này đã được chốt xong.</p>
            
            <button wire:click="reopenTask" 
                    wire:confirm="Bạn có chắc chắn muốn mở lại công việc này để báo cáo tiếp không?"
                    class="btn btn-outline-danger font-weight-bold mt-3 rounded-pill px-4 shadow-sm">
                <i class="fas fa-undo mr-2"></i> MỞ LẠI CÔNG VIỆC
            </button>
        </div>

    {{-- CASE 2: ĐANG LÀM => HIỆN FORM BÁO CÁO --}}
    @else
        {{-- 1. NỘI DUNG & ẢNH (BẮT BUỘC) --}}
        <div class="app-card">
            <div class="app-card-header">
                <span><i class="fas fa-edit mr-1"></i> Nội dung báo cáo <span class="text-danger">*</span></span>
            </div>
            <div class="app-card-body">
                <div class="form-group mb-3">
                    <textarea wire:model="report_content" class="form-control" rows="3" placeholder="Mô tả chi tiết công việc đã làm... (bắt buộc)"></textarea>
                    @error('report_content') <span class="text-danger text-xs mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                {{-- Upload Ảnh (BẮT BUỘC) --}}
                <div>
                    <label class="text-xs text-muted text-uppercase font-weight-bold mb-2">
                        Ảnh nghiệm thu <span class="text-danger">* (bắt buộc ít nhất 1 ảnh)</span>
                    </label>
                    <div class="d-flex flex-wrap align-items-center">
                        <label class="mb-0 mr-2 mb-2" style="cursor: pointer;">
                            <div class="d-flex align-items-center justify-content-center border rounded bg-light" style="width: 70px; height: 70px; border-style: dashed !important;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-camera fa-lg mb-1"></i><br><span style="font-size: 9px;">THÊM</span>
                                </div>
                            </div>
                            <input type="file" wire:model="proof_images" multiple accept="image/*" class="d-none">
                        </label>

                        @if ($proof_images)
                            @foreach($proof_images as $index => $image)
                                <div class="img-thumb-wrapper">
                                    <img src="{{ asset('storage/livewire-tmp/' . $image->getFilename()) }}" class="img-thumb" onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    <button wire:click="removeProofImage({{ $index }})" class="btn btn-danger btn-remove-img shadow-sm">&times;</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @error('proof_images') <span class="text-danger text-xs mt-1 d-block">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="proof_images" class="text-primary text-xs mt-1">
                        <i class="fas fa-spinner fa-spin"></i> Đang tải ảnh...
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. TÀI CHÍNH - TỔNG TIỀN & THU TIỀN --}}
        <div class="app-card border-warning">
            <div class="app-card-header bg-warning text-dark">
                <span><i class="fas fa-money-bill-wave mr-1"></i> Tài chính</span>
            </div>
            <div class="app-card-body py-3">
                {{-- TỔNG TIỀN CÔNG VIỆC (Công nợ - LUÔN NHẬP) --}}
                <div class="form-group mb-3" 
                     x-data="{ 
                        amount: @entangle('collected_amount'),
                        format(val) {
                            if (!val) return '';
                            return parseInt(val).toLocaleString('en-US');
                        },
                        input(e) {
                            let raw = e.target.value.replace(/[^0-9]/g, ''); 
                            this.amount = raw ? parseInt(raw) : 0;
                            e.target.value = this.format(this.amount);
                        }
                     }"
                     x-init="$refs.moneyInput.value = format(amount)">
                    <label class="text-sm font-weight-bold">
                        <i class="fas fa-file-invoice-dollar text-primary mr-1"></i>
                        Tổng tiền công việc
                    </label>
                    <input type="text" 
                           x-ref="moneyInput"
                           @input="input($event)"
                           class="form-control form-control-lg font-weight-bold text-primary" 
                           placeholder="Nhập tổng tiền khách phải trả...">
                    <small class="text-muted">Đây là số tiền khách phải trả (công nợ), dù đã thu hay chưa</small>
                </div>

                {{-- Form thu tiền hiện luôn nếu có tiền --}}
                @if($collected_amount > 0)
                <div class="bg-light rounded p-3 border mt-2">
                    {{-- Số tiền thực thu --}}
                    <div class="form-group mb-3" 
                         x-data="{ 
                            recv: @entangle('received_amount'),
                            format(val) {
                                if (!val) return '';
                                return parseInt(val).toLocaleString('en-US');
                            },
                            input(e) {
                                let raw = e.target.value.replace(/[^0-9]/g, ''); 
                                this.recv = raw ? parseInt(raw) : 0;
                                e.target.value = this.format(this.recv);
                            }
                         }"
                         x-init="$refs.recvInput.value = format(recv)">
                        <label class="text-sm font-weight-bold text-success">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Số tiền thực thu
                        </label>
                        <input type="text" 
                               x-ref="recvInput"
                               @input="input($event)"
                               class="form-control font-weight-bold text-success" 
                               placeholder="Nhập số tiền khách đưa...">
                        <small class="text-muted">Nhập nhỏ hơn tổng tiền nếu khách chỉ trả 1 phần</small>
                    </div>

                    <label class="text-xs text-muted font-weight-bold text-uppercase mb-2">Hình thức thanh toán <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <div class="custom-control custom-radio mr-4">
                            <input class="custom-control-input" type="radio" id="pay_cash" value="cash" wire:model="payment_method">
                            <label class="custom-control-label" for="pay_cash">
                                <i class="fas fa-money-bill-alt text-success mr-1"></i> Tiền mặt
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" type="radio" id="pay_transfer" value="transfer" wire:model="payment_method">
                            <label class="custom-control-label" for="pay_transfer">
                                <i class="fas fa-university text-info mr-1"></i> Chuyển khoản
                            </label>
                        </div>
                    </div>
                    @error('payment_method') <span class="text-danger text-xs mt-1 d-block">{{ $message }}</span> @enderror
                </div>
                @endif

            </div>
        </div>

        {{-- 4. VẬT TƯ TIÊU HAO --}}
        <div class="app-card">
            <div class="app-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box mr-1"></i> Vật tư tiêu hao</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openContinuousScanner('items')">
                    <i class="fas fa-barcode mr-1"></i> Quét liên tục
                </button>
            </div>
            <div class="app-card-body p-2">
                {{-- Header Cột --}}
                <div class="row no-gutters mb-1 px-1">
                    <div class="col-7"><small class="text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Tên vật tư / Serial</small></div>
                    <div class="col-3 text-center"><small class="text-muted font-weight-bold text-uppercase" style="font-size: 10px;">SL</small></div>
                    <div class="col-2"></div>
                </div>

                @foreach($items as $index => $item)
                    <div class="form-row mb-2 align-items-start no-gutters border-bottom pb-2">
                        {{-- Cột 1: Tên & Serial --}}
                        <div class="col-7 pr-1 position-relative"> 
                            <input type="text" 
                                   wire:model.live.debounce.300ms="items.{{ $index }}.name" 
                                   wire:blur="closeSuggestions({{ $index }})"
                                   class="form-control form-control-sm mb-1 font-weight-bold" 
                                   placeholder="Gõ tên hoặc mã tắt..." autocomplete="off">

                            {{-- Dropdown gợi ý --}}
                            @if(isset($showSuggestions[$index]) && $showSuggestions[$index] && !empty($materialSuggestions[$index]))
                                <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; top: 32px; max-height: 200px; overflow-y: auto;">
                                    @foreach($materialSuggestions[$index] as $suggestion)
                                        <button type="button" 
                                                wire:click="selectMaterial({{ $index }}, {{ $suggestion->id }})"
                                                class="list-group-item list-group-item-action p-2 text-left">
                                            <small class="d-block font-weight-bold text-dark">{{ $suggestion->name }}</small>
                                            <small class="text-muted text-xs">
                                                Mã: {{ $suggestion->code ?? '---' }}
                                            </small>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Serial --}}
                            @if(empty($items[$index]['qty']) || $items[$index]['qty'] <= 1)
                                <div class="input-group input-group-sm">
                                    <input type="text" 
                                           wire:model.live="items.{{ $index }}.serial" 
                                           class="form-control" 
                                           placeholder="Serial (nếu có)">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary px-2" type="button" onclick="openScanner({{ $index }})">
                                            <i class="fas fa-qrcode text-dark"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Cột 2: Số lượng --}}
                        <div class="col-3 px-1">
                            <input type="number" 
                                   wire:model.live="items.{{ $index }}.qty" 
                                   class="form-control form-control-sm text-center font-weight-bold mt-1" 
                                   placeholder="1"
                                   @if(!empty($items[$index]['serial'])) readonly style="background-color: #e9ecef;" @endif>
                        </div>

                        {{-- Cột 3: Xóa --}}
                        <div class="col-2 pl-1 text-right">
                            <button wire:click="removeItem({{ $index }})" class="btn btn-light btn-sm text-danger border mt-1 w-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
                
                <div class="text-center mt-2">
                    <button wire:click="addItem" class="btn btn-sm btn-link text-primary text-decoration-none">
                        <i class="fas fa-plus-circle"></i> Thêm vật tư khác
                    </button>
                </div>
            </div>
        </div>

        {{-- 5. THIẾT BỊ THU HỒI --}}
        <div class="app-card border-danger">
            <div class="app-card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-undo-alt mr-1"></i> Thiết bị thu hồi (nếu có)</span>
                <button type="button" class="btn btn-sm btn-light" onclick="openContinuousScanner('returned')">
                    <i class="fas fa-barcode mr-1"></i> Quét liên tục
                </button>
            </div>
            <div class="app-card-body p-2">
                <small class="text-muted d-block mb-2">Ghi nhận thiết bị khách mang về để bảo hành/đổi trả</small>
                
                <div class="row no-gutters mb-1 px-1">
                    <div class="col-5"><small class="text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Tên thiết bị</small></div>
                    <div class="col-4"><small class="text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Serial</small></div>
                    <div class="col-2"><small class="text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Lý do</small></div>
                    <div class="col-1"></div>
                </div>

                @foreach($returnedItems as $index => $item)
                    <div class="form-row mb-2 align-items-start no-gutters border-bottom pb-2">
                        <div class="col-5 pr-1 position-relative">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="returnedItems.{{ $index }}.name" 
                                   class="form-control form-control-sm" 
                                   placeholder="Gõ tên hoặc mã tắt..." autocomplete="off">
                            
                            {{-- Dropdown gợi ý vật tư --}}
                            @if(isset($returnedItemSuggestions[$index]) && !empty($returnedItemSuggestions[$index]))
                                <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; top: 32px; max-height: 200px; overflow-y: auto;">
                                    @foreach($returnedItemSuggestions[$index] as $suggestion)
                                        <button type="button" 
                                                wire:click="selectReturnedItemMaterial({{ $index }}, {{ $suggestion->id }})"
                                                class="list-group-item list-group-item-action p-2 text-left">
                                            <small class="d-block font-weight-bold text-dark">{{ $suggestion->name }}</small>
                                            <small class="text-muted text-xs">Mã: {{ $suggestion->code ?? '---' }}</small>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-4 px-1">
                            <div class="input-group input-group-sm">
                                <input type="text" wire:model="returnedItems.{{ $index }}.serial" 
                                       class="form-control" placeholder="Serial number">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-danger px-2" type="button" onclick="openReturnedScanner({{ $index }})">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-2 px-1">
                            <select wire:model="returnedItems.{{ $index }}.reason" class="form-control form-control-sm">
                                <option value="">--</option>
                                <option value="warranty">Bảo hành</option>
                                <option value="replace">Đổi model</option>
                                <option value="defective">Lỗi NSX</option>
                                <option value="upgrade">Nâng cấp</option>
                            </select>
                        </div>
                        <div class="col-1 text-right">
                            @if(count($returnedItems) > 1)
                            <button wire:click="removeReturnedItem({{ $index }})" class="btn btn-sm btn-link text-danger p-0">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                <div class="text-center mt-2">
                    <button wire:click="addReturnedItem" class="btn btn-sm btn-link text-danger text-decoration-none">
                        <i class="fas fa-plus-circle"></i> Thêm thiết bị thu hồi
                    </button>
                </div>
            </div>
        </div>

        {{-- 6. CHỮ KÝ --}}
        <div class="app-card">
            <div class="app-card-header">
                <span><i class="fas fa-pen-nib mr-1"></i> Khách ký xác nhận</span>
                <a href="javascript:void(0)" onclick="clearSignature()" class="text-danger text-xs font-weight-bold">XÓA KÝ LẠI</a>
            </div>
            <div class="app-card-body p-2">
                <div wire:ignore class="signature-wrapper">
                    <canvas id="signature-pad"></canvas>
                </div>
            </div>
        </div>

        {{-- FORM TẠO VIỆC TIẾP THEO ĐÃ CHUYỂN SANG MODAL RIÊNG (main.blade.php) --}}
        {{-- Sử dụng nút FAB (+) để mở modal --}}
    @endif
</div>
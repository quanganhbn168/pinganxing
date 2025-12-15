<div>
    @if (session()->has('success'))
        <div class="alert alert-success mx-1 mb-3 shadow-sm border-0">{{ session('success') }}</div>
    @endif

    {{-- ================================================= --}}
    {{-- TRƯỜNG HỢP 1: TASK ĐÃ HOÀN THÀNH => CHẶN BÁO CÁO --}}
    {{-- ================================================= --}}
    @if($task->status == 'completed')
        <div class="text-center py-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success fa-5x"></i>
            </div>
            <h5 class="font-weight-bold text-dark">ĐÃ HOÀN THÀNH</h5>
            <p class="text-muted px-4 small">Công việc này đã được chốt xong.</p>
            {{-- Removed Reopen button used by Admin --}}
        </div>

    @else
    {{-- ================================================= --}}
    {{-- TRƯỜNG HỢP 2: ĐANG LÀM => HIỆN FORM BÁO CÁO FULL --}}
    {{-- ================================================= --}}

        {{-- 1. TRẠNG THÁI HOÀN THÀNH --}}
        <div class="app-card">
            <div class="app-card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 font-weight-bold text-success">ĐÃ XONG VIỆC?</h6>
                    <small class="text-muted">Gạt nút nếu đã hoàn thành 100%</small>
                </div>
                <div class="custom-control custom-switch custom-switch-lg">
                    <input type="checkbox" class="custom-control-input" id="sw_completed" wire:model="is_task_completed">
                    <label class="custom-control-label" for="sw_completed"></label>
                </div>
            </div>
        </div>

        {{-- 2. NỘI DUNG & ẢNH --}}
        <div class="app-card">
            <div class="app-card-header"><span><i class="fas fa-edit mr-1"></i> Nội dung báo cáo</span></div>
            <div class="app-card-body">
                <div class="form-group mb-3">
                    <textarea wire:model="report_content" class="form-control" rows="3" placeholder="Mô tả chi tiết công việc..."></textarea>
                    @error('report_content') <span class="text-danger text-xs mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                {{-- Upload Ảnh --}}
                <div>
                    <label class="text-xs text-muted text-uppercase font-weight-bold mb-2">Ảnh hiện trường</label>
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
                    <div wire:loading wire:target="proof_images" class="text-primary text-xs mt-1">
                        <i class="fas fa-spinner fa-spin"></i> Đang tải ảnh...
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. TIỀN NONG (LOGIC MỚI: TOGGLE) --}}
        <div class="app-card border-warning">
            <div class="app-card-body py-3">
                
                {{-- Toggle Thu tiền --}}
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-money-bill-wave text-warning mr-1"></i> CÓ THU TIỀN KHÔNG?
                    </h6>
                    <div class="custom-control custom-switch custom-switch-lg">
                        <input type="checkbox" class="custom-control-input" id="sw_payment" wire:model.live="has_payment">
                        <label class="custom-control-label" for="sw_payment"></label>
                    </div>
                </div>

                {{-- Form Nhập tiền (Chỉ hiện khi bật toggle) --}}
                @if($has_payment)
                    <div class="mt-3 pt-3 border-top">
                        <div class="form-group mb-2">
                            <label class="text-xs text-muted font-weight-bold">Số tiền thu</label>
                            <input type="text" wire:model.live="collected_amount" class="form-control font-weight-bold text-success form-control-lg" placeholder="Nhập số tiền...">
                        </div>

                        {{-- Chỉ hiện option loại tiền khi số tiền > 0 --}}
                        @if((int)$collected_amount > 0)
                            <div class="mt-3 bg-light p-3 rounded border">
                                <label class="text-xs text-muted font-weight-bold mb-2 text-uppercase">Hình thức thanh toán</label>
                                <div class="d-flex mb-3">
                                    <div class="custom-control custom-radio mr-4">
                                        <input class="custom-control-input" type="radio" id="pay_cash" value="cash" wire:model.live="payment_method">
                                        <label class="custom-control-label" for="pay_cash">Tiền mặt</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="pay_transfer" value="transfer" wire:model.live="payment_method">
                                        <label class="custom-control-label" for="pay_transfer">Chuyển khoản</label>
                                    </div>
                                </div>

                                @if($payment_method == 'transfer')
                                    <hr class="my-2">
                                    <label class="text-xs text-muted font-weight-bold mb-2 text-uppercase">Chuyển khoản vào đâu?</label>
                                    <div class="d-flex">
                                        <div class="custom-control custom-radio mr-4">
                                            <input class="custom-control-input" type="radio" id="acc_company" value="company" wire:model="transfer_target">
                                            <label class="custom-control-label" for="acc_company">TK Công ty</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="acc_personal" value="personal" wire:model="transfer_target">
                                            <label class="custom-control-label" for="acc_personal">TK Cá nhân</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- 4. VẬT TƯ TIÊU HAO --}}
        <div class="app-card">
            <div class="app-card-header">
                <span><i class="fas fa-box mr-1"></i> Vật tư tiêu hao</span>
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
                        
                        {{-- Cột 1: Tên (Có Search) & Serial --}}
                        <div class="col-7 pr-1 position-relative"> {{-- position-relative để căn dropdown --}}
                            
                            {{-- Ô nhập tên (Có autocomplete) --}}
                            <input type="text" 
                                   wire:model.live.debounce.300ms="items.{{ $index }}.name" 
                                   wire:blur="closeSuggestions({{ $index }})"
                                   class="form-control form-control-sm mb-1 font-weight-bold" 
                                   placeholder="Gõ tên hoặc mã tắt..." autocomplete="off">

                            {{-- DANH SÁCH GỢI Ý (Dropdown) --}}
                            @if(isset($showSuggestions[$index]) && $showSuggestions[$index] && !empty($materialSuggestions[$index]))
                                <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000; top: 32px; max-height: 200px; overflow-y: auto;">
                                    @foreach($materialSuggestions[$index] as $suggestion)
                                        <button type="button" 
                                                wire:click="selectMaterial({{ $index }}, {{ $suggestion->id }})"
                                                class="list-group-item list-group-item-action p-2 text-left">
                                            <small class="d-block font-weight-bold text-dark">{{ $suggestion->name }}</small>
                                            <small class="text-muted text-xs">
                                                Mã: {{ $suggestion->code ?? '---' }} | 
                                                {{ number_format($suggestion->price) }}đ/{{ $suggestion->unit }}
                                            </small>
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Ô nhập Serial (Chỉ hiện khi SL <= 1) --}}
                            @if(empty($items[$index]['qty']) || $items[$index]['qty'] <= 1)
                                <div class="input-group input-group-sm">
                                    <input type="text" 
                                           wire:model.live="items.{{ $index }}.serial" 
                                           class="form-control" 
                                           placeholder="Serial (nếu có)">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary px-2" onclick="openScanner({{ $index }})">
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
                                   @if(!empty($items[$index]['serial'])) 
                                       readonly 
                                       style="background-color: #e9ecef; cursor: not-allowed;" 
                                   @endif
                            >
                            @if(isset($items[$index]['unit'])) 
                                <div class="text-center text-xs text-muted mt-1">{{ $items[$index]['unit'] }}</div>
                            @endif
                        </div>

                        {{-- Cột 3: Xóa --}}
                        <div class="col-2 pl-1 text-right">
                            <button wire:click="removeItem({{ $index }})" class="btn btn-light btn-sm text-danger border mt-1 w-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
                
                {{-- Nút thêm --}}
                <div class="text-center mt-2">
                    <button wire:click="addItem" class="btn btn-sm btn-link text-primary text-decoration-none">
                        <i class="fas fa-plus-circle"></i> Thêm vật tư khác
                    </button>
                </div>
            </div>
        </div>

        {{-- 5. CHỮ KÝ --}}
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
    @endif
</div>

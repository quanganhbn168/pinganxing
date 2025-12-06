<div>
    @if($task->reports->count() == 0)
        <div class="text-center py-5 mt-3 opacity-50">
            <i class="fas fa-history fa-4x text-gray mb-3"></i>
            <p class="text-muted">Chưa có lịch sử báo cáo nào.</p>
        </div>
    @else
        <div class="px-1">
            @foreach($task->reports as $rpt)
                <div class="history-card">
                    {{-- Header Card --}}
                    <div class="history-header">
                        <div>
                            <div class="font-weight-bold text-dark" style="font-size: 14px;">
                                <i class="fas fa-user-circle text-primary mr-1"></i> {{ $rpt->reporter->name ?? 'NV' }}
                            </div>
                            <div class="text-muted text-xs">{{ $rpt->created_at->format('H:i - d/m/Y') }}</div>
                        </div>
                        @if($rpt->is_completed) 
                            <span class="badge badge-success"><i class="fas fa-check"></i> Đã xong</span> 
                        @elseif(str_contains($rpt->content, 'Mở lại:'))
                            <span class="badge badge-danger"><i class="fas fa-undo"></i> Mở lại</span>
                        @endif
                    </div>

                    <div class="history-body">
                        {{-- Nội dung --}}
                        <div class="mb-2 text-dark" style="font-size: 14px; line-height: 1.5;">{{ $rpt->content }}</div>

                        {{-- Tiền nong --}}
                        @if($rpt->collected_amount > 0)
                            <div class="money-box">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="money-label">Đã thu tiền</div>
                                        <div class="money-value">{{ number_format($rpt->collected_amount) }} đ</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="badge badge-light border text-dark mb-1">
                                            {{ $rpt->payment_method == 'cash' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                        </div>
                                        @if($rpt->payment_method == 'transfer')
                                            <div class="text-xs text-muted">Về: <b>{{ $rpt->transfer_target == 'company' ? 'Công ty' : 'Cá nhân' }}</b></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Vật tư --}}
                        @if($rpt->items->count() > 0)
                            <div class="mt-3">
                                <div class="text-xs font-weight-bold text-uppercase text-muted border-bottom pb-1 mb-1">Vật tư sử dụng</div>
                                <table style="width:100%; font-size:13px">
                                    @foreach($rpt->items as $item)
                                        <tr>
                                            <td style="padding:4px 0; border-bottom:1px dashed #eee">
                                                <span class="font-weight-bold">{{ $item->item_name }}</span>
                                                @if($item->serial_number) <br><span class="badge badge-dark text-xs">{{ $item->serial_number }}</span> @endif
                                            </td>
                                            <td class="text-right" style="vertical-align:top">x{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endif

                        {{-- Ảnh --}}
                        @if($rpt->images->count() > 0)
                            <div class="mt-3">
                                <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Ảnh nghiệm thu</div>
                                <div class="image-grid">
                                    @foreach($rpt->images as $img)
                                        <img src="{{ Storage::url($img->image_path) }}" class="image-item" onclick="viewImage('{{ Storage::url($img->image_path) }}')">
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Chữ ký --}}
                        @if($rpt->customer_signature)
                            <div class="mt-3 border-top pt-2">
                                <div class="text-xs font-weight-bold text-uppercase text-muted">Chữ ký khách hàng</div>
                                <img src="{{ Storage::url($rpt->customer_signature) }}" style="max-height: 80px; border: 1px dashed #ccc; border-radius: 4px; margin-top: 5px;">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

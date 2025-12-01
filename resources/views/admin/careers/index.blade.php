@extends('layouts.admin')

@section('title', 'Quản lý Tuyển dụng')
@section('content_header', 'Tin tuyển dụng')

@push('css')
<style>
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
    .thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
    .table td { vertical-align: middle !important; }
</style>
@endpush

@section('content')
    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    {{-- BỘ LỌC --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('admin.careers.index') }}" class="row">
                <div class="col-md-4">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên vị trí/Địa điểm..." />
                </div>
                <div class="col-md-3">
                    <x-form.select name="status" label="Trạng thái" :options="['1' => 'Đang tuyển', '0' => 'Dừng tuyển']" :selected="request('status')" placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách tin tuyển dụng</h3>
            <div class="card-tools d-flex align-items-center">
                
                {{-- Component Bulk Action (Xóa nhiều) --}}
                <x-admin.bulk-action-bar model="career" />

                <a href="{{ route('admin.careers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Đăng tin mới
                </a>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                <tr>
                    <th class="text-center" width="40">
                        <input type="checkbox" id="checkAll" class="custom-checkbox">
                    </th>
                    <th width="60">#</th>
                    <th width="70">Ảnh</th>
                    <th>Vị trí tuyển dụng</th>
                    <th>Mức lương</th>
                    <th width="120">Hạn nộp</th>
                    <th class="text-center" width="100">Home</th>
                    <th class="text-center" width="100">Trạng thái</th>
                    <th class="text-center" width="120">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($careers as $key => $item)
                    @php
                        // Xử lý ảnh từ Media
                        $img = $item->image ? asset('storage/' . ltrim($item->image, '/')) : asset('images/no-image.png');
                        // Xử lý hạn nộp: Nếu quá hạn thì bôi đỏ
                        $isExpired = $item->deadline && $item->deadline->isPast();
                    @endphp
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $item->id }}">
                        </td>
                        <td>{{ $loop->index + 1 + ($careers->currentPage() - 1) * $careers->perPage() }}</td>
                        <td><img src="{{ $img }}" class="thumb"></td>
                        <td>
                            <a href="{{ route('admin.careers.edit', $item) }}" class="font-weight-bold">{{ $item->name }}</a>
                            <div class="small text-muted">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $item->location ?? 'Toàn quốc' }}
                                <span class="mx-1">|</span> 
                                <i class="fas fa-user mr-1"></i> {{ $item->quantity ?? 1 }} người
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-success">{{ $item->salary ?? 'Thỏa thuận' }}</span>
                        </td>
                        <td>
                            @if($item->deadline)
                                <span class="{{ $isExpired ? 'text-danger font-weight-bold' : '' }}">
                                    {{ $item->deadline->format('d/m/Y') }}
                                </span>
                                @if($isExpired) <br><small class="text-danger">(Đã hết hạn)</small> @endif
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="Career" :record="$item" field="is_home" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="Career" :record="$item" field="status" />
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.careers.edit', $item) }}" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="UniversalBulk.confirmOne({{ $item->id }}, 'delete')" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có tin tuyển dụng nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($careers->hasPages())
            <div class="card-footer clearfix">{{ $careers->links() }}</div>
        @endif
    </div>
@endsection
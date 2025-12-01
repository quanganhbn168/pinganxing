@props(['model'])

{{-- Container chứa nút thao tác (Mặc định ẩn, JS sẽ kích hoạt) --}}
<div id="bulkActionContainer" class="d-none mr-2">
    
    {{-- Nút Xóa --}}
    <button type="button" class="btn btn-danger btn-sm shadow-sm" onclick="UniversalBulk.confirm('delete')">
        <i class="fas fa-trash mr-1"></i> Xóa (<span id="bulkCount">0</span>)
    </button>

    {{-- Anh có thể thêm nút khác ở đây: Duyệt, Ẩn, v.v... --}}
</div>

{{-- Form Ẩn Toàn Cục --}}
<form id="universalBulkForm" action="{{ route('admin.global.bulk_action') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="model" value="{{ $model }}">
    <input type="hidden" name="action" id="universalActionInput">
    <div id="universalIdsInput"></div>
</form>
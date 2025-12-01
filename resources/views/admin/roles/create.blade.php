@extends('layouts.admin')
@section('title', 'Tạo Vai trò')
@section('content_header', 'Tạo Vai trò mới')

@section('content')
<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Tên Vai trò <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="VD: Biên tập viên, Nhân viên kho..." required>
            </div>

            <hr>
            <h5 class="mb-3 text-primary"><i class="fas fa-user-shield"></i> Phân quyền chi tiết</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 200px">Module</th>
                            {{-- Render cột hành động (Xem, Thêm, Sửa, Xóa) --}}
                            @foreach($actions as $actKey => $actLabel)
                                <th class="text-center">{{ $actLabel }}</th>
                            @endforeach
                            <th class="text-center" width="100">Chọn hết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $modKey => $modLabel)
                            <tr>
                                <td class="font-weight-bold">{{ $modLabel }}</td>
                                
                                @foreach($actions as $actKey => $actLabel)
                                    @php $permName = "{$actKey}_{$modKey}"; @endphp
                                    <td class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input perm-check row-{{ $modKey }}" 
                                                   id="perm_{{ $permName }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permName }}">
                                            <label class="custom-control-label" for="perm_{{ $permName }}"></label>
                                        </div>
                                    </td>
                                @endforeach

                                {{-- Nút check all dòng --}}
                                <td class="text-center">
                                    <input type="checkbox" class="row-checker" data-row="row-{{ $modKey }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Lưu Vai trò</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Hủy</a>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    // Script chọn hết 1 dòng
    $('.row-checker').change(function() {
        let rowClass = $(this).data('row');
        $('.' + rowClass).prop('checked', $(this).prop('checked'));
    });
</script>
@endpush
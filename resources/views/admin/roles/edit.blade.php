@extends('layouts.admin')
@section('title', 'Sửa Vai trò')
@section('content_header', 'Cập nhật Vai trò: ' . $role->name)

@section('content')
<form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Tên Vai trò <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
            </div>

            <hr>
            <h5 class="mb-3 text-primary"><i class="fas fa-user-shield"></i> Phân quyền chi tiết</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 200px">Module</th>
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
                                    @php 
                                        $permName = "{$actKey}_{$modKey}"; 
                                        $checked = in_array($permName, $rolePermissions) ? 'checked' : '';
                                    @endphp
                                    <td class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" 
                                                   class="custom-control-input perm-check row-{{ $modKey }}" 
                                                   id="perm_{{ $permName }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permName }}"
                                                   {{ $checked }}>
                                            <label class="custom-control-label" for="perm_{{ $permName }}"></label>
                                        </div>
                                    </td>
                                @endforeach

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
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Hủy</a>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    $('.row-checker').change(function() {
        let rowClass = $(this).data('row');
        $('.' + rowClass).prop('checked', $(this).prop('checked'));
    });
</script>
@endpush
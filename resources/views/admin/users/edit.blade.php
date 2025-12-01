@extends('layouts.admin')

@section('title', 'Cập nhật người dùng')
@section('content_header', 'Cập nhật: ' . $user->name)

@section('content')
<form action="{{ route('admin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        {{-- CỘT TRÁI: THÔNG TIN --}}
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thông tin tài khoản</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-tool" title="Quay lại">
                            <i class="fas fa-reply"></i>
                        </a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Địa chỉ</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $user->address) }}">
                                @error('address') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu mới <small class="text-muted font-weight-normal">(Bỏ trống nếu không đổi)</small></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" autocomplete="new-password">
                            <div class="input-group-append">
                                <button class="btn btn-default" type="button" id="btnGenPass" title="Tạo ngẫu nhiên">
                                    <i class="fas fa-random"></i>
                                </button>
                                <button class="btn btn-default" type="button" id="btnTogglePass" title="Hiện/Ẩn mật khẩu">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: PHÂN QUYỀN & SAVE --}}
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Phân quyền & Tác vụ</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Vai trò (Roles)</label>
                        <div class="p-2 border rounded" style="max-height: 200px; overflow-y: auto; background: #f8f9fa;">
                            @if($roles->count() > 0)
                                @foreach($roles as $role)
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" 
                                               id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                                               {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                                        <label for="role_{{ $role->id }}" class="custom-control-label font-weight-normal">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted small font-italic">Chưa có vai trò nào.</span>
                            @endif
                        </div>
                        @error('roles') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    
                    <hr>
                    <div class="text-muted small">
                        <i class="fas fa-clock mr-1"></i> Ngày tạo: {{ $user->created_at->format('d/m/Y H:i') }}<br>
                        <i class="fas fa-sync-alt mr-1"></i> Cập nhật: {{ $user->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // 1. Toggle Password Visibility
        $('#btnTogglePass').click(function() {
            let input = $('#password');
            let icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // 2. Generate Random Password
        $('#btnGenPass').click(function() {
            let chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let pass = "";
            for (let i = 0; i < 12; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            $('#password').val(pass);
            
            // Auto show password to copy
            if ($('#password').attr('type') === 'password') {
                $('#btnTogglePass').click();
            }
        });
    });
</script>
@endpush
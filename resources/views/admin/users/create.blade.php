@extends('layouts.admin')

@section('title', 'Thêm người dùng mới')
@section('content_header', 'Thêm người dùng mới')

@section('content')
<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    <div class="row">
        {{-- CỘT TRÁI: THÔNG TIN TÀI KHOẢN --}}
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thông tin cơ bản</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Hàng 1: Tên & Email --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Nhập họ tên" required>
                                @error('name') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email (không bắt buộc)">
                                @error('email') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Hàng 2: SĐT & Địa chỉ --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại" required>
                                @error('phone') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Địa chỉ</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address') }}" placeholder="Nhập địa chỉ">
                                @error('address') <span class="error invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Hàng 3: Mật khẩu --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" autocomplete="new-password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-default" type="button" id="generate-password-btn" title="Tạo ngẫu nhiên">
                                            <i class="fas fa-random"></i>
                                        </button>
                                        <span class="input-group-text" id="toggle-password-span" style="cursor: pointer;" title="Hiện/Ẩn">
                                            <i class="fas fa-eye" id="toggle-password-icon"></i>
                                        </span>
                                    </div>
                                    @error('password') <span class="error invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="toggle-password-confirmation-span" style="cursor: pointer;" title="Hiện/Ẩn">
                                            <i class="fas fa-eye" id="toggle-password-confirmation-icon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: PHÂN QUYỀN & TÁC VỤ --}}
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Phân quyền & Tác vụ</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Gán vai trò (Roles)</label>
                        @error('roles') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                        
                        <div class="p-2 border rounded" style="max-height: 300px; overflow-y: auto; background: #f8f9fa;">
                            @if(isset($roles) && !$roles->isEmpty())
                                @foreach($roles as $roleName)
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" 
                                               id="role-{{ $roleName->id }}" name="roles[]" value="{{ $roleName->name }}"
                                               {{ in_array($roleName, old('roles', [])) ? 'checked' : '' }}>
                                        <label for="role-{{ $roleName->id }}" class="custom-control-label font-weight-normal">
                                            {{ $roleName->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted small font-italic p-2">Không có vai trò nào (guard: web).</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Lưu người dùng
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-block mt-2">Hủy bỏ</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const generateBtn = document.getElementById('generate-password-btn');

    const toggleSpan = document.getElementById('toggle-password-span');
    const toggleIcon = document.getElementById('toggle-password-icon');
    const toggleConfirmationSpan = document.getElementById('toggle-password-confirmation-span');
    const toggleConfirmationIcon = document.getElementById('toggle-password-confirmation-icon');

    // 1. Generate Random Password
    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            const randomPassword = generateStrongPassword();
            passwordInput.value = randomPassword;
            passwordConfirmationInput.value = randomPassword;
            
            // Auto show pass to copy
            if (passwordInput.type === "password") {
                togglePasswordVisibility(passwordInput, toggleIcon);
                togglePasswordVisibility(passwordConfirmationInput, toggleConfirmationIcon);
            }
        });
    }

    // 2. Toggle Visibility
    if (toggleSpan) {
        toggleSpan.addEventListener('click', function () {
            togglePasswordVisibility(passwordInput, toggleIcon);
        });
    }
    
    if (toggleConfirmationSpan) {
        toggleConfirmationSpan.addEventListener('click', function () {
            togglePasswordVisibility(passwordConfirmationInput, toggleConfirmationIcon);
        });
    }

    function generateStrongPassword(length = 12) {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
        let password = "";
        for (let i = 0, n = charset.length; i < length; ++i) {
            password += charset.charAt(Math.floor(Math.random() * n));
        }
        return password;
    }

    function togglePasswordVisibility(inputField, iconElement) {
        if (inputField.type === "password") {
            inputField.type = "text";
            iconElement.classList.remove("fa-eye");
            iconElement.classList.add("fa-eye-slash");
        } else {
            inputField.type = "password";
            iconElement.classList.remove("fa-eye-slash");
            iconElement.classList.add("fa-eye");
        }
    }
});
</script>
@endpush
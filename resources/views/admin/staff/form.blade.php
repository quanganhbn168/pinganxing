@extends('layouts.admin')

@section('title', $staff ? 'Cập nhật Nhân viên' : 'Thêm Nhân viên Mới')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <h1>{{ $staff ? 'Cập nhật Nhân viên' : 'Thêm Nhân viên Mới' }}</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <form action="{{ $staff ? route('admin.staff.update', $staff->id) : route('admin.staff.store') }}" 
              method="POST" 
              autocomplete="off">
            @csrf
            @if($staff)
                @method('PUT')
            @endif
            
            {{-- Fake fields để chống browser auto-fill --}}
            <div style="position: absolute; top: -9999px; left: -9999px;">
                <input type="text" name="fake_username_prevention" tabindex="-1">
                <input type="password" name="fake_password_prevention" tabindex="-1">
            </div>

            <div class="row">
                {{-- Cột Trái: Thông tin đăng nhập --}}
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin tài khoản</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" 
                                       value="{{ old('name', $staff->name ?? '') }}" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="VD: Nguyễn Văn Thợ"
                                       autocomplete="off" 
                                       readonly onfocus="this.removeAttribute('readonly');">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Số điện thoại (Dùng để đăng nhập) <span class="text-danger">*</span></label>
                                <input type="text" name="phone" 
                                       value="{{ old('phone', $staff->phone ?? '') }}" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       placeholder="09xxxxxxxxx"
                                       autocomplete="off" 
                                       readonly onfocus="this.removeAttribute('readonly');">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" 
                                       value="{{ old('email', $staff->email ?? '') }}" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="email@example.com"
                                       autocomplete="off" 
                                       readonly onfocus="this.removeAttribute('readonly');">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password với Generate Random --}}
                            <div class="form-group" 
                                 x-data="{ 
                                     showPass: false,
                                     generatePass() {
                                         let chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                                         let pass = '';
                                         for (let i = 0; i < 8; i++) {
                                             pass += chars.charAt(Math.floor(Math.random() * chars.length));
                                         }
                                         this.$refs.passwordInput.value = pass;
                                         this.showPass = true;
                                     }
                                 }">
                                
                                <label>
                                    Mật khẩu 
                                    @if($staff)
                                        <small class="text-muted font-weight-normal">(Để trống nếu không đổi)</small>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                
                                <div class="input-group">
                                    <input :type="showPass ? 'text' : 'password'" 
                                           name="password"
                                           x-ref="passwordInput"
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="******"
                                           autocomplete="new-password"
                                           readonly onfocus="this.removeAttribute('readonly');">
                                    
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default" @click="generatePass()" title="Tạo mật khẩu ngẫu nhiên">
                                            <i class="fas fa-key text-warning"></i>
                                        </button>
                                        <button type="button" class="btn btn-default" @click="showPass = !showPass" title="Xem/Ẩn mật khẩu">
                                            <i class="fas" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Cột Phải: Phân quyền & Trạng thái --}}
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Phân quyền & Trạng thái</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Vai trò (Chức vụ) <span class="text-danger">*</span></label>
                                <div class="p-2 border rounded" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($roles as $r)
                                        @php
                                            $currentRole = $staff ? ($staff->roles->first()->name ?? 'staff') : 'staff';
                                            $isChecked = old('role', $currentRole) == $r->name;
                                        @endphp
                                        <div class="custom-control custom-radio mb-2">
                                            <input class="custom-control-input" 
                                                   type="radio" 
                                                   id="role_{{ $r->id }}" 
                                                   name="role" 
                                                   value="{{ $r->name }}"
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            
                                            <label for="role_{{ $r->id }}" class="custom-control-label font-weight-normal" style="cursor: pointer;">
                                                <strong>{{ $roleLabels[$r->name] ?? ucfirst($r->name) }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    @if($r->name == 'super_admin') 
                                                        Toàn quyền hệ thống.
                                                    @elseif($r->name == 'staff') 
                                                        Chỉ được xem việc được giao và báo cáo.
                                                    @else
                                                        Vai trò tùy chỉnh.
                                                    @endif
                                                </small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('role')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Trạng thái hoạt động</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="statusSwitch" 
                                           name="status"
                                           value="1"
                                           {{ old('status', $staff->status ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="statusSwitch">
                                        Đang hoạt động (Active)
                                    </label>
                                </div>
                                <small class="text-muted">Nếu tắt, nhân viên này sẽ không thể đăng nhập.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center pb-4">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary mr-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-success px-5">
                        <i class="fas fa-save"></i> LƯU THÔNG TIN
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

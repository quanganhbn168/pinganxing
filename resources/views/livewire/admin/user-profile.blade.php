<div>
    <section class="content-header">
        <div class="container-fluid">
            <h1>Hồ sơ cá nhân</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            {{-- 1. PROFILE HEADER --}}
            <div class="card card-widget widget-user">
                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username">{{ $name }}</h3>
                    <h5 class="widget-user-desc">Mã NV: {{ auth('admin')->id() }}</h5>
                </div>
                <div class="widget-user-image">
                    @if($avatar)
                        <img class="img-circle elevation-2" src="{{ asset('storage/livewire-tmp/' . $avatar->getFilename()) }}" alt="User Avatar" style="width: 90px; height: 90px; object-fit: cover;" onerror="this.src='{{ $currentAvatarUrl }}'">
                    @else
                        <img class="img-circle elevation-2" src="{{ $currentAvatarUrl }}" alt="User Avatar" style="width: 90px; height: 90px; object-fit: cover;">
                    @endif
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $stats['completed_7'] ?? 0 }}</h5>
                                <span class="description-text">VIỆC TUẦN</span>
                            </div>
                        </div>
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">{{ $stats['completed_30'] ?? 0 }}</h5>
                                <span class="description-text">VIỆC THÁNG</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="description-block">
                                <h5 class="description-header">{{ $stats['warranty_handled'] ?? 0 }}</h5>
                                <span class="description-text">BẢO HÀNH</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. EDIT INFO & SECURITY --}}
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#info" data-toggle="tab">Thông tin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab">Bảo mật</a></li>
                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Cài đặt</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        
                        {{-- TAB: INFO --}}
                        <div class="active tab-pane" id="info">
                            <form wire:submit.prevent="updateProfile">
                                <div class="form-group">
                                    <label>Họ và tên</label>
                                    <input type="text" wire:model="name" class="form-control" placeholder="Nhập họ tên">
                                    @error('name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" wire:model="email" class="form-control" placeholder="Email">
                                    @error('email') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input type="text" wire:model="phone" class="form-control" placeholder="Số điện thoại">
                                    @error('phone') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Ảnh đại diện mới</label>
                                    <div class="custom-file">
                                        <input type="file" wire:model="avatar" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">Chọn ảnh</label>
                                    </div>
                                    @error('avatar') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB: SECURITY --}}
                        <div class="tab-pane" id="security">
                            <form wire:submit.prevent="updatePassword">
                                <div class="form-group">
                                    <label>Mật khẩu hiện tại</label>
                                    <input type="password" wire:model="current_password" class="form-control">
                                    @error('current_password') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu mới</label>
                                    <input type="password" wire:model="new_password" class="form-control">
                                    @error('new_password') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Xác nhận mật khẩu mới</label>
                                    <input type="password" wire:model="new_password_confirmation" class="form-control">
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-danger">Đổi mật khẩu</button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB: SETTINGS --}}
                        <div class="tab-pane" id="settings">
                            <form wire:submit.prevent="saveSettings">
                                <h6 class="text-primary">Thông báo</h6>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" wire:model="notify_new_job" class="custom-control-input" id="notify_new_job">
                                        <label class="custom-control-label" for="notify_new_job">Có việc mới được giao</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" wire:model="notify_due_job" class="custom-control-input" id="notify_due_job">
                                        <label class="custom-control-label" for="notify_due_job">Việc sắp đến hạn / quá hạn</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" wire:model="notify_warranty" class="custom-control-input" id="notify_warranty">
                                        <label class="custom-control-label" for="notify_warranty">Yêu cầu bảo hành mới</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" wire:model="notify_sos" class="custom-control-input" id="notify_sos">
                                        <label class="custom-control-label" for="notify_sos">Thông báo khẩn cấp (SOS)</label>
                                    </div>
                                </div>

                                <h6 class="text-primary mt-4">Hiển thị</h6>
                                <div class="form-group">
                                    <label>Sắp xếp công việc mặc định</label>
                                    <select wire:model="pref_sort_job" class="form-control">
                                        <option value="priority">Ưu tiên cao nhất</option>
                                        <option value="newest">Mới nhất</option>
                                        <option value="oldest">Cũ nhất</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tab mặc định khi mở app</label>
                                    <select wire:model="pref_default_tab" class="form-control">
                                        <option value="current">Việc hiện tại</option>
                                        <option value="all">Tất cả việc</option>
                                    </select>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                                </div>
                                
                                <div class="divider"></div>
                                <div class="form-group mt-4">
                                    <button type="button" wire:click="logout" class="btn btn-danger btn-block">
                                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>



        </div>
    </section>
</div>

@extends('layouts.master')
@section('title', $career->name)

@section('content')
<div class="container py-5">
    {{-- Thông báo thành công --}}
    @if(session('success_apply'))
        <div class="alert alert-success mb-4 text-center">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success_apply') }}
        </div>
    @endif

    <div class="row">
        {{-- CỘT TRÁI: NỘI DUNG CHI TIẾT --}}
        <div class="col-lg-8">
            <h1 class="career-detail-title mb-3">{{ $career->name }}</h1>
            
            {{-- Meta data mobile (hiện ở đây cho dễ thấy trên đt) --}}
            <div class="d-block d-lg-none mb-4 p-3 bg-light rounded">
                <p class="mb-1"><strong><i class="fas fa-money-bill-wave text-success mr-2"></i> Lương:</strong> {{ $career->salary ?? 'Thỏa thuận' }}</p>
                <p class="mb-1"><strong><i class="fas fa-clock text-primary mr-2"></i> Hạn nộp:</strong> {{ $career->deadline ? $career->deadline->format('d/m/Y') : 'Không giới hạn' }}</p>
            </div>

            <div class="career-content mb-5">
                @if($career->description)
                    <div class="mb-4">
                        <h3 class="section-heading">Mô tả công việc</h3>
                        <div class="content-body">{!! $career->description !!}</div>
                    </div>
                @endif
                
                @if($career->requirement)
                    <div class="mb-4">
                        <h3 class="section-heading">Yêu cầu ứng viên</h3>
                        <div class="content-body">{!! $career->requirement !!}</div>
                    </div>
                @endif

                @if($career->benefit)
                    <div class="mb-4">
                        <h3 class="section-heading">Quyền lợi được hưởng</h3>
                        <div class="content-body">{!! $career->benefit !!}</div>
                    </div>
                @endif
            </div>

            <hr>

            {{-- FORM ỨNG TUYỂN (ID để scroll xuống) --}}
            <div id="apply-form-section" class="mt-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white font-weight-bold py-3">
                        <i class="fas fa-paper-plane mr-2"></i> ỨNG TUYỂN VỊ TRÍ NÀY
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('frontend.careers.apply', $career->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="small font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required placeholder="Nhập họ tên của bạn" value="{{ old('name') }}">
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="small font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" required placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Nhập email (không bắt buộc)" value="{{ old('email') }}">
                            </div>

                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">File CV (PDF, DOC, DOCX) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="cvFile" name="cv_file" required accept=".pdf,.doc,.docx">
                                    <label class="custom-file-label" for="cvFile">Chọn file hồ sơ...</label>
                                </div>
                                <small class="text-muted">Dung lượng tối đa 5MB.</small>
                                @error('cv_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="small font-weight-bold">Lời nhắn</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Giới thiệu ngắn gọn về bản thân...">{{ old('message') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2">
                                GỬI HỒ SƠ ỨNG TUYỂN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: SIDEBAR THÔNG TIN --}}
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold text-primary mb-3">Thông tin chung</h5>
                        
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2 d-flex">
                                <i class="fas fa-money-bill-wave text-muted mt-1 mr-3" style="width: 20px"></i>
                                <div>
                                    <span class="d-block small text-muted">Mức lương</span>
                                    <strong>{{ $career->salary ?? 'Thỏa thuận' }}</strong>
                                </div>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fas fa-users text-muted mt-1 mr-3" style="width: 20px"></i>
                                <div>
                                    <span class="d-block small text-muted">Số lượng tuyển</span>
                                    <strong>{{ $career->quantity ? $career->quantity . ' người' : 'Không giới hạn' }}</strong>
                                </div>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fas fa-briefcase text-muted mt-1 mr-3" style="width: 20px"></i>
                                <div>
                                    <span class="d-block small text-muted">Hình thức</span>
                                    <strong>{{ $career->type ?? 'Toàn thời gian' }}</strong>
                                </div>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fas fa-graduation-cap text-muted mt-1 mr-3" style="width: 20px"></i>
                                <div>
                                    <span class="d-block small text-muted">Bằng cấp</span>
                                    <strong>{{ $career->education ?? 'Không yêu cầu' }}</strong>
                                </div>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="fas fa-clock text-muted mt-1 mr-3" style="width: 20px"></i>
                                <div>
                                    <span class="d-block small text-muted">Hạn nộp hồ sơ</span>
                                    <strong class="{{ $career->deadline && $career->deadline->isPast() ? 'text-danger' : '' }}">
                                        {{ $career->deadline ? $career->deadline->format('d/m/Y') : 'Không thời hạn' }}
                                    </strong>
                                </div>
                            </li>
                        </ul>

                        <a href="#apply-form-section" class="btn btn-primary btn-block font-weight-bold">
                            ỨNG TUYỂN NGAY
                        </a>
                    </div>
                </div>

                {{-- Box hỗ trợ --}}
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="font-weight-bold">Cần hỗ trợ?</h6>
                        <p class="small text-muted mb-2">Vui lòng liên hệ phòng nhân sự:</p>
                        {{-- Dùng SettingHelper hoặc fix cứng nếu chưa có --}}
                        <p class="mb-1"><i class="fas fa-phone-alt mr-2"></i> {{ $setting->phone }}</p>
                        <p class="mb-0"><i class="fas fa-envelope mr-2"></i> {{ $setting->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Hiển thị tên file khi chọn input file (Bootstrap 4 Custom File)
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
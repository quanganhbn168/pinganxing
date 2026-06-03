@extends('frontend.dashboard.layout')

@section('title', 'Hồ sơ cá nhân')
@section('dashboard_title', 'Hồ sơ')

@section('dashboard_content')
<form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 md:p-8">
    @csrf

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
            Vui lòng kiểm tra lại thông tin hồ sơ.
        </div>
    @endif

    <div class="mb-8 flex flex-col gap-5 border-b border-gray-200 pb-8 dark:border-gray-800 md:flex-row md:items-center">
        <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-lg bg-blue-600 text-2xl font-bold text-white">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
            @else
                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name ?? 'U', 0, 1)) }}
            @endif
        </div>
        <div class="flex-1">
            <h2 class="text-lg font-bold text-gray-950 dark:text-white">Thông tin tài khoản</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cập nhật thông tin nhận hàng và đổi mật khẩu khi cần.</p>
            <input type="file" name="avatar" accept="image/*" class="mt-4 block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-blue-700 dark:text-gray-300">
            @error('avatar') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="profile-name" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Họ và tên</label>
            <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-950 dark:text-white dark:focus:ring-blue-950">
            @error('name') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-phone" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Số điện thoại</label>
            <input id="profile-phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-950 dark:text-white dark:focus:ring-blue-950">
            @error('phone') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-email" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Email</label>
            <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-950 dark:text-white dark:focus:ring-blue-950">
            @error('email') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="profile-address" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Địa chỉ</label>
            <input id="profile-address" type="text" name="address" value="{{ old('address', $user->address) }}" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-950 dark:text-white dark:focus:ring-blue-950">
            @error('address') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-8 border-t border-gray-200 pt-8 dark:border-gray-800">
        <h3 class="font-bold text-gray-950 dark:text-white">Đổi mật khẩu</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Để trống nếu anh chưa muốn đổi mật khẩu.</p>
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div>
                <label for="profile-password" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Mật khẩu mới</label>
                <input id="profile-password" type="password" name="password" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-950 dark:text-white dark:focus:ring-blue-950">
                @error('password') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="profile-password-confirmation" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Nhập lại mật khẩu</label>
                <input id="profile-password-confirmation" type="password" name="password_confirmation" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-950 dark:text-white dark:focus:ring-blue-950">
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700">
            Lưu thay đổi
            <i class="fas fa-save text-xs"></i>
        </button>
    </div>
</form>
@endsection

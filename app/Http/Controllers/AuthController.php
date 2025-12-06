<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $isAdmin = $request->is('admin') || $request->is('admin/*');
        return view($isAdmin ? 'auth.admin.login' : 'auth.client.login');
    }
    public function login(Request $request)
    {
        $isAdmin = $request->is('admin/*');
        $guard = $isAdmin ? 'admin' : 'web';
        $remember = $request->boolean('remember');

        // Lấy giá trị input (đặt tên chung là login_id cho dễ hiểu)
        $loginInput = $request->input('login_id'); 
        $password = $request->input('password');

        // --- ADMIN / STAFF LOGIN ---
        if ($isAdmin) {
            // 1. Validate: Chỉ cần không để trống
            $request->validate([
                'login_id' => 'required',
                'password' => 'required',
            ], [
                'login_id.required' => 'Vui lòng nhập Email hoặc Số điện thoại',
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]);

            // 2. Tự động nhận diện Email hay Phone
            // Hàm filter_var kiểm tra xem chuỗi có phải định dạng email không
            $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            // 3. Tạo mảng chứng thực
            $credentials = [
                $fieldType => $loginInput,
                'password' => $password
            ];
        } 
        // --- CLIENT LOGIN (Giữ nguyên hoặc áp dụng logic tương tự tùy anh) ---
        else {
            $credentials = $request->validate([
                'phone' => ['required', 'string'], // Client anh đang để fix cứng là phone
                'password' => ['required'],
            ]);
        }

        // 4. Thực hiện Login
        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            if ($isAdmin) {
                $user = Auth::guard('admin')->user();
                
                // Check Active
                if (method_exists($user, 'isActive') && !$user->isActive()) {
                    Auth::guard('admin')->logout();
                    return back()->withErrors(['login_id' => 'Tài khoản đã bị khóa.']);
                }

                // Check Role
                if ($user->hasRole('staff')) {
                    return redirect()->route('worker.jobs');
                }
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'login_id' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->only('login_id', 'remember'));
    }
    
    public function showRegisterForm()
    {
        return view('auth.client.register');
    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole('customer');
        Auth::login($user);
        return redirect()->route('home');
    }
    public function logout(Request $request)
    {
        // Check if it's admin logout based on route name or URL
        if ($request->routeIs('admin.logout') || $request->is('admin/*')) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login');
        }

        // Default to web guard
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
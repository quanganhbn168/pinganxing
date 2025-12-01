<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Page;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    public function index()
    {
        $page = Page::where('slug','tuyen-dung')->first();
        $thongdiep = Page::where('slug','thong-diep')->first();
        $careers = Career::where('status', true)
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', now()))
            ->latest('id')
            ->paginate(10);
            
        return view('frontend.careers.index', compact('careers','page','thongdiep'));
    }

    public function show(Career $career)
    {
        if (!$career->status) {
            abort(404);
        }
        return view('frontend.careers.show', compact('career'));
    }

    public function apply(Request $request, $id)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // Chỉ nhận PDF/Word, max 5MB
        ], [
            'name.required'    => 'Vui lòng nhập họ tên',
            'phone.required'   => 'Vui lòng nhập số điện thoại',
            'cv_file.required' => 'Vui lòng đính kèm CV',
            'cv_file.mimes'    => 'CV phải là định dạng PDF, DOC hoặc DOCX',
            'cv_file.max'      => 'Dung lượng CV không được quá 5MB',
        ]);

        $career = Career::findOrFail($id);

        // 2. Upload File CV
        $path = null;
        if ($request->hasFile('cv_file')) {
            // Lưu vào thư mục storage/app/public/cvs
            $path = $request->file('cv_file')->store('cvs', 'public');
        }

        // 3. Lưu vào Database
        CareerApplication::create([
            'career_id' => $career->id,
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'message'   => $request->message,
            'cv_path'   => $path,
            'status'    => 'pending'
        ]);

        // 4. Thông báo (Optional: Gửi email cho Admin ở đây)

        return back()->with('success_apply', 'Nộp hồ sơ thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');
    }
}
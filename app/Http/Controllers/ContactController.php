<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Setting;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmationToCustomer; 
use App\Mail\NewContactNotificationToAdmin; 

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::latest()->paginate(10);
        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $isNewsletter = $request->input('source') === 'newsletter';

        if ($isNewsletter) {
            $data = $request->validate([
                'email' => ['required', 'email', 'max:255', 'unique:newsletters,email'],
            ]);
            \App\Models\Newsletter::create($data);
            return back()->with('success', 'Đăng ký nhận tin thành công!');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $contact = Contact::create($data);

        // --- BẮT ĐẦU PHẦN GỬI EMAIL ---
        try {
            // Lấy email của admin từ setting
            // Chúng ta dùng cache để tối ưu, giống như trong Service Provider
            $setting = cache()->rememberForever('global_setting', function () {
                return Setting::first();
            });

            // Gửi email thông báo cho Admin (nếu setting có email)
            if ($setting && $setting->email) {
                Mail::to($setting->email)->send(new NewContactNotificationToAdmin($contact));
            }

            // Gửi email cảm ơn cho khách hàng (nếu họ có nhập email)
            if ($contact->email) {
                Mail::to($contact->email)->send(new ContactConfirmationToCustomer($contact));
            }

        } catch (\Exception $e) {
            // Nếu gửi mail lỗi, ghi log và vẫn tiếp tục, không làm gián đoạn người dùng
            logger()->error('Gửi mail liên hệ thất bại: ' . $e->getMessage());
        }
        // --- KẾT THÚC PHẦN GỬI EMAIL ---
        return redirect()->route('thank-you')->with('success', 'Gửi liên hệ thành công. Chúng tôi sẽ sớm liên hệ với bạn!');
        // return back()->with('success', 'Gửi liên hệ thành công. Chúng tôi sẽ sớm liên hệ với bạn!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $branches = Branch::where('status',1)->get();
        return view('frontend.contact',compact('branches'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Xoá liên hệ thành công.');
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|min:5',
            'assignee_ids' => 'required|array|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'started_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    // Chỉ check NGÀY >= ngày hôm nay (không check giờ)
                    $inputDate = Carbon::parse($value)->startOfDay();
                    $today = Carbon::today();
                    if ($inputDate->lt($today)) {
                        $fail('Ngày bắt đầu không được trước ngày hôm nay.');
                    }
                },
            ],
            'deadline' => [
                'required',
                'date',
                'after:started_at',
            ],
            'site_address' => 'required',
            'contact_person' => 'required',
            'contact_phone' => 'required',
            'task_list.*.content' => 'required|min:3',
            'attachments.*' => 'nullable|file|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.min' => 'Tiêu đề phải có ít nhất 5 ký tự.',
            'assignee_ids.required' => 'Phải gán ít nhất 1 nhân viên.',
            'assignee_ids.min' => 'Phải gán ít nhất 1 nhân viên.',
            'started_at.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'deadline.required' => 'Vui lòng chọn thời hạn hoàn thành.',
            'deadline.after' => 'Thời hạn phải sau thời gian bắt đầu.',
            'site_address.required' => 'Địa chỉ thi công không được để trống.',
            'contact_person.required' => 'Người liên hệ không được để trống.',
            'contact_phone.required' => 'Số điện thoại không được để trống.',
            'task_list.*.content.required' => 'Nội dung nhiệm vụ không được để trống.',
            'task_list.*.content.min' => 'Nội dung nhiệm vụ phải có ít nhất 3 ký tự.',
            'attachments.*.max' => 'File đính kèm không được vượt quá 10MB.',
        ];
    }
}

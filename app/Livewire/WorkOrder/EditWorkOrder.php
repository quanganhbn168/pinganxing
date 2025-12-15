<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WorkOrder;
use App\Models\WorkOrderAttachment;
use App\Models\Admin;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

class EditWorkOrder extends Component
{
    use WithFileUploads;

    public $workOrderId;
    public $code;
    public $customer_name;

    // Customer normalization (Admin only)
    public $customer;
    public $customer_id;
    public $customer_type; // individual, company
    public $customer_representative; // Tên người đại diện (nếu công ty)
    public $customer_email;
    public $customer_tax_code; // Mã số thuế (nếu công ty)

    // Các trường cho phép sửa
    public $title;
    public $description;
    public $priority;
    public $started_at;
    public $deadline;
    public $site_address;
    public $contact_person;
    public $contact_phone;
    public $assignee_ids = [];
    public $tasks = [];
    public $selectedTags = []; // Tags đã chọn

    // Attachments
    public $attachments = [];          // File mới upload
    public $existingAttachments = [];  // File cũ đã có

    public function mount($id)
    {
        $order = WorkOrder::with(['customer', 'assignees', 'tasks.performers', 'attachments'])->findOrFail($id);

        // 1. Chặn nếu đã hoàn thành hoặc hủy
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('admin.work-orders.index')
                ->with('error', 'Không thể chỉnh sửa phiếu đã Hoàn thành hoặc Đã hủy.');
        }

        // 2. Đổ dữ liệu cũ vào form
        $this->workOrderId = $order->id;
        $this->code = $order->code;
        
        // Customer info
        $this->customer = $order->customer;
        $this->customer_id = $order->customer_id;
        $this->customer_name = $order->customer->name;
        $this->customer_type = $order->customer->type ?? 'individual';
        $this->customer_representative = $order->customer->representative_name;
        $this->customer_email = $order->customer->email;
        $this->customer_tax_code = $order->customer->tax_code;

        $this->title = $order->title;
        $this->description = $order->description;
        $this->priority = $order->priority->value ?? $order->priority;
        $this->started_at = $order->started_at ? $order->started_at->format('Y-m-d\TH:i') : null;
        $this->deadline = $order->deadline ? $order->deadline->format('Y-m-d\TH:i') : null;
        $this->site_address = $order->site_address;
        $this->contact_person = $order->contact_person;
        $this->contact_phone = $order->contact_phone;

        // Tính days_count từ started_at và deadline
        $this->calculateDaysCount();

        // Lấy danh sách ID nhân viên đã gán
        $this->assignee_ids = $order->assignees->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // Lấy tags đã gán
        $this->selectedTags = $order->tags->pluck('id')->toArray();

        // Lấy danh sách Task với format mới
        foreach ($order->tasks as $task) {
            $this->tasks[] = [
                'id' => $task->id,
                'title' => $task->title ?: $task->report_content, // Ưu tiên title
                'description' => $task->description ?? '',
                'performer_ids' => $task->performers->pluck('id')->map(fn($id) => (string)$id)->toArray(),
                'status' => $task->status->value ?? $task->status, // Lưu string value
                'is_deleted' => false
            ];
        }

        // Lấy attachments cũ
        $this->existingAttachments = $order->attachments->toArray();
    }

    // Tính số ngày từ deadline
    protected function calculateDaysCount()
    {
        if (!$this->started_at || !$this->deadline) {
            $this->days_count = 0;
            return;
        }

        $start = \Carbon\Carbon::parse($this->started_at);
        $end = \Carbon\Carbon::parse($this->deadline);
        $this->days_count = max(0, $start->diffInDays($end, false));
    }

    public function addTask()
    {
        // Auto chọn staff từ assignees
        $staffIds = $this->getStaffIdsFromAssignees();
        
        $this->tasks[] = [
            'id' => null,
            'title' => '',
            'description' => '',
            'performer_ids' => $staffIds,
            'status' => 'pending',
            'is_deleted' => false
        ];
    }

    // Helper: Lấy staff IDs từ assignees
    protected function getStaffIdsFromAssignees(): array
    {
        if (empty($this->assignee_ids)) return [];
        
        return Admin::whereIn('id', $this->assignee_ids)
            ->whereHas('roles', fn($q) => $q->where('name', 'staff'))
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
    }

    // Khi thay đổi assignees, auto select staff cho tasks mới
    public function updatedAssigneeIds()
    {
        $staffIds = $this->getStaffIdsFromAssignees();
        
        // Chỉ update performer_ids cho task mới (chưa có ID)
        foreach ($this->tasks as $i => $task) {
            if (empty($task['id'])) {
                $this->tasks[$i]['performer_ids'] = $staffIds;
            }
        }
    }

    public function removeTask($index)
    {
        if (empty($this->tasks[$index]['id'])) {
            unset($this->tasks[$index]);
            $this->tasks = array_values($this->tasks);
        } else {
            $this->tasks[$index]['is_deleted'] = true;
        }
    }

    /**
     * Toggle tag selection
     */
    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$tagId]));
        } else {
            $this->selectedTags[] = $tagId;
        }
    }

    /**
     * Xóa file đính kèm mới (chưa lưu)
     */
    public function removeAttachment($index)
    {
        array_splice($this->attachments, $index, 1);
    }

    /**
     * Xóa file đính kèm cũ (đã lưu trong DB)
     */
    public function removeExistingAttachment($attachmentId)
    {
        $attachment = WorkOrderAttachment::find($attachmentId);
        if ($attachment) {
            // Xóa file vật lý
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            
            // Cập nhật danh sách hiển thị
            $this->existingAttachments = array_filter(
                $this->existingAttachments, 
                fn($a) => $a['id'] !== $attachmentId
            );
        }
    }

    /**
     * CHUẨN HÓA THÔNG TIN KHÁCH HÀNG (Admin Only)
     */
    public function normalizeCustomer()
    {
        // Block staff from editing customer info
        if (auth('admin')->user()->hasRole('staff')) {
            session()->flash('error', 'Bạn không có quyền chỉnh sửa thông tin khách hàng.');
            return;
        }

        $this->validate([
            'customer_name' => 'required|min:2',
            'customer_type' => 'required|in:individual,company',
        ], [
            'customer_name.required' => 'Vui lòng nhập tên khách hàng.',
        ]);

        // Update customer
        $this->customer->update([
            'name' => $this->customer_name,
            'type' => $this->customer_type,
            'representative_name' => $this->customer_representative,
            'email' => $this->customer_email,
            'tax_code' => $this->customer_tax_code,
        ]);

        // Sync contact_phone & site_address vào CustomerContact nếu chưa có
        $existingPhone = $this->customer->contacts()->where('type', 'phone')->where('value', $this->contact_phone)->first();
        if (!$existingPhone && $this->contact_phone) {
            $this->customer->contacts()->create([
                'type' => 'phone',
                'value' => $this->contact_phone,
                'label' => 'Liên hệ từ phiếu việc',
            ]);
        }

        $existingAddress = $this->customer->contacts()->where('type', 'address')->where('value', $this->site_address)->first();
        if (!$existingAddress && $this->site_address) {
            $this->customer->contacts()->create([
                'type' => 'address',
                'value' => $this->site_address,
                'label' => 'Địa điểm từ phiếu việc',
            ]);
        }

        session()->flash('customer_success', 'Cập nhật thông tin khách hàng thành công!');
    }

    public function update()
    {
        return $this->performUpdate("Đã cập nhật phiếu {$this->code} thành công!");
    }

    public function syncData()
    {
        return $this->performUpdate("Đã đồng bộ dữ liệu phiếu {$this->code} thành công! Các lỗi hiển thị đã được sửa.");
    }

    protected function performUpdate($successMessage)
    {
        $this->validate([
            'title' => 'required|min:5',
            'priority' => 'required|in:low,medium,high,urgent',
            'site_address' => 'required',
            'contact_person' => 'required',
            'contact_phone' => 'required',
            'assignee_ids' => 'array',
            'tasks.*.title' => 'required_unless:tasks.*.is_deleted,true',
            'tasks.*.performer_ids' => 'array',
        ]);

        $order = WorkOrder::find($this->workOrderId);

        // Cập nhật thông tin chính
        $order->update([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'started_at' => $this->started_at,
            'deadline' => $this->deadline,
            'site_address' => $this->site_address,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
        ]);

        // Cập nhật danh sách nhân viên
        $order->assignees()->sync($this->assignee_ids);

        // Cập nhật tags
        $order->tags()->sync($this->selectedTags);

        // Xử lý Tasks
        // Lấy ID người thực hiện mặc định (Leader)
        $mainPerformer = $this->assignee_ids[0] ?? auth('admin')->id();

        foreach ($this->tasks as $taskData) {
            if ($taskData['is_deleted']) {
                // Xóa task cũ
                if ($taskData['id']) {
                    \App\Models\Task::destroy($taskData['id']);
                }
                continue;
            }

            $performerIds = $taskData['performer_ids'] ?? [];
            $defaultPerformer = $performerIds[0] ?? $mainPerformer;

            if ($taskData['id']) {
                // Update task cũ
                $task = \App\Models\Task::find($taskData['id']);
                if ($task) {
                    $task->update([
                        'title' => $taskData['title'],
                        'description' => $taskData['description'] ?? null,
                        'report_content' => $taskData['title'], // Sync
                        'performer_id' => $defaultPerformer,
                    ]);
                    $task->performers()->sync($performerIds);
                }
            } else {
                // Tạo task mới
                $task = \App\Models\Task::create([
                    'work_order_id' => $order->id,
                    'performer_id' => $defaultPerformer,
                    'title' => $taskData['title'],
                    'description' => $taskData['description'] ?? null,
                    'report_content' => $taskData['title'],
                    'status' => 'pending',
                    'collected_amount' => 0,
                    'is_paid' => false
                ]);
                $task->performers()->sync($performerIds);
            }
        }

        // Lưu file đính kèm mới
        foreach ($this->attachments as $file) {
            $path = $file->store('work-orders/' . $order->code, 'public');
            $isImage = str_starts_with($file->getMimeType(), 'image/');
            
            WorkOrderAttachment::create([
                'work_order_id' => $order->id,
                'type' => $isImage ? 'image' : 'document',
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'uploaded_by' => auth('admin')->id(),
            ]);
        }

        session()->flash('success', $successMessage);
        return redirect()->route('admin.work-orders.index');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        // Lấy list nhân viên để đổ vào Select2
        $staffs = Admin::all();
        
        // Lấy danh sách tags cho Work Order
        $availableTags = Tag::forWorkOrders()->ordered()->get();
        
        return view('livewire.work-order.edit-work-order', [
            'staffs' => $staffs,
            'assignee_ids' => $this->assignee_ids,
            'availableTags' => $availableTags,
        ]);
    }
}
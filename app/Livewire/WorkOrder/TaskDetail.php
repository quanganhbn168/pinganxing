<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Task;
use App\Models\TaskReport;
use App\Models\TaskItem;
use App\Models\TaskImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskDetail extends Component
{
    use WithFileUploads;

    public $task; 
    public $activeTab = 'new_report'; 

    // --- FORM DATA ---
    public $report_content;
    public $is_task_completed = false;
    
    // TIỀN NONG (ĐƠN GIẢN HÓA)
    public $collected_amount = 0;
    public $payment_method = 'cash'; 

    public $items = [['name' => '', 'serial' => '', 'qty' => 1]]; 
    public $proof_images = [];
    public $signature_data;

    // THIẾT BỊ THU HỒI (Bảo hành)
    public $returnedItems = [['name' => '', 'serial' => '', 'reason' => '']];

    // TASK PHÁT SINH / SPAWN
    public $newTaskTitle = '';
    public $newTaskScheduledAt = '';
    public $newTaskAssigneeId = '';

    // BIẾN MỚI CHO SUGGESTION
    public $materialSuggestions = []; // Danh sách gợi ý trả về
    public $showSuggestions = [];     // Kiểm soát việc hiện dropdown cho từng dòng (dạng [index => true/false])
    
    public function mount($id)
    {
        $this->task = Task::with([
            'workOrder.customer', 
            'workOrder.tags',
            'reports.images', 
            'reports.items', 
            'reports.returnedItems',
            'reports.reporter'
        ])->findOrFail($id);
        
        // Nếu WorkOrder đã hoàn thành HOẶC task đã xong -> vào xem lịch sử
        if($this->task->workOrder->isLocked() || $this->task->status === \App\Enums\TaskStatus::COMPLETED) {
            $this->activeTab = 'history';
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        // Chỉ init chữ ký nếu task chưa xong và đang ở tab báo cáo
        if($tab == 'new_report' && $this->task->status !== \App\Enums\TaskStatus::COMPLETED) {
            $this->dispatch('init-signature');
        }
    }

    // --- LOGIC MỞ LẠI TASK (Dành cho Admin) ---
    public function reopenTask()
    {
        // Có thể check quyền Super Admin ở đây nếu cần
        // if(!auth('admin')->user()->isSuperAdmin()) abort(403);

        $this->task->update(['status' => \App\Enums\TaskStatus::PROCESSING]);
        $this->is_task_completed = false; // Reset toggle hoàn thành
        $this->activeTab = 'new_report'; // Chuyển ngay về tab báo cáo để làm tiếp
        
        session()->flash('success', 'Đã mở lại công việc này. Vui lòng báo cáo tiếp.');
        return redirect()->route('admin.tasks.detail', $this->task->id);
    }
    
    // ... (skip unchanged code) ...

    public function saveReport()
    {
        // CHẶN BÁO CÁO NẾU WORK ORDER ĐÃ HOÀN THÀNH (ưu tiên cao nhất)
        if(!$this->task->workOrder->allowsReporting()) {
            session()->flash('error', 'Phiếu việc này đã được Admin đóng hoàn thành. Nếu cần báo cáo thêm, vui lòng liên hệ Admin.');
            return;
        }

        // CHẶN BÁO CÁO NẾU TASK ĐÃ XONG
        if($this->task->status === \App\Enums\TaskStatus::COMPLETED) {
            session()->flash('error', 'Công việc này đã hoàn thành. Vui lòng mở lại nếu cần báo cáo thêm.');
            return;
        }

        $this->validate([
            'report_content' => 'required|min:5',
            'proof_images' => 'required|array|min:1',
            'proof_images.*' => 'image|max:10240',
            'collected_amount' => 'numeric|min:0',
        ], [
            'report_content.required' => 'Vui lòng nhập nội dung báo cáo.',
            'report_content.min' => 'Nội dung phải có ít nhất 5 ký tự.',
            'proof_images.required' => 'Vui lòng chụp ít nhất 1 ảnh nghiệm thu.',
            'proof_images.min' => 'Vui lòng chụp ít nhất 1 ảnh nghiệm thu.',
        ]);

        DB::transaction(function () {
            // 1. Chữ ký
            $signaturePath = null;
            if ($this->signature_data) {
                $image_parts = explode(";base64,", $this->signature_data);
                if (count($image_parts) >= 2) {
                    $fileName = 'signatures/sig_' . time() . '_' . Str::random(10) . '.png';
                    Storage::disk('public')->put($fileName, base64_decode($image_parts[1]));
                    $signaturePath = $fileName;
                }
            }

            // 2. Xử lý Tiền nong (ĐƠN GIẢN: nếu amount > 0 thì có thu tiền)
            $hasPayment = (int) $this->collected_amount > 0;

            // 3. Tạo Report
            $report = TaskReport::create([
                'task_id' => $this->task->id,
                'reporter_id' => auth('admin')->id(),
                'content' => $this->report_content,
                'is_completed' => $this->is_task_completed,
                
                'collected_amount' => $hasPayment ? $this->collected_amount : 0,
                'payment_method' => $hasPayment ? $this->payment_method : null,
                'transfer_target' => null, // Đã bỏ chi tiết TK
                
                'customer_signature' => $signaturePath,
            ]);

            // 4. Ảnh
            foreach ($this->proof_images as $photo) {
                $path = $photo->store('reports/' . date('Y-m'), 'public');
                TaskImage::create(['task_report_id' => $report->id, 'image_path' => $path]);
            }

            // 5. Vật tư
            foreach ($this->items as $item) {
                if (!empty($item['name'])) {
                    TaskItem::create([
                        'task_report_id' => $report->id,
                        'item_name' => $item['name'],
                        'serial_number' => $item['serial'] ?? null,
                        'quantity' => $item['qty'] ?? 1,
                    ]);
                }
            }

            // 6. Thiết bị thu hồi (Bảo hành)
            foreach ($this->returnedItems as $returned) {
                if (!empty($returned['name'])) {
                    \App\Models\ReturnedItem::create([
                        'task_report_id' => $report->id,
                        'item_name' => $returned['name'],
                        'serial_number' => $returned['serial'] ?? null,
                        'reason' => $returned['reason'] ?? null,
                    ]);
                }
            }

            // 7. Cập nhật trạng thái Task cha
            if ($this->is_task_completed) {
                $this->task->update(['status' => \App\Enums\TaskStatus::COMPLETED]);
            } else {
                if ($this->task->status === \App\Enums\TaskStatus::PENDING) {
                    $this->task->update(['status' => \App\Enums\TaskStatus::PROCESSING]);
                }
            }
        });

        session()->flash('success', 'Đã lưu báo cáo thành công!');
        return redirect()->route('admin.tasks.detail', $this->task->id);
    }
    // 1. Hàm tìm kiếm (Chạy khi user gõ phím)
    public function updatedItems($value, $key)
    {
        // Key có dạng: 0.name, 1.name ...
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        // Nếu đang gõ vào trường 'name'
        if ($field === 'name') {
            if (strlen($value) >= 2) {
                // Tìm trong bảng materials (tìm tên hoặc tên viết tắt)
                $this->materialSuggestions[$index] = \App\Models\Material::query()
                    ->where('name', 'like', '%' . $value . '%')
                    ->orWhere('short_name', 'like', '%' . $value . '%')
                    ->orWhere('code', 'like', '%' . $value . '%')
                    ->take(5)
                    ->get();
                
                $this->showSuggestions[$index] = true;
            } else {
                $this->showSuggestions[$index] = false;
            }
        }

        // Logic cũ: Check Serial -> SL = 1
        if ($field === 'serial') {
             if (!empty($value)) {
                $this->items[$index]['qty'] = 1;
             }
        }
    }

    // 2. Hàm chọn vật tư từ danh sách gợi ý
    public function selectMaterial($index, $materialId)
    {
        $material = \App\Models\Material::find($materialId);
        if ($material) {
            // Điền tên chuẩn vào ô
            $this->items[$index]['name'] = $material->name;
            // Nếu có mã SKU, có thể điền tạm vào ô serial hoặc ghi chú (tùy nhu cầu)
            // $this->items[$index]['serial'] = $material->code; 
        }
        
        // Ẩn dropdown
        $this->showSuggestions[$index] = false;
    }

    // 3. Ẩn gợi ý khi click ra ngoài (Optional)
    public function closeSuggestions($index)
    {
        // Dùng sleep nhỏ để kịp bắt sự kiện click chọn trước khi đóng
        usleep(200000); 
        $this->showSuggestions[$index] = false;
    }

    // 4. Thêm/Xóa dòng vật tư
    public function addItem()
    {
        $this->items[] = ['name' => '', 'serial' => '', 'qty' => 1];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    // 5. Xử lý Scan Serial liên tục
    public function handleScannedSerial($serial)
    {
        // Tìm dòng trống đầu tiên (chưa có serial)
        $foundIndex = -1;
        foreach ($this->items as $index => $item) {
            if (empty($item['serial'])) {
                $foundIndex = $index;
                break;
            }
        }

        // Nếu không có dòng trống, thêm dòng mới
        if ($foundIndex === -1) {
            $this->items[] = ['name' => '', 'serial' => $serial, 'qty' => 1];
            $foundIndex = count($this->items) - 1;
        } else {
            // Điền vào dòng trống tìm thấy
            $this->items[$foundIndex]['serial'] = $serial;
            $this->items[$foundIndex]['qty'] = 1;
        }

        // Tự động tìm tên vật tư theo mã (nếu có logic mapping)
        // $material = Material::where('code', $serial)->first();
        // if($material) $this->items[$foundIndex]['name'] = $material->name;

        $this->dispatch('scan-success', "Đã thêm serial: $serial");
    }

    // --- THIẾT BỊ THU HỒI (Bảo hành) ---
    public function addReturnedItem()
    {
        $this->returnedItems[] = ['name' => '', 'serial' => '', 'reason' => ''];
    }

    public function removeReturnedItem($index)
    {
        unset($this->returnedItems[$index]);
        $this->returnedItems = array_values($this->returnedItems);
    }

    // === CONTINUOUS SCAN METHODS ===
    
    /**
     * Thêm vật tư từ scan liên tục
     * @param string $serial Mã serial
     * @param string $materialName Tên vật tư (đã chọn trước khi quét)
     */
    public function addScannedItem($serial, $materialName = '')
    {
        $this->items[] = ['name' => $materialName, 'serial' => $serial, 'qty' => 1];
    }

    /**
     * Thêm thiết bị thu hồi từ scan liên tục
     * @param string $serial Mã serial
     * @param string $materialName Tên thiết bị (đã chọn trước khi quét)
     */
    public function addScannedReturnedItem($serial, $materialName = '')
    {
        $this->returnedItems[] = ['name' => $materialName, 'serial' => $serial, 'reason' => ''];
    }

    // --- TASK PHÁT SINH / SPAWN ---
    public function createAdditionalTask()
    {
        // Chặn nếu WorkOrder đã đóng
        if (!$this->task->workOrder->allowsAdditionalTasks()) {
            session()->flash('error', 'Không thể thêm task vì phiếu việc đã hoàn thành hoặc chờ duyệt.');
            return;
        }

        $this->validate([
            'newTaskTitle' => 'required|min:5|max:255',
            'newTaskScheduledAt' => 'nullable|date|after_or_equal:today',
            'newTaskAssigneeId' => 'nullable|exists:admins,id',
        ], [
            'newTaskTitle.required' => 'Vui lòng nhập nội dung công việc.',
            'newTaskTitle.min' => 'Nội dung phải có ít nhất 5 ký tự.',
            'newTaskScheduledAt.after_or_equal' => 'Ngày hẹn phải từ hôm nay trở đi.',
        ]);

        $newTask = Task::create([
            'work_order_id' => $this->task->work_order_id,
            'parent_task_id' => $this->task->id, // Link to current task as parent
            'title' => $this->newTaskTitle,
            'performer_id' => $this->newTaskAssigneeId ?: null,
            'scheduled_at' => $this->newTaskScheduledAt ?: null,
            'status' => \App\Enums\TaskStatus::PENDING,
            'is_additional' => true,
            'created_by_worker_id' => auth('admin')->id(),
        ]);

        session()->flash('success', 'Đã tạo công việc tiếp theo: ' . $newTask->title);
        $this->newTaskTitle = '';
        $this->newTaskScheduledAt = '';
        $this->newTaskAssigneeId = '';
        
        return redirect()->route('admin.tasks.detail', $newTask->id);
    }

    public function render()
    {
        return view('livewire.work-order.task-detail.main')
            ->layout(auth('admin')->user()->layout);
    }
}
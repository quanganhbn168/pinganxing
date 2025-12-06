<?php

namespace App\Livewire\Worker;

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
    
    // TIỀN NONG
    public $has_payment = false; 
    public $collected_amount = 0;
    public $payment_method = 'cash'; 
    public $transfer_target = 'company'; 

    public $items = [['name' => '', 'serial' => '', 'qty' => 1]]; 
    public $proof_images = [];
    public $signature_data;

    // BIẾN MỚI CHO SUGGESTION
    public $materialSuggestions = []; 
    public $showSuggestions = [];     
    
    public function mount($id)
    {
        $this->task = Task::with([
            'workOrder', 
            'reports.images', 
            'reports.items', 
            'reports.reporter'
        ])->findOrFail($id);
        
        // Nếu task đã xong -> vào xem lịch sử
        if($this->task->status === \App\Enums\TaskStatus::COMPLETED) {
            $this->activeTab = 'history';
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        if($tab == 'new_report' && $this->task->status !== \App\Enums\TaskStatus::COMPLETED) {
            $this->dispatch('init-signature');
        }
    }

    public function saveReport()
    {
        // CHẶN BÁO CÁO NẾU TASK ĐÃ XONG
        if($this->task->status === \App\Enums\TaskStatus::COMPLETED) {
            $this->dispatch('error', 'Công việc này đã hoàn thành. Vui lòng liên hệ Admin nếu cần mở lại.');
            return;
        }

        $this->validate([
            'report_content' => 'required|min:5',
            'collected_amount' => 'numeric|min:0',
            'proof_images.*' => 'image|max:10240',
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

            // 2. Xử lý Tiền nong
            if (!$this->has_payment) {
                $this->collected_amount = 0;
                $this->payment_method = null;
                $this->transfer_target = null;
            }

            // 3. Tạo Report
            $report = TaskReport::create([
                'task_id' => $this->task->id,
                'reporter_id' => auth('admin')->id(),
                'content' => $this->report_content,
                'is_completed' => $this->is_task_completed,
                
                'collected_amount' => $this->collected_amount,
                'payment_method' => $this->has_payment ? $this->payment_method : null,
                'transfer_target' => ($this->has_payment && $this->payment_method == 'transfer') ? $this->transfer_target : null,
                
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

            // 6. Cập nhật trạng thái Task cha
            if ($this->is_task_completed) {
                $this->task->update(['status' => \App\Enums\TaskStatus::COMPLETED]);
            } else {
                if ($this->task->status === \App\Enums\TaskStatus::PENDING) {
                    $this->task->update(['status' => \App\Enums\TaskStatus::PROCESSING]);
                }
            }
        });

        session()->flash('success', 'Đã lưu báo cáo thành công!');
        return redirect()->route('worker.tasks.detail', $this->task->id);
    }
    
    // 1. Hàm tìm kiếm
    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];
        $field = $parts[1];

        if ($field === 'name') {
            if (strlen($value) >= 2) {
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

        if ($field === 'serial') {
             if (!empty($value)) {
                $this->items[$index]['qty'] = 1;
             }
        }
    }

    public function selectMaterial($index, $materialId)
    {
        $material = \App\Models\Material::find($materialId);
        if ($material) {
            $this->items[$index]['name'] = $material->name;
        }
        $this->showSuggestions[$index] = false;
    }

    public function closeSuggestions($index)
    {
        usleep(200000); 
        $this->showSuggestions[$index] = false;
    }

    public function addItem()
    {
        $this->items[] = ['name' => '', 'serial' => '', 'qty' => 1];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function handleScannedSerial($serial)
    {
        $foundIndex = -1;
        foreach ($this->items as $index => $item) {
            if (empty($item['serial'])) {
                $foundIndex = $index;
                break;
            }
        }

        if ($foundIndex === -1) {
            $this->items[] = ['name' => '', 'serial' => $serial, 'qty' => 1];
            $foundIndex = count($this->items) - 1;
        } else {
            $this->items[$foundIndex]['serial'] = $serial;
            $this->items[$foundIndex]['qty'] = 1;
        }

        $this->dispatch('scan-success', "Đã thêm serial: $serial");
    }

    public function render()
    {
        return view('livewire.worker.task-detail.main')
            ->layout('layouts.mobile', ['title' => $this->task->workOrder->title . ' - ' . $this->task->name]);
    }
}

<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Customer;
use App\Models\WorkOrder;
use App\Models\Admin;
use App\Models\Tag;
use App\Services\WorkOrderService;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

class CreateWorkOrder extends Component
{
    use WithFileUploads;

    // --- Biến cho Job (Work Order) ---
    public $title;
    public $description;
    public $assignee_ids = []; 
    public $priority = 'medium';
    
    // --- Thông tin thi công (Site Info) ---
    public $site_address;
    public $contact_person;
    public $contact_phone;

    // --- Thời gian ---
    public $started_at; // Thời điểm bắt đầu
    public $deadline_option = 'custom'; // '1', '2', '3', 'custom'
    public $deadline; // Deadline cụ thể (khi chọn custom)
    public $days_count = 0; // Số ngày tính được

    // --- Danh sách nhiệm vụ con ---
    public $task_list = [
        ['content' => '', 'note' => '']
    ];

    // --- Khách hàng ---
    public $search_customer = '';
    public $selected_customer_id = null;
    public $selected_customer_name = ''; 
    public $suggestedSites = [];

    // --- File đính kèm ---
    public $attachments = []; // File upload (nhiều file)

    // --- Tags ---
    public $selected_tags = []; // Mảng ID tags được chọn

    public function mount()
    {
        // Mặc định started_at = thời điểm hiện tại
        $this->started_at = now()->format('Y-m-d\TH:i');
    }

    #[Layout('layouts.admin')] 
    public function render()
    {
        $customers = [];
        if (strlen($this->search_customer) > 1) {
            $customers = Customer::query()
                ->with('contacts')
                ->where('name', 'like', '%' . $this->search_customer . '%')
                ->orWhereHas('contacts', function ($q) {
                    $q->where('value', 'like', '%' . $this->search_customer . '%');
                })
                ->take(10)->get();
        }
        $staffs = Admin::all();

        return view('livewire.work-order.create-work-order', [
            'customers' => $customers,
            'staffs' => $staffs,
            'availableTags' => Tag::forWorkOrders()->ordered()->get(),
        ]);
    }

    // --- Khi thay đổi deadline option ---
    public function updatedDeadlineOption($value)
    {
        $this->calculateDeadline();
    }

    // --- Khi thay đổi deadline tùy chọn ---
    public function updatedDeadline($value)
    {
        $this->calculateDaysCount();
    }

    // --- Khi thay đổi thời điểm bắt đầu ---
    public function updatedStartedAt($value)
    {
        $this->calculateDeadline();
    }

    // Tính deadline dựa trên option
    protected function calculateDeadline()
    {
        if (!$this->started_at) return;

        $start = Carbon::parse($this->started_at);

        if ($this->deadline_option === 'custom') {
            // Giữ nguyên deadline đã chọn
            $this->calculateDaysCount();
        } else {
            // Tính deadline từ số ngày
            $days = (int) $this->deadline_option;
            $deadlineDate = $start->copy()->addDays($days);
            $this->deadline = $deadlineDate->format('Y-m-d\TH:i');
            $this->days_count = $days;
        }
    }

    // Tính số ngày từ deadline
    protected function calculateDaysCount()
    {
        if (!$this->started_at || !$this->deadline) {
            $this->days_count = 0;
            return;
        }

        $start = Carbon::parse($this->started_at);
        $end = Carbon::parse($this->deadline);
        $this->days_count = max(0, $start->diffInDays($end, false));
    }

    // --- Logic thêm/xóa dòng nhiệm vụ ---
    public function addTaskRow()
    {
        $this->task_list[] = ['content' => '', 'note' => ''];
    }

    public function removeTaskRow($index)
    {
        unset($this->task_list[$index]);
        $this->task_list = array_values($this->task_list);
    }

    // --- Xóa file đính kèm (preview) ---
    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    // --- Toggle tag selection ---
    public function toggleTag($tagId)
    {
        $tagId = (int) $tagId;
        if (in_array($tagId, $this->selected_tags)) {
            $this->selected_tags = array_values(array_diff($this->selected_tags, [$tagId]));
        } else {
            $this->selected_tags[] = $tagId;
        }
    }

    // --- Chọn khách hàng ---
    public function selectCustomer($id, $name)
    {
        $this->selected_customer_id = $id;
        $this->selected_customer_name = $name;
        $this->search_customer = '';

        $customer = Customer::with('contacts')->find($id);
        if ($customer) {
            $this->contact_person = $customer->name; 
            
            $phone = $customer->contacts->where('type', 'phone')->sortByDesc('is_primary')->first();
            $this->contact_phone = $phone ? $phone->value : '';

            $address = $customer->contacts->where('type', 'address')->sortByDesc('is_primary')->first();
            $this->site_address = $address ? $address->value : '';
        }

        // Load lịch sử địa điểm thi công
        $this->suggestedSites = WorkOrder::where('customer_id', $id)
            ->latest()
            ->take(30)
            ->get()
            ->unique(fn($item) => $item->site_address . '|' . $item->contact_person . '|' . $item->contact_phone)
            ->take(5)
            ->map(fn($item) => [
                'site_address' => $item->site_address,
                'contact_person' => $item->contact_person,
                'contact_phone' => $item->contact_phone
            ])
            ->values()
            ->toArray();
    }
    
    public function fillSiteInfo($index)
    {
        if (isset($this->suggestedSites[$index])) {
            $site = $this->suggestedSites[$index];
            $this->site_address = $site['site_address'];
            $this->contact_person = $site['contact_person'];
            $this->contact_phone = $site['contact_phone'];
        }
    }

    public function clearSelectedCustomer()
    {
        $this->selected_customer_id = null;
        $this->selected_customer_name = '';
        $this->suggestedSites = [];
        $this->reset(['site_address', 'contact_person', 'contact_phone']);
    }

    // --- Lưu Work Order ---
    public function save(WorkOrderService $service)
    {
        $this->validate([
            'title' => 'required|min:5',
            'assignee_ids' => 'required|array|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'started_at' => 'required|date',
            'site_address' => 'required',
            'contact_person' => 'required',
            'contact_phone' => 'required',
            'task_list.*.content' => 'required|min:3',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ], [
            'assignee_ids.required' => 'Phải gán ít nhất 1 nhân viên.',
            'started_at.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'site_address.required' => 'Địa chỉ thi công không được để trống.',
            'contact_person.required' => 'Người liên hệ không được để trống.',
            'contact_phone.required' => 'Số điện thoại không được để trống.',
            'task_list.*.content.required' => 'Nội dung nhiệm vụ không được để trống.',
        ]);

        try {
            // Nếu chưa chọn khách có sẵn -> Tạo khách lẻ mới từ thông tin liên hệ thi công
            $customerId = $this->selected_customer_id;
            if (!$customerId) {
                $customer = Customer::create([
                    'name' => $this->contact_person,
                    'type' => 'individual', // Khách lẻ
                    'notes' => 'Tự động tạo từ phiếu việc',
                ]);
                // Thêm SĐT
                $customer->contacts()->create([
                    'type' => 'phone',
                    'value' => $this->contact_phone,
                    'is_primary' => true,
                ]);
                // Thêm địa chỉ
                if ($this->site_address) {
                    $customer->contacts()->create([
                        'type' => 'address',
                        'value' => $this->site_address,
                        'is_primary' => true,
                    ]);
                }
                $customerId = $customer->id;
            }

            // Chuẩn bị dữ liệu cho Service
            $data = [
                'customer_id' => $customerId,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'started_at' => $this->started_at,
                'deadline' => $this->deadline,
                'site_address' => $this->site_address,
                'contact_person' => $this->contact_person,
                'contact_phone' => $this->contact_phone,
                'assignee_ids' => $this->assignee_ids,
                'tasks' => array_column($this->task_list, 'content'),
            ];

            // Tạo Work Order qua Service
            $workOrder = $service->create($data);

            // Upload attachments nếu có
            if (!empty($this->attachments)) {
                $images = [];
                $documents = [];
                
                foreach ($this->attachments as $file) {
                    $ext = strtolower($file->getClientOriginalExtension());
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $images[] = $file;
                    } else {
                        $documents[] = $file;
                    }
                }

                if (!empty($images)) {
                    $service->storeAttachments($workOrder, $images, 'image');
                }
                if (!empty($documents)) {
                    $service->storeAttachments($workOrder, $documents, 'document');
                }
            }

            // Gán tags nếu có
            if (!empty($this->selected_tags)) {
                $workOrder->tags()->attach($this->selected_tags);
            }

            // Reset form
            $this->reset();
            $this->task_list = [['content' => '', 'note' => '']];
            $this->started_at = now()->format('Y-m-d\TH:i');
            $this->deadline_option = 'custom';
            $this->selected_tags = [];
            $this->dispatch('clear-select2');
            
            $this->dispatch('swal', [
                'title' => 'Thành công!',
                'text' => 'Đã tạo phiếu việc thành công!',
                'icon' => 'success',
                'timer' => 3000
            ]);

            session()->flash('success', 'Đã tạo phiếu việc thành công!');

        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
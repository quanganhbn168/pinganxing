<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use App\Models\Customer;
use App\Models\WorkOrder;
use App\Models\CustomerContact;
use App\Models\Admin; // Model nhân viên
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

class CreateWorkOrder extends Component
{
    // --- Biến cho Job (Work Order) ---
    public $title;
    public $description;
    public $assignee_ids = []; // Mảng chứa ID nhân viên được gán

    // --- Biến cho Khách hàng ---
    public $is_new_customer = false;
    
    // Tìm kiếm khách cũ
    public $search_customer = '';
    public $selected_customer_id = null;
    public $selected_customer_name = ''; 
    
    // Nhập khách mới
    public $new_customer_name;
    public $new_customer_phone;
    public $new_customer_address;

    #[Layout('layouts.admin')] 
    public function render()
    {
        $customers = [];
        
        // Logic tìm kiếm khách
        if (!$this->is_new_customer && strlen($this->search_customer) > 1) {
            $customers = Customer::query()
                ->with('contacts')
                ->where('name', 'like', '%' . $this->search_customer . '%')
                ->orWhereHas('contacts', function ($q) {
                    $q->where('value', 'like', '%' . $this->search_customer . '%');
                })
                ->take(10)
                ->get();
        }

        // Lấy danh sách tất cả nhân viên để chọn gán việc
        // (Có thể lọc where('role', '!=', 'super_admin') nếu cần, ở đây lấy hết)
        $staffs = Admin::all();

        return view('livewire.work-order.create-work-order', [
            'customers' => $customers,
            'staffs' => $staffs
        ]);
    }

    public function selectCustomer($id, $name)
    {
        $this->selected_customer_id = $id;
        $this->selected_customer_name = $name;
        $this->search_customer = '';
    }

    public function clearSelectedCustomer()
    {
        $this->selected_customer_id = null;
        $this->selected_customer_name = '';
    }

    public function toggleNewCustomer()
    {
        $this->is_new_customer = !$this->is_new_customer;
        $this->reset(['selected_customer_id', 'selected_customer_name', 'search_customer', 'new_customer_name', 'new_customer_phone', 'new_customer_address']);
    }

    public function save()
    {
        $rules = [
            'title' => 'required|min:5',
            'assignee_ids' => 'required|array|min:1', // Bắt buộc phải chọn ít nhất 1 thợ
        ];

        if ($this->is_new_customer) {
            $rules['new_customer_name'] = 'required';
            $rules['new_customer_phone'] = 'required';
        } else {
            $rules['selected_customer_id'] = 'required';
        }

        $this->validate($rules, [
            'selected_customer_id.required' => 'Chưa chọn khách hàng.',
            'new_customer_name.required' => 'Tên khách là bắt buộc.',
            'new_customer_phone.required' => 'SĐT là bắt buộc.',
            'title.required' => 'Phải nhập tiêu đề.',
            'assignee_ids.required' => 'Phải gán ít nhất 1 nhân viên.',
        ]);

        DB::beginTransaction();
        try {
            $customerId = $this->selected_customer_id;

            // Xử lý Khách hàng
            if ($this->is_new_customer) {
                $customer = Customer::create(['name' => $this->new_customer_name]);
                CustomerContact::create([
                    'customer_id' => $customer->id,
                    'type' => 'phone',
                    'value' => $this->new_customer_phone,
                    'is_primary' => true
                ]);
                if ($this->new_customer_address) {
                    CustomerContact::create([
                        'customer_id' => $customer->id,
                        'type' => 'address',
                        'value' => $this->new_customer_address,
                        'is_primary' => true
                    ]);
                }
                $customerId = $customer->id;
            }

            // Tạo Work Order
            $workOrder = WorkOrder::create([
                'customer_id' => $customerId,
                'created_by' => auth('admin')->id(),
                'code' => 'WO-' . strtoupper(Str::random(6)),
                'title' => $this->title,
                'description' => $this->description,
                'status' => 'pending'
            ]);

            // --- QUAN TRỌNG: Gán nhân viên vào Job ---
            // attach() sẽ ghi vào bảng work_order_assignees
            $workOrder->assignees()->attach($this->assignee_ids);

            DB::commit();

            // Reset và thông báo
            $this->reset(['title', 'description', 'assignee_ids', 'is_new_customer', 'search_customer', 'selected_customer_id', 'selected_customer_name']);
            
            // Dispatch sự kiện để JS bên View clear cái Select2 đi
            $this->dispatch('clear-select2'); 
            
            session()->flash('success', 'Đã tạo phiếu việc thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
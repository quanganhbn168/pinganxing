<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use App\Models\CustomerContact;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

class CustomerForm extends Component
{
    public $customer_id = null; // Nếu có ID là Sửa, null là Thêm
    public $name;
    public $notes;

    // Mảng chứa danh sách liên hệ
    // Cấu trúc: [['type' => 'phone', 'value' => '098...', 'label' => 'Nhà riêng']]
    public $contacts = []; 

    public function mount($id = null)
    {
        if ($id) {
            // Chế độ EDIT
            $this->customer_id = $id;
            $customer = Customer::with('contacts')->findOrFail($id);
            $this->name = $customer->name;
            $this->notes = $customer->notes;
            
            // Load danh sách liên hệ cũ vào mảng
            foreach ($customer->contacts as $contact) {
                $this->contacts[] = [
                    'type' => $contact->type,
                    'value' => $contact->value,
                    'label' => $contact->label,
                    'is_primary' => $contact->is_primary
                ];
            }
        } else {
            // Chế độ CREATE: Mặc định có sẵn 1 ô nhập SĐT cho tiện
            $this->contacts[] = ['type' => 'phone', 'value' => '', 'label' => 'Di động', 'is_primary' => 1];
        }
    }

    // Thêm dòng liên hệ mới
    public function addContact($type)
    {
        $this->contacts[] = [
            'type' => $type, // 'phone' hoặc 'address'
            'value' => '',
            'label' => '',
            'is_primary' => 0
        ];
    }

    // Xóa dòng liên hệ
    public function removeContact($index)
    {
        unset($this->contacts[$index]);
        $this->contacts = array_values($this->contacts); // Re-index mảng
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
            'contacts.*.value' => 'required', // Bắt buộc nhập giá trị nếu đã thêm dòng
        ], [
            'name.required' => 'Tên khách hàng không được để trống.',
            'contacts.*.value.required' => 'Thông tin liên hệ không được để trống.',
        ]);

        DB::transaction(function () {
            // 1. Lưu/Cập nhật Khách
            $customer = Customer::updateOrCreate(
                ['id' => $this->customer_id],
                ['name' => $this->name, 'notes' => $this->notes]
            );

            // 2. Xử lý Liên hệ: Xóa hết cũ tạo lại mới (Cách đơn giản nhất để đồng bộ)
            // Nếu muốn tối ưu hơn thì check ID, nhưng với data nhỏ thì xóa đi tạo lại cho nhanh code
            if ($this->customer_id) {
                CustomerContact::where('customer_id', $this->customer_id)->delete();
            }

            foreach ($this->contacts as $contact) {
                if (!empty($contact['value'])) {
                    CustomerContact::create([
                        'customer_id' => $customer->id,
                        'type' => $contact['type'],
                        'value' => $contact['value'],
                        'label' => $contact['label'] ?? null,
                        'is_primary' => $contact['is_primary'] ?? 0
                    ]);
                }
            }
        });

        session()->flash('success', $this->customer_id ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
        return redirect()->route('admin.customers.index');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.customer.customer-form');
    }
}
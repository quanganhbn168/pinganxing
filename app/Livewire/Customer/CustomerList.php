<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class CustomerList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    // --- PHẦN MỚI: XỬ LÝ CHECKBOX ---
    public $selected = []; // Mảng chứa ID các khách được chọn
    public $selectAll = false; // Trạng thái ô check all

    // Khi chuyển trang thì reset lại lựa chọn
    public function updatingPage()
    {
        $this->reset(['selected', 'selectAll']);
    }

    // Xử lý nút "Chọn tất cả" ở header bảng
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Chỉ chọn những item đang hiển thị ở trang hiện tại
            $this->selected = $this->getCustomersQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    // Helper query để tái sử dụng
    private function getCustomersQuery()
    {
        $query = Customer::query()
            ->with(['contacts', 'workOrders']) 
            
            ->withSum(['tasks as total_spent' => function($q) {
                $q->where('is_paid', true); // Nếu muốn tính cả tiền chưa nộp thì bỏ dòng này đi
            }], 'collected_amount')
            // --------------------

            ->withCount('workOrders')
            ->orderByDesc('id');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('contacts', function($c) {
                      $c->where('value', 'like', '%' . $this->search . '%');
                  });
            });
        }
        return $query;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        // Kiểm tra xem số lượng đã chọn có khớp với số lượng trên trang không để tick ô SelectAll
        $customers = $this->getCustomersQuery()->paginate(10);
        
        // Logic nhỏ để UI checkbox SelectAll tự động bật tắt
        $currentPageIds = $customers->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->selectAll = !empty($currentPageIds) && count(array_intersect($currentPageIds, $this->selected)) === count($currentPageIds);

        return view('livewire.customer.customer-list', [
            'customers' => $customers
        ]);
    }

    // --- HÀNH ĐỘNG: XÓA 1 KHÁCH ---
    public function delete($id)
    {
        $this->deleteCustomers([$id]);
    }

    // --- HÀNH ĐỘNG: XÓA NHIỀU KHÁCH ---
    public function deleteSelected()
    {
        $this->deleteCustomers($this->selected);
        $this->reset(['selected', 'selectAll']);
    }

    // Logic xóa chung
    private function deleteCustomers(array $ids)
    {
        // Lọc ra những khách có Job -> Không cho xóa
        $customersWithJobs = Customer::whereIn('id', $ids)->has('workOrders')->count();

        if ($customersWithJobs > 0) {
            $this->dispatch('notify', type: 'error', message: "Không thể xóa $customersWithJobs khách hàng đang có lịch sử giao dịch (Phiếu việc)!");
            return;
        }

        Customer::whereIn('id', $ids)->delete();
        $this->dispatch('notify', type: 'success', message: 'Đã xóa dữ liệu khách hàng chọn.');
    }
}
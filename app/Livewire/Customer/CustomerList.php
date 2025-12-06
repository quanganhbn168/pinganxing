<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use Livewire\Attributes\Layout;

class CustomerList extends Component
{
    use WithPagination;
    use \Livewire\WithFileUploads; // Thêm trait upload
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $file_import; // Variable for file upload
    
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

    // --- IMPORT / EXPORT EXCEL ---

    public function downloadTemplate()
    {
        $rows = [
            ['Tên khách hàng (Bắt buộc)', 'Email', 'Số điện thoại', 'Địa chỉ', 'Mã số thuế', 'Loại (personal/company)', 'Ghi chú'],
            ['Nguyễn Văn A', 'a@example.com', '0912345678', 'Hà Nội', '', 'personal', 'Khách vip'],
            ['Công ty ABC', 'contact@abc.com', '0243123456', 'Hồ Chí Minh', '0101234567', 'company', 'Đối tác'],
        ];

        return response()->streamDownload(function () use ($rows) {
            \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload('mau_nhap_khach_hang.xlsx')
                ->addRows($rows)
                ->close();
        }, 'mau_nhap_khach_hang.xlsx');
    }

    public function export()
    {
        $customers = Customer::with('contacts')->get();
        
        return response()->streamDownload(function () use ($customers) {
            $writer = \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload('danh_sach_khach_hang_' . date('d_m_Y') . '.xlsx');
            
            foreach ($customers as $cus) {
                // Lấy thông tin liên hệ chính
                $phone = $cus->contacts->where('type', 'phone')->first()->value ?? '';
                $address = $cus->contacts->where('type', 'address')->first()->value ?? '';
                
                $writer->addRow([
                    'ID' => $cus->id,
                    'Tên khách hàng' => $cus->name,
                    'Email' => $cus->email,
                    'SĐT' => $phone,
                    'Địa chỉ' => $address,
                    'Mã số thuế' => $cus->tax_code,
                    'Loại' => $cus->type,
                    'Ghi chú' => $cus->notes,
                    'Ngày tạo' => $cus->created_at->format('d/m/Y')
                ]);
            }
            $writer->close();
        }, 'danh_sach_khach_hang_' . date('d_m_Y') . '.xlsx');
    }

    public function updatedFileImport()
    {
        $this->validate([
            'file_import' => 'max:10240', // Limit 10MB
        ]);

        $path = $this->file_import->getRealPath();
        $extension = $this->file_import->getClientOriginalExtension();
        
        if (!in_array(strtolower($extension), ['xlsx', 'csv', 'xls'])) {
             $this->dispatch('swal', ['title' => 'Lỗi', 'text' => 'Chỉ hỗ trợ file .xlsx hoặc .csv', 'icon' => 'error']);
             return;
        }

        try {
            $rows = \Spatie\SimpleExcel\SimpleExcelReader::create($path, $extension)->getRows();
            
            $importedCount = 0;
            $skippedCount = 0;
            $duplicates = []; // Lưu chi tiết trùng lặp
            
            // 1. Collect data for checking
            $emailsToCheck = [];
            $taxCodesToCheck = [];
            $phonesToCheck = [];
            
            // Đọc trước để gom dữ liệu check (Có thể tốn RAM nếu file quá lớn, nhưng an toàn hơn)
            // Với SimpleExcel có thể loop 2 lần.
            
            // 1. Check existing Email/Tax in DB
            $existingEmails = Customer::whereNotNull('email')->pluck('email')->toArray();
            $existingTaxCodes = Customer::whereNotNull('tax_code')->pluck('tax_code')->toArray();
            $existingPhones = \App\Models\CustomerContact::where('type', 'phone')->pluck('value')->toArray();

            // Normalize for comparison (lowercase, trim)
            $existingEmails = array_map(fn($v) => strtolower(trim($v)), $existingEmails);
            $existingTaxCodes = array_map(fn($v) => strtolower(trim($v)), $existingTaxCodes);
            $existingPhones = array_map(fn($v) => trim($v), $existingPhones);

            $rows->each(function(array $row) use (&$importedCount, &$skippedCount, &$duplicates, $existingEmails, $existingTaxCodes, $existingPhones) {
                // Header mapping
                // ['Tên khách hàng (Bắt buộc)', 'Email', 'Số điện thoại', 'Địa chỉ', 'Mã số thuế', 'Loại (personal/company)', 'Ghi chú']
                
                $values = array_values($row);
                $name = $row['Tên khách hàng (Bắt buộc)'] ?? ($row['Tên khách hàng'] ?? $values[0]);
                
                // Skip example row or empty
                if (empty($name) || $name === 'Nguyễn Văn A' || $name === 'Tên khách hàng (Bắt buộc)') return;
                
                $email = isset($row['Email']) ? trim($row['Email']) : (isset($values[1]) ? trim($values[1]) : null);
                $phone = isset($row['Số điện thoại']) ? trim($row['Số điện thoại']) : (isset($values[2]) ? trim($values[2]) : null);
                $address = isset($row['Địa chỉ']) ? trim($row['Địa chỉ']) : (isset($values[3]) ? trim($values[3]) : null);
                $tax = isset($row['Mã số thuế']) ? trim($row['Mã số thuế']) : (isset($values[4]) ? trim($values[4]) : null);
                $type = isset($row['Loại (personal/company)']) ? trim($row['Loại (personal/company)']) : (isset($values[5]) ? trim($values[5]) : 'personal');
                $note = isset($row['Ghi chú']) ? trim($row['Ghi chú']) : (isset($values[6]) ? trim($values[6]) : null);

                // --- DUPLICATE CHECKING ---
                $isDuplicate = false;
                $dupReason = [];

                if ($email && in_array(strtolower($email), $existingEmails)) {
                    $isDuplicate = true;
                    $dupReason[] = "Email: $email";
                }
                if ($tax && in_array(strtolower($tax), $existingTaxCodes)) {
                    $isDuplicate = true;
                    $dupReason[] = "MST: $tax";
                }
                if ($phone && in_array($phone, $existingPhones)) {
                    $isDuplicate = true;
                     $dupReason[] = "SĐT: $phone";
                }

                if ($isDuplicate) {
                    $skippedCount++;
                    $duplicates[] = "$name (" . implode(', ', $dupReason) . ")";
                    return;
                }

                // Create Customer
                $customer = Customer::create([
                    'name' => $name,
                    'email' => $email,
                    'tax_code' => $tax,
                    'type' => $type === 'company' ? 'company' : 'personal',
                    'notes' => $note,
                ]);

                // Create Contacts
                if ($phone) {
                    $customer->contacts()->create([
                        'type' => 'phone',
                        'value' => $phone,
                        'label' => 'Chính',
                        'is_primary' => true
                    ]);
                }
                if ($address) {
                    $customer->contacts()->create([
                        'type' => 'address',
                        'value' => $address,
                        'label' => 'Địa chỉ',
                        'is_primary' => true
                    ]);
                }

                $importedCount++;
                
                // Add to temporary existing list to avoid self-duplicates in same file? (Optional but recommended)
                // For now, let's keep it simple.
            });

            if ($skippedCount > 0) {
                $msg = "Đã nhập: <b>$importedCount</b><br>";
                $msg .= "Trùng lặp (Bỏ qua): <b class='text-danger'>$skippedCount</b><br>";
                $msg .= "<div class='text-left mt-2 text-sm' style='max-height: 150px; overflow-y: auto;'><b>Chi tiết trùng:</b><br>" . implode('<br>', $duplicates) . "</div>";
                
                $this->dispatch('swal', ['title' => 'Kết quả nhập khẩu', 'text' => $msg, 'icon' => 'warning', 'html' => true]);
            } else {
                $this->dispatch('swal', ['title' => 'Hoàn tất', 'text' => "Đã nhập thành công $importedCount khách hàng!", 'icon' => 'success']);
            }

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Lỗi nhập file', 'text' => $e->getMessage(), 'icon' => 'error']);
        }

        $this->reset('file_import');
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
            $this->dispatch('swal', ['title' => 'Cảnh báo', 'text' => "Không thể xóa $customersWithJobs khách hàng đang có lịch sử giao dịch!", 'icon' => 'error']);
            return;
        }

        Customer::whereIn('id', $ids)->delete();
        $this->dispatch('swal', ['title' => 'Thành công', 'text' => 'Đã xóa dữ liệu khách hàng chọn.', 'icon' => 'success']);
    }
}
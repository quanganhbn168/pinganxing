<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialList extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $name, $code, $unit = 'cái';
    public $is_edit = false;
    public $edit_id;
    
    public $file_import;

    // Reset form
    public function resetForm() {
        $this->reset(['name', 'code', 'unit', 'is_edit', 'edit_id']);
    }

    public function save() {
        $this->validate([
            'name' => 'required',
            'code' => 'nullable|unique:materials,code,' . $this->edit_id,
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'unit' => $this->unit,
            // 'short_name' và 'price' đã bị loại bỏ theo yêu cầu
        ];

        if ($this->is_edit) {
            Material::find($this->edit_id)->update($data);
        } else {
            Material::create($data);
        }

        $this->resetForm();
        $this->dispatch('swal', ['title' => 'Thành công', 'text' => 'Đã lưu vật tư!', 'icon' => 'success']);
    }

    public function edit($id) {
        $m = Material::find($id);
        $this->edit_id = $m->id;
        $this->name = $m->name;
        $this->code = $m->code;
        $this->unit = $m->unit;
        $this->is_edit = true;
    }

    // --- IMPORT / EXPORT (XLXS with Spatie/SimpleExcel) ---
    
    public function downloadTemplate()
    {
        $rows = [
            ['Tên vật tư (Bắt buộc)', 'Mã SKU (Tùy chọn)', 'ĐVT'],
            ['Dây cáp mạng Cat6', 'CAB-CAT6', 'mét'],
        ];

        return response()->streamDownload(function () use ($rows) {
            \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload('mau_nhap_vat_tu.xlsx')
                ->addRows($rows)
                ->close();
        }, 'mau_nhap_vat_tu.xlsx');
    }

    public function export()
    {
        $materials = Material::all();
        
        return response()->streamDownload(function () use ($materials) {
            $writer = \Spatie\SimpleExcel\SimpleExcelWriter::streamDownload('danh_sach_vat_tu_' . date('d_m_Y') . '.xlsx');
            
            foreach ($materials as $m) {
                $writer->addRow([
                    'ID' => $m->id,
                    'Tên vật tư' => $m->name,
                    'Mã SKU' => $m->code,
                    'ĐVT' => $m->unit,
                    'Ngày tạo' => $m->created_at->format('d/m/Y')
                ]);
            }
            $writer->close();
        }, 'danh_sach_vat_tu_' . date('d_m_Y') . '.xlsx');
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
            // 1. Đọc tất cả dòng
            $rows = \Spatie\SimpleExcel\SimpleExcelReader::create($path, $extension)->getRows();
            
            $importedCount = 0;
            $skippedCount = 0;
            $duplicateCodes = [];
            
            // 2. Lấy danh sách Code từ file để check trước (Optimize query)
            // Tuy nhiên vì SimpleExcel là Stream, ta có thể check từng dòng hoặc load all. 
            // Với file nhỏ (< vài nghìn dòng), load all OK.
            
            // Collect existing codes logic:
            // Cách đơn giản: Loop từng dòng, nếu có code thì check.
            // Để tối ưu: Gom hết code trong file, query DB 1 lần lấy danh sách đã tồn tại.
            
            $allFileCodes = [];
            $rows->each(function(array $row) use (&$allFileCodes) {
                 $code = $row['Mã SKU (Tùy chọn)'] ?? ($row['Mã SKU'] ?? (isset(array_values($row)[1]) ? array_values($row)[1] : null));
                 if($code) $allFileCodes[] = trim($code);
            });

            $existingCodes = [];
            if(count($allFileCodes) > 0) {
                $existingCodes = Material::whereIn('code', $allFileCodes)->pluck('code')->toArray();
                // Normalize for comparison
                $existingCodes = array_map('trim', $existingCodes); 
            }

            // 3. Xử lý từng dòng
            $rows->each(function(array $row) use (&$importedCount, &$skippedCount, &$duplicateCodes, $existingCodes) {
                
                $name = $row['Tên vật tư (Bắt buộc)'] ?? ($row['Tên vật tư'] ?? array_values($row)[0]);
                $code = $row['Mã SKU (Tùy chọn)'] ?? ($row['Mã SKU'] ?? (isset(array_values($row)[1]) ? array_values($row)[1] : null));
                $unit = $row['ĐVT'] ?? (isset(array_values($row)[2]) ? array_values($row)[2] : 'cái');
                
                $code = $code ? trim($code) : null;

                // Skip header duplicate if user copy-pasted wrong
                if ($name === 'Tên vật tư (Bắt buộc)' || empty($name)) return;
                
                // CHECK DUPLICATE CODE
                if ($code && in_array($code, $existingCodes)) {
                    $skippedCount++;
                    $duplicateCodes[] = $code;
                    return; // Skip this row
                }
                
                // CHECK DUPLICATE IN CURRENT BATCH (Nếu file có 2 dòng cùng code mới)
                // (Optional - but good for integrity)
                
                Material::create([
                    'name' => $name,
                    'code' => $code,
                    'unit' => $unit,
                ]);
                $importedCount++;
            });
            
            if ($skippedCount > 0) {
                // Show report with duplicates
                $msg = "Đã nhập: <b>$importedCount</b><br>";
                $msg .= "Trùng lặp (Bỏ qua): <b class='text-danger'>$skippedCount</b><br>";
                $msg .= "Các mã trùng: " . implode(', ', array_unique($duplicateCodes));
                
                $this->dispatch('swal', ['title' => 'Kết quả nhập khẩu', 'text' => $msg, 'icon' => 'warning', 'html' => true]);
            } else {
                $this->dispatch('swal', ['title' => 'Hoàn tất', 'text' => "Đã nhập thành công $importedCount vật tư!", 'icon' => 'success']);
            }

        } catch (\Exception $e) {
            $this->dispatch('swal', ['title' => 'Lỗi nhập file', 'text' => $e->getMessage(), 'icon' => 'error']);
        }

        $this->reset('file_import');
    }

    public function render() {
        $materials = Material::search($this->search)->orderBy('id', 'desc')->paginate(10);
        return view('livewire.material.material-list', ['materials' => $materials])
            ->layout('layouts.admin');
    }
}
<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use App\Models\Tag;
use App\Enums\TagType;

class SupplierList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    
    // Form properties
    public $name, $code, $type_tag_id;
    public $contact_name, $phone, $email, $address;
    public $tax_code, $bank_account, $bank_name, $note;
    public $status = true;
    
    public $is_edit = false;
    public $edit_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:50',
        'type_tag_id' => 'nullable|exists:tags,id',
        'contact_name' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'tax_code' => 'nullable|string|max:20',
        'bank_account' => 'nullable|string|max:50',
        'bank_name' => 'nullable|string|max:255',
        'note' => 'nullable|string',
        'status' => 'boolean',
    ];

    public function resetForm()
    {
        $this->reset([
            'name', 'code', 'type_tag_id', 'contact_name', 
            'phone', 'email', 'address', 'tax_code', 
            'bank_account', 'bank_name', 'note', 'status',
            'is_edit', 'edit_id'
        ]);
        $this->status = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'type_tag_id' => $this->type_tag_id ?: null,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'tax_code' => $this->tax_code,
            'bank_account' => $this->bank_account,
            'bank_name' => $this->bank_name,
            'note' => $this->note,
            'status' => $this->status,
        ];

        if ($this->is_edit) {
            Supplier::find($this->edit_id)->update($data);
            $this->dispatch('swal', ['title' => 'Thành công', 'text' => 'Đã cập nhật nhà cung cấp!', 'icon' => 'success']);
        } else {
            Supplier::create($data);
            $this->dispatch('swal', ['title' => 'Thành công', 'text' => 'Đã thêm nhà cung cấp!', 'icon' => 'success']);
        }

        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) return;

        $this->edit_id = $supplier->id;
        $this->name = $supplier->name;
        $this->code = $supplier->code;
        $this->type_tag_id = $supplier->type_tag_id;
        $this->contact_name = $supplier->contact_name;
        $this->phone = $supplier->phone;
        $this->email = $supplier->email;
        $this->address = $supplier->address;
        $this->tax_code = $supplier->tax_code;
        $this->bank_account = $supplier->bank_account;
        $this->bank_name = $supplier->bank_name;
        $this->note = $supplier->note;
        $this->status = $supplier->status;
        $this->is_edit = true;

        $this->dispatch('open-modal');
    }

    public function delete($id)
    {
        Supplier::find($id)?->delete();
        $this->dispatch('swal', ['title' => 'Đã xóa', 'text' => 'Nhà cung cấp đã bị xóa.', 'icon' => 'success']);
    }

    public function toggleStatus($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            $supplier->update(['status' => !$supplier->status]);
        }
    }

    public function render()
    {
        $suppliers = Supplier::with('typeTag')
            ->search($this->search)
            ->orderByDesc('id')
            ->paginate(15);
        
        $supplierTypes = Tag::where('type', TagType::SUPPLIER)->ordered()->get();

        return view('livewire.supplier.supplier-list', [
            'suppliers' => $suppliers,
            'supplierTypes' => $supplierTypes,
        ])->layout('layouts.admin');
    }
}

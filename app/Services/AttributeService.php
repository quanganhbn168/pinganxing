<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\Storage;

class AttributeService
{
    public function getAllAttributesPaginated(int $perPage = 15)
    {
        return Attribute::with('values')->latest()->paginate($perPage);
    }
    public function getAttributesWithValues()
    {
        return Attribute::with('values')->latest()->get();
    }
    
    public function createAttribute(array $data): Attribute
    {
        return Attribute::create($data);
    }

    public function updateAttribute(Attribute $attribute, array $data): bool
    {
        return $attribute->update($data);
    }

    public function deleteAttribute(Attribute $attribute): ?bool
    {
        // Xóa các ảnh của giá trị con trước khi xóa cha
        foreach ($attribute->values as $value) {
            if ($value->image) {
                Storage::disk('public')->delete($value->image);
            }
        }
        return $attribute->delete();
    }

    public function createAttributeValue(Attribute $attribute, array $data): AttributeValue
    {
        // Nếu data['image'] là file upload (legacy), lưu vào storage
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('attribute_values', 'public');
        }
        // Nếu là string (LFM), giữ nguyên path
        
        return $attribute->values()->create($data);
    }

    public function updateAttributeValue(AttributeValue $value, array $data): bool
    {
        if (isset($data['image'])) {
            // Nếu upload file mới (legacy)
            if ($data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($value->image && !str_starts_with($value->image, '/')) {
                    Storage::disk('public')->delete($value->image);
                }
                $data['image'] = $data['image']->store('attribute_values', 'public');
            }
            // Nếu là string (LFM), cập nhật path mới. Không xóa file cũ vì LFM là thư viện chung.
        }
        return $value->update($data);
    }

    public function deleteAttributeValue(AttributeValue $value): ?bool
    {
        // Chỉ xóa file nếu nó nằm trong storage legacy (không bắt đầu bằng /)
        if ($value->image && !str_starts_with($value->image, '/')) {
            Storage::disk('public')->delete($value->image);
        }
        return $value->delete();
    }
}
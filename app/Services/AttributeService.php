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
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('attribute_values', 'public');
        }
        return $attribute->values()->create($data);
    }

    public function updateAttributeValue(AttributeValue $value, array $data): bool
    {
        if (isset($data['image'])) {
            if ($value->image) {
                Storage::disk('public')->delete($value->image);
            }
            $data['image'] = $data['image']->store('attribute_values', 'public');
        }
        return $value->update($data);
    }

    public function deleteAttributeValue(AttributeValue $value): ?bool
    {
        if ($value->image) {
            Storage::disk('public')->delete($value->image);
        }
        return $value->delete();
    }
}
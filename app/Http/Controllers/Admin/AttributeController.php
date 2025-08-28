<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeValueRequest;
use App\Http\Requests\AttributeRequest;
use App\Models\Attribute;
use App\Services\AttributeService;
use Illuminate\Http\JsonResponse;
class AttributeController extends Controller
{
    protected $attributeService;
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    public function index()
    {
        $attributes = $this->attributeService->getAllAttributesPaginated();
        return view('admin.attributes.index', compact('attributes'));
    }
    public function create()
    {
        return view('admin.attributes.create');
    }
    public function store(AttributeRequest $request)
    {
        $this->attributeService->createAttribute($request->validated());
        return redirect()->route('admin.attributes.index')->with('success', 'Tạo thuộc tính thành công!');
    }
    public function edit(Attribute $attribute)
    {
        $attribute->load('values');
        return view('admin.attributes.edit', compact('attribute'));
    }
    public function update(AttributeRequest $request, Attribute $attribute)
    {
        $this->attributeService->updateAttribute($attribute, $request->validated());
        return redirect()->route('admin.attributes.index')->with('success', 'Cập nhật thuộc tính thành công!');
    }
    public function destroy(Attribute $attribute)
    {
        $this->attributeService->deleteAttribute($attribute);
        return redirect()->route('admin.attributes.index')->with('success', 'Xóa thuộc tính thành công!');
    }
    /**
     * Xử lý yêu cầu AJAX để tạo một giá trị thuộc tính mới.
     */
    public function storeValue(StoreAttributeValueRequest $request, Attribute $attribute): JsonResponse
    {
        $validated = $request->validated();
        $newValue = $attribute->values()->create([
            'value' => $validated['name'],
        ]);
        return response()->json([
            'id'   => $newValue->id,
            'text' => $newValue->value,
        ]);
    }
}
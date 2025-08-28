<?php

namespace App\Console\Commands;

use App\Models\{Product, ProductVariant, Attribute, AttributeValue};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Support\Str;

class ImportProductsFromJson extends Command
{
    protected $signature = 'import:products-json 
        {path=import/products_grouped.json : Path relative to storage/app} 
        {--default-image=images/setting/no-image.png}
        {--default-brand-id= : Optional fallback for brand_id if your column is NOT NULL}';

    protected $description = 'Import products + variants from grouped JSON (category => [products])';

    public function handle()
    {
        $path         = $this->argument('path');
        $defaultImage = (string) $this->option('default-image');
        $defaultBrand = $this->option('default-brand-id'); // null by default

        if (!Storage::disk('local')->exists($path)) {
            $this->error("JSON not found: storage/app/{$path}");
            return self::FAILURE;
        }

        $data = json_decode(Storage::disk('local')->get($path), true);
        if (!is_array($data)) {
            $this->error('Invalid JSON content.');
            return self::FAILURE;
        }

        // Map tên danh mục -> ID
        $catMap = config('import.category_name_to_id', [
            'Dây Đai' => 1,
            'Băng Dính , Màng Chít' => 2,
            'Văn Phòng Phẩm' => 3,
            'Khăn Lau Phòng Sạch' => 4,
            'Khẩu Trang' => 5,
            'Găng  Tay Bảo Hộ' => 6,
            'Giầy Ủng Bảo Hộ' => 7,
            'Quần Áo Bảo Hộ' => 8,
            'Tem In Các Loại' => 9,
            'Xe Nâng Thủy Lực' => 10,
        ]);

        // Thuộc tính biến thể phổ biến
        $attrSize  = Attribute::firstOrCreate(['name' => 'Kích thước'], ['type' => 'text',  'is_variant_defining' => true]);
        $attrColor = Attribute::firstOrCreate(['name' => 'Màu sắc'],   ['type' => 'color', 'is_variant_defining' => true]);

        $countProducts = 0;
        $countVariants = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $categoryName => $products) {
                $categoryId = $catMap[$categoryName] ?? null;
                if (!$categoryId) {
                    $this->warn("Skip category (no ID map): {$categoryName}");
                    continue;
                }

                foreach ((array) $products as $p) {
                    $title = trim((string)($p['title'] ?? ''));
                    if ($title === '') continue;

                    $variantsData = (array)($p['variants'] ?? []);
                    $hasVariants  = count($variantsData) > 1;

                    // ----- CODE fallback (NOT NULL safe)
                    $baseCode = $this->makeBaseCode($title);
                    $code     = $this->uniqueCode($baseCode);

                    // product insert/update
                    $product = Product::updateOrCreate(
                        ['slug' => Str::slug($title)],
                        [
                            'category_id'     => $categoryId,
                            'brand_id'        => $defaultBrand ?: null, // nếu brand_id NOT NULL, truyền --default-brand-id=
                            'product_type'    => null,
                            'name'            => $title,
                            'code'            => $code, // <<<<<< FIX HERE
                            'image'           => $defaultImage,
                            'description'     => null,
                            'content'         => null,
                            'price'           => null,
                            'price_discount'  => null,
                            'stock'           => null,
                            'status'          => true,
                            'has_variants'    => $hasVariants,
                            'is_on_sale'      => false,
                            'is_featured'     => false,
                            'meta_title'      => $title,
                            'meta_description'=> null,
                            'meta_image'      => null,
                            'meta_keywords'   => null,
                        ]
                    );

                    // Nếu không có variants → tạo 1 biến thể mặc định
                    if (empty($variantsData)) {
                        $variantsData = [[ 'name' => $title ]];
                    }

                    $isFirst = true;
                    foreach ($variantsData as $vd) {
                        $vd    = (array) $vd;
                        $name  = (string)($vd['name'] ?? $title);
                        $size  = $vd['size']  ?? null;
                        $color = $vd['color'] ?? null;

                        $sku = Str::upper(Str::slug($name)).'-'.substr(md5($name.'|'.$size.'|'.$color), 0, 6);

                        $variant = ProductVariant::updateOrCreate(
                            ['product_id' => $product->id, 'sku' => $sku],
                            [
                                'price'            => 0,
                                'compare_at_price' => 0,
                                'stock'            => 0,
                                'image'            => $product->image,
                                'is_default'       => $isFirst,
                            ]
                        );

                        // Gán giá trị thuộc tính biến thể
                        $valueIds = [];
                        if ($size && trim($size) !== '') {
                            $sizeVal = AttributeValue::firstOrCreate(
                                ['attribute_id' => $attrSize->id, 'value' => trim((string)$size)]
                            );
                            $valueIds[] = $sizeVal->id;
                        }
                        if ($color && trim($color) !== '') {
                            $colorVal = AttributeValue::firstOrCreate(
                                ['attribute_id' => $attrColor->id, 'value' => $this->normalizeColor($color)]
                            );
                            $valueIds[] = $colorVal->id;
                        }
                        if ($valueIds) {
                            $variant->attributeValues()->syncWithoutDetaching($valueIds);
                        }

                        $countVariants++;
                        $isFirst = false;
                    }

                    $countProducts++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Done. Products: {$countProducts}, Variants: {$countVariants}");
        return self::SUCCESS;
    }

    // ----- Helpers -----
    protected function makeBaseCode(string $title): string
    {
        // ví dụ: "Băng Dính Bụi" -> "BANGDINH BUI" -> "BANGDINHBUI"
        $base = Str::upper(Str::slug($title, ' '));
        $base = str_replace(' ', '', $base);
        return substr($base, 0, 24); // gọn gàng
    }

    protected function uniqueCode(string $base): string
    {
        $code = $base;
        $i = 0;
        while (Product::where('code', $code)->exists()) {
            $suffix = substr(dechex(crc32($base.$i)), 0, 4);
            $code = substr($base, 0, 20).'-'.$suffix;
            $i++;
            if ($i > 50) { // đề phòng
                $code = substr($base, 0, 16).'-'.strtoupper(Str::random(6));
                break;
            }
        }
        return $code;
    }

    protected function normalizeColor(string $v): string
    {
        $k = Str::slug($v, ' ');
        $map = [
            'trang' => 'Trắng','trong'=>'Trong','duc'=>'Đục',
            'den'=>'Đen','do'=>'Đỏ','xanh'=>'Xanh','vang'=>'Vàng',
            'nau'=>'Nâu','hong'=>'Hồng','tim'=>'Tím','bac'=>'Bạc','ghi'=>'Ghi'
        ];
        return $map[$k] ?? ucfirst(trim($v));
    }
}

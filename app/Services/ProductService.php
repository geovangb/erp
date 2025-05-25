<?php
/**
 * GB Developer
 *
 * @category GB_Developer
 * @package  GB
 *
 * @copyright Copyright (c) 2025 GB Developer.
 *
 * @author Geovan Brambilla <geovangb@gmail.com>
 */

namespace App\Services;

use App\DTO\ProductData;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductService
{
    /**
     * @param ProductData $data
     * @return Product
     */
    public function create(ProductData $data): Product
    {
        $product = Product::create([
            'sku' => $data->sku,
            'name' => $data->name,
            'description' => $data->description,
            'price_of' => $data->price_of,
            'price_for' => $data->price_for,
            'status' => $data->status,
            'image' => $data->image,
        ]);

        $this->handleVariantsAndStock($product, $data);

        return $product;
    }

    /**
     * @param Product $product
     * @param ProductData $data
     * @return Product
     */
    public function update(Product $product, ProductData $data): Product
    {
        $product->update([
            'sku' => $data->sku,
            'name' => $data->name,
            'description' => $data->description,
            'price_of' => $data->price_of,
            'price_for' => $data->price_for,
            'status' => $data->status,
            'image' => $data->image,
        ]);

        $product->variants()->delete();
        $product->stock()->delete();

        $this->handleVariantsAndStock($product, $data);

        return $product;
    }

    /***
     * @param Product $product
     * @param ProductData $data
     * @return void
     */
    private function handleVariantsAndStock(Product $product, ProductData $data): void
    {
        if (!empty($data->variants)) {
            foreach ($data->variants as $variant) {
                $v = new ProductVariant($variant);
                $v->id_product = $product->id;
                $v->save();

                $product->stock()->create([
                    'variant_id' => $v->id,
                    'qtd_min' => $variant['stock_min'] ?? 0,
                    'qtd_atual' => $variant['stock_current'] ?? 0,
                ]);
            }
        } else {
            $product->stock()->create([
                'qtd_min' => $data->stock_min,
                'qtd_atual' => $data->stock_current,
            ]);
        }
    }
}

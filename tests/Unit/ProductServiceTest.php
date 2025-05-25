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

namespace Tests\Unit;

use App\DTO\ProductData;
use App\Models\Product;
use App\Models\PrdVariant;
use App\Models\Stock;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductService();
    }

    public function test_it_creates_product_with_variants_and_stock()
    {
        $dataArray = [
            'sku' => 'SKU123',
            'name' => 'Produto Teste',
            'description' => 'Descrição teste',
            'price_of' => 100.00,
            'price_for' => 90.00,
            'status' => true,
            'image' => null,
            'stock_min' => null,
            'stock_current' => null,
            'variants' => [
                [
                    'variant' => 'Tamanho',
                    'name_variant' => 'M',
                    'sku' => 'SKU-M',
                    'price_of' => 100,
                    'price_for' => 90,
                    'stock_min' => 2,
                    'stock_current' => 5,
                ],
                [
                    'variant' => 'Tamanho',
                    'name_variant' => 'G',
                    'sku' => 'SKU-G',
                    'price_of' => 110,
                    'price_for' => 95,
                    'stock_min' => 1,
                    'stock_current' => 3,
                ],
            ]
        ];

        $productData = new ProductData($dataArray);

        $product = $this->service->create($productData);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU123',
            'name' => 'Produto Teste',
        ]);

        $this->assertCount(2, $product->variants);

        foreach ($product->variants as $variant) {
            $this->assertDatabaseHas('stocks', [
                'variant_id' => $variant->id,
            ]);
        }
    }

    public function test_it_creates_product_without_variants_and_sets_general_stock()
    {
        $dataArray = [
            'sku' => 'SKU456',
            'name' => 'Produto Simples',
            'description' => null,
            'price_of' => 50,
            'price_for' => 45,
            'status' => true,
            'image' => null,
            'variants' => null,
            'stock_min' => 3,
            'stock_current' => 8,
        ];

        $productData = new ProductData($dataArray);

        $product = $this->service->create($productData);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU456',
            'name' => 'Produto Simples',
        ]);

        $this->assertCount(0, $product->variants);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'variant_id' => null,
            'qtd_min' => 3,
            'qtd_atual' => 8,
        ]);
    }
}

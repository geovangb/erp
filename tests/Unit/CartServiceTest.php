<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Product;
use App\Services\CartService;
use App\Repositories\CouponRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Mockery;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $cartService;
    protected $couponRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock do repositório de cupons
        $this->couponRepository = Mockery::mock(CouponRepository::class);
        $this->cartService = new CartService($this->couponRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_subtotal()
    {
        $cart = [
            '1_null' => ['quantity' => 2, 'price' => 10.50],
            '2_null' => ['quantity' => 1, 'price' => 20.00],
        ];

        $subtotal = $this->cartService->calculateSubtotal($cart);

        $this->assertEquals(41.00, $subtotal);
    }

    public function test_calculate_freight_free_above_200()
    {
        $frete = $this->cartService->calculateFreight('SP', 250.00);
        $this->assertEquals(0, $frete);
    }

    public function test_calculate_freight_with_state_rules()
    {
        $this->assertEquals(10.00, $this->cartService->calculateFreight('SP', 100));
        $this->assertEquals(12.00, $this->cartService->calculateFreight('RJ', 100));
        $this->assertEquals(20.00, $this->cartService->calculateFreight('MG', 100));
    }

    public function test_add_product_to_cart_new_item()
    {
        $product = Product::factory()->make(['id' => 1, 'name' => 'Produto X', 'price_for' => 100.00]);

        $cart = [];
        $cart = $this->cartService->addProductToCart($cart, $product, null);

        $this->assertArrayHasKey('1_', $cart);
        $this->assertEquals(1, $cart['1_']['quantity']);
    }

    public function test_add_product_to_cart_existing_item_increments_quantity()
    {
        $product = Product::factory()->make(['id' => 1, 'name' => 'Produto X', 'price_for' => 100.00]);

        $cart = ['1_' => [
            'product_id' => 1,
            'variant_id' => null,
            'name' => 'Produto X',
            'price' => 100.00,
            'quantity' => 1,
        ]];

        $cart = $this->cartService->addProductToCart($cart, $product, null);

        $this->assertEquals(2, $cart['1_']['quantity']);
    }

    public function test_apply_coupon_invalid()
    {
        $this->couponRepository
            ->shouldReceive('findValidByCode')
            ->with('INVALID')
            ->andReturn(null);

        $result = $this->cartService->applyCouponToCart('INVALID', 100);

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Cupom inválido ou expirado.', $result['error']);
    }

    public function test_apply_coupon_below_minimum_cart_value()
    {
        $coupon = new Coupon([
            'code' => 'DESCONTO10',
            'discount' => 10,
            'min_cart_value' => 200,
            'valid_until' => now()->addDay()
        ]);

        $this->couponRepository
            ->shouldReceive('findValidByCode')
            ->with('DESCONTO10')
            ->andReturn($coupon);

        $result = $this->cartService->applyCouponToCart('DESCONTO10', 100);

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('valor mínimo', $result['error']);
    }

    public function test_apply_coupon_successfully()
    {
        $coupon = new Coupon([
            'code' => 'DESCONTO20',
            'discount' => 20,
            'min_cart_value' => 100,
            'valid_until' => now()->addDay()
        ]);

        $this->couponRepository
            ->shouldReceive('findValidByCode')
            ->with('DESCONTO20')
            ->andReturn($coupon);

        $result = $this->cartService->applyCouponToCart('DESCONTO20', 150);

        $this->assertArrayHasKey('success', $result);
        $this->assertEquals('Cupom aplicado com sucesso.', $result['success']);
        $this->assertEquals('DESCONTO20', session('coupon.code'));
        $this->assertEquals(20, session('coupon.discount'));
    }
}

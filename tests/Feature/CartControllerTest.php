<?php

namespace Tests\Feature;

use App\Http\Controllers\CartController;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Mockery;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_cart_summary()
    {
        $mock = Mockery::mock(CartService::class);
        $mock->shouldReceive('getCart')->once()->andReturn([
            '1_null' => ['quantity' => 2, 'price' => 50]
        ]);
        $mock->shouldReceive('calculateSubtotal')->once()->andReturn(100);
        $mock->shouldReceive('calculateFreight')->once()->with(null, 100)->andReturn(20);

        $this->app->instance(CartService::class, $mock);

        $response = $this->get(route('cart.view'));

        $response->assertStatus(200);
        $response->assertViewHasAll(['cart', 'subtotal', 'frete', 'total']);
    }

    public function test_add_adds_product_to_cart()
    {
        $product = Product::factory()->create(['price_for' => 150]);

        $mock = Mockery::mock(CartService::class);
        $mock->shouldReceive('getCart')->once()->andReturn([]);
        $mock->shouldReceive('addProductToCart')
            ->once()
            ->andReturn([
                '1_null' => [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'name' => $product->name,
                    'price' => 150,
                    'quantity' => 1,
                ]
            ]);

        $this->app->instance(CartService::class, $mock);

        $response = $this->post(route('cart.add', $product), [
            'variant_id' => null,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Produto adicionado ao carrinho.');
    }

    public function test_apply_coupon_success()
    {
        $mock = Mockery::mock(CartService::class);
        $mock->shouldReceive('getCart')->once()->andReturn([]);
        $mock->shouldReceive('calculateSubtotal')->once()->andReturn(120);
        $mock->shouldReceive('applyCouponToCart')->once()->andReturn([
            'success' => 'Cupom aplicado com sucesso.'
        ]);

        $this->app->instance(CartService::class, $mock);

        $response = $this->post(route('cart.applyCoupon'), [
            'coupon_code' => 'DESCONTO10'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Cupom aplicado com sucesso.');
    }

    public function test_apply_coupon_invalid()
    {
        $mock = Mockery::mock(CartService::class);
        $mock->shouldReceive('getCart')->once()->andReturn([]);
        $mock->shouldReceive('calculateSubtotal')->once()->andReturn(50);
        $mock->shouldReceive('applyCouponToCart')->once()->andReturn([
            'error' => 'Cupom inválido ou expirado.'
        ]);

        $this->app->instance(CartService::class, $mock);

        $response = $this->post(route('cart.applyCoupon'), [
            'coupon_code' => 'INVALID'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cupom inválido ou expirado.');
    }

    public function test_remove_item_from_cart()
    {
        Session::put('cart', [
            '1_null' => ['product_id' => 1, 'quantity' => 1, 'price' => 100]
        ]);

        $response = $this->delete(route('cart.clear', '1_null'));

        $response->assertRedirect(route('cart.view'));
        $response->assertSessionHas('success', 'Carrinho esvaziado.');
        $this->assertEmpty(session('cart'));
    }

    public function test_set_payment_method_success()
    {
        $response = $this->post(route('cart.setPaymentMethod'), [
            'payment_method' => 'pix',
        ]);

        $response->assertRedirect(route('cart.view'));
        $response->assertSessionHas('success', 'Método de pagamento atualizado.');
        $this->assertEquals('pix', session('payment_method'));
    }

    public function test_set_payment_method_invalid()
    {
        $response = $this->post(route('cart.setPaymentMethod'), [
            'payment_method' => 'bitcoin',
        ]);

        $response->assertSessionHasErrors('payment_method');
        $this->assertNull(session('payment_method'));
    }

}

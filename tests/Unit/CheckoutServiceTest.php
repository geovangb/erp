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

use App\DTO\CheckoutData;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Mockery;
use App\Models\User;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_process_order_creates_customer_order_and_sends_email()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $service = new CheckoutService();

        $checkoutData = new CheckoutData([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '123456789',
            'cep' => '12345-678',
            'address' => 'Rua A, 123',
        ]);

        $cart = [
            [
                'name' => 'Produto 1',
                'variant_id' => 'V1',
                'quantity' => 2,
                'price' => 50,
            ],
            [
                'name' => 'Produto 2',
                'variant_id' => null,
                'quantity' => 1,
                'price' => 30,
            ],
        ];

        Auth::loginUsingId(1);

        $order = $service->processOrder($checkoutData, $cart, 'pix');

        $this->assertDatabaseHas('customers', ['email' => 'joao@example.com']);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'user_id' => 1, 'payment_method' => 'pix']);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_name' => 'Produto 1',
            'quantity' => 2,
            'price' => 50,
        ]);

        Mail::assertSent(\App\Mail\OrderConfirmationMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }

    public function test_process_order_throws_exception_if_cart_empty()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Carrinho vazio');

        $service = new CheckoutService();

        $checkoutData = new CheckoutData([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '123456789',
            'cep' => '12345-678',
            'address' => 'Rua A, 123',
        ]);

        $cart = [];

        $service->processOrder($checkoutData, $cart, 'pix');
    }
}

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

use App\DTO\WebhookData;
use App\Models\Order;
use App\Services\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class WebhookServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_order_status()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'status' => 'pendente',
            'user_id' => $user->id,
        ]);

        $data = new WebhookData([
            'order_id' => $order->id,
            'status' => 'pago',
        ]);

        // Act
        $service = new WebhookService();
        $response = $service->handle($data);

        // Assert
        $this->assertEquals('Status do pedido atualizado com sucesso.', $response);
    }

    /** @test */
    public function it_deletes_order_if_status_is_cancelado()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'status' => 'pendente',
            'user_id' => $user->id,
        ]);

        $data = new WebhookData([
            'order_id' => $order->id,
            'status' => 'cancelado',
        ]);

        $service = new WebhookService();
        $response = $service->handle($data);

        $this->assertEquals('Pedido cancelado e removido com sucesso.', $response);
    }
}

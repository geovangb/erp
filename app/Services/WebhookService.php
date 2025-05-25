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

use App\DTO\WebhookData;
use App\Models\Order;

class WebhookService
{
    public function handle(WebhookData $data): string
    {
        $order = Order::find($data->order_id);

        if (!$order) {
            throw new \Exception('Pedido não encontrado');
        }

        if ($data->status === 'cancelado') {
            $order->delete();
            return 'Pedido cancelado e removido com sucesso.';
        }

        $order->status = $data->status;
        $order->save();

        return 'Status do pedido atualizado com sucesso.';
    }
}

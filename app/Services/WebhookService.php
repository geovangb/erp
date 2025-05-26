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
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\OrderRepository;

class WebhookService
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle(WebhookData $data): string
    {
        $order = $this->orderRepository->findById($data->order_id);

        if (!$order) {
            throw new Exception(__('messages.order_not_found'));
        }

        DB::transaction(function () use ($order, $data) {
            if ($data->status === 'cancelado') {
                $order->delete();
            } else {
                $order->status = $data->status;
                $order->save();
            }
        });

        return $data->status === 'cancelado'
            ? __('messages.order_cancelled')
            : __('messages.order_status_updated');
    }
}

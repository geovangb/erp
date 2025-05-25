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

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        if ($request->header('X-WEBHOOK-KEY') !== env('WEBHOOK_SECRET')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'status' => 'required|string',
        ]);

        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['error' => 'Pedido não encontrado'], 404);
        }

        if (strtolower($request->status) === 'cancelado') {
            $order->delete();
            return response()->json(['message' => 'Pedido cancelado e removido com sucesso.']);
        }

        $order->status = strtolower($request->status);
        $order->save();

        return response()->json(['message' => 'Status do pedido atualizado com sucesso.']);
    }
}

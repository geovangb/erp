<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('customer')->latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'customer');

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:recebido,processando,enviado,concluido',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.show', $order)->with('success', 'Status atualizado com sucesso!');
    }
}

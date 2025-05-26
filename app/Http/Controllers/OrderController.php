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

use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $orders = $this->orderRepository->getAllWithCustomer();

        return view('orders.index', compact('orders'));
    }

    /**
     * @param Order $order
     * @return Application|Factory|View
     */
    public function show(Order $order): Factory|View|Application
    {
        $order->load('items', 'customer');

        return view('orders.show', compact('order'));
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:recebido,processando,enviado,concluido',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.show', $order)->with('success', __('messages.status_updated'));
    }
}

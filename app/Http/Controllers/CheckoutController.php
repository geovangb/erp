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

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'cep' => 'required|string',
            'address' => 'required|string',
        ]);

        $cart = session('cart', []);

        if (count($cart) === 0) {
            return redirect()->route('cart.view')->with('error', 'Carrinho vazio');
        }

        $payment_method = session('payment_method', 'não informado');

        $subtotal = collect($cart)->sum(fn ($item) => $item['quantity'] * $item['price']);

        $frete = 20.00;

        if ($subtotal >= 52 && $subtotal <= 166.59) {
            $frete = 15.00;
        }

        if ($subtotal > 200) {
            $frete = 0.00;
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'cep' => $request->cep,
            'address' => $request->address,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_cep' => $customer->cep,
            'customer_address' => $customer->address,
            'freight' => $frete,
            'payment_method' => $payment_method,
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'shipping' => $frete,
            'total' => $subtotal + $frete,
            'status' => 'recebido',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $item['name'],
                'variant_name' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $order->load('items');

        if (!empty($customer->email)) {
            logger('Enviando e-mail para: ' . $customer->email);
            Mail::to($customer->email)->send(new OrderConfirmationMail($order));
        }

        Session::forget('cart');
        Session::forget('payment_method');

        return redirect()->route('cart.view')->with('success', 'Pedido finalizado com sucesso!');
    }
}

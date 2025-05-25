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

use App\DTO\CheckoutData;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\OrderConfirmationMail;

class CheckoutService
{
    /**
     * @param CheckoutData $checkoutData
     * @param array $cart
     * @param string|null $paymentMethod
     * @return mixed
     * @throws Exception
     */
    public function processOrder(CheckoutData $checkoutData, array $cart, ?string $paymentMethod): mixed
    {
        if (count($cart) === 0) {
            throw new Exception('Carrinho vazio');
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['quantity'] * $item['price']);
        $frete = $this->returnFrete($subtotal);

        $customerData = $this->customerData($checkoutData);
        $customer = Customer::create($customerData);
        $orderData = $this->orderData($customer, $frete, $paymentMethod, $subtotal);
        $order = Order::create($orderData);

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
            Mail::to($customer->email)->send(new OrderConfirmationMail($order));
        }

        Session::forget('cart');
        Session::forget('payment_method');

        return $order;
    }

    /**
     * @param $subtotal
     * @return float
     */
    public function returnFrete($subtotal): float
    {
        $frete = 20.00;
        if ($subtotal >= 52 && $subtotal <= 166.59) {
            $frete = 15.00;
        }
        if ($subtotal > 200) {
            $frete = 0.00;
        }

        return $frete;
    }

    /**
     * @param $checkoutData
     * @return array
     */
    public function customerData($checkoutData): array
    {
        return [
            'name' => $checkoutData->name,
            'email' => $checkoutData->email,
            'phone' => $checkoutData->phone,
            'cep' => $checkoutData->cep,
            'address' => $checkoutData->address,
        ];
    }

    /**
     * @param $customer
     * @param $frete
     * @param $paymentMethod
     * @param $subtotal
     * @return array
     */
    public function orderData($customer, $frete, $paymentMethod, $subtotal): array
    {
        return [
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_cep' => $customer->cep,
            'customer_address' => $customer->address,
            'freight' => $frete,
            'payment_method' => $paymentMethod ?? 'não informado',
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'shipping' => $frete,
            'total' => $subtotal + $frete,
            'status' => 'recebido',
        ];
    }
}

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

use App\DTO\CheckoutData;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    /**
     * @param CheckoutService $checkoutService
     */
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'cep' => 'required|string',
            'address' => 'required|string',
        ]);

        $checkoutData = new CheckoutData($validated);
        $cart = session('cart', []);
        $paymentMethod = session('payment_method', null);

        try {
            $order = $this->checkoutService->processOrder($checkoutData, $cart, $paymentMethod);
        } catch (\Exception $e) {
            return redirect()->route('cart.view')->with('error', $e->getMessage());
        }

        return redirect()->route('cart.view')->with('success', __('messages.order_completed'));
    }
}

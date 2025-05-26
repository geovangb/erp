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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\CartService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Models\Coupon;

class CartController extends Controller
{
    protected $cartService;

    /**
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @return Application|Factory|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): Factory|View|Application
    {
        $cart = $this->cartService->getCart();
        $subtotal = $this->cartService->calculateSubtotal($cart);
        $uf = session('frete_uf');
        $frete = $this->cartService->calculateFreight($uf, $subtotal);
        $total = $subtotal + $frete;

        return view('cart.index', compact('cart', 'subtotal', 'frete', 'total'));
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $variantId = $request->input('variant_id');
        $cart = $this->cartService->getCart();
        $cart = $this->cartService->addProductToCart($cart, $product, $variantId);

        session()->put('cart', $cart);

        return redirect()->back()->with('success', __('messages.product_added_to_cart'));
    }

    /**
     * @param $key
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function remove($key): RedirectResponse
    {
        $cart = $this->cartService->getCart();
        unset($cart[$key]);
        session()->put('cart', $cart);

        return redirect()->route('cart.view')->with('success',  __('messages.item_removed_from_cart'));
    }

    /**
     * @return RedirectResponse
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.view')->with('success',  __('messages.cart_emptied'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateFreight(Request $request)
    {
        $uf = $request->input('uf');
        session(['frete_uf' => $uf]);

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function setPaymentMethod(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:pix,boleto,credito,debito',
        ]);

        session(['payment_method' => $request->payment_method]);

        return redirect()->route('cart.view')->with('success', __('messages.payment_method_updated'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyCoupon(Request $request): RedirectResponse
    {
        $cart = $this->cartService->getCart();
        $subtotal = $this->cartService->calculateSubtotal($cart);

        $result = $this->cartService->applyCouponToCart($request->coupon_code, $subtotal);

        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        return redirect()->back()->with('success', $result['success']);
    }
}

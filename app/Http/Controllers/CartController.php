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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CartController extends Controller
{
    /****
     * @return Application|Factory|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }

        if ($subtotal > 200) {
            $frete = 0;
        } else {
            $uf = session('frete_uf');

            switch ($uf) {
                case 'SP':
                    $frete = 10.00;
                    break;
                case 'RJ':
                    $frete = 12.00;
                    break;
                default:
                    $frete = 20.00;
            }
        }

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
        $key = $product->id . '_' . $variantId;

        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += 1;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'name' => $product->name,
                'price' => $product->price_for,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho.');
    }

    /**
     * @param $key
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function remove($key): RedirectResponse
    {
        $cart = session()->get('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);

        return redirect()->route('cart.view')->with('success', 'Item removido do carrinho.');
    }

    /**
     * @return RedirectResponse
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.view')->with('success', 'Carrinho esvaziado.');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateFreight(Request $request): JsonResponse
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

        return redirect()->route('cart.view')->with('success', 'Método de pagamento atualizado.');
    }
}

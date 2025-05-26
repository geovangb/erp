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

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Repositories\CouponRepository;

class CartService
{
    /**
     * @param CouponRepository $couponRepository
     */
    public function __construct(
        protected CouponRepository $couponRepository
    ) {}

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCart()
    {
        return session()->get('cart', []);
    }

    /**
     * @param array $cart
     * @return float
     */
    public function calculateSubtotal(array $cart): float
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }
        return $subtotal;
    }

    /**
     * @param string|null $uf
     * @param float $subtotal
     * @return float
     */
    public function calculateFreight(?string $uf, float $subtotal): float
    {
        if ($subtotal > 200) {
            return 0;
        }

        switch ($uf) {
            case 'SP':
                return 10.00;
            case 'RJ':
                return 12.00;
            default:
                return 20.00;
        }
    }

    /**
     * @param array $cart
     * @param $product
     * @param int|null $variantId
     * @return array
     */
    public function addProductToCart(array $cart, $product, ?int $variantId): array
    {
        $key = $product->id . '_' . $variantId;

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

        return $cart;
    }

    /**
     * @param string $couponCode
     * @param float $subtotal
     * @return string[]
     */
    public function applyCouponToCart(string $couponCode, float $subtotal): array
    {
        $coupon = $this->couponRepository->findValidCoupon($couponCode);

        if (!$coupon) {
            return ['error' => 'Cupom inválido ou expirado.'];
        }

        if ($subtotal < $coupon->min_cart_value) {
            return ['error' => 'Este cupom exige um valor mínimo de R$ ' . number_format($coupon->min_cart_value, 2, ',', '.')];
        }

        session([
            'coupon' => [
                'code' => $coupon->code,
                'discount' => $coupon->discount,
            ]
        ]);

        return ['success' => 'Cupom aplicado com sucesso.'];
    }
}

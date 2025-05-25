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

use App\Models\Coupon;
use App\DTO\CouponData;

class CouponService
{
    /**
     * @param CouponData $data
     * @return Coupon
     */
    public function createCoupon(CouponData $data): Coupon
    {
        return Coupon::create([
            'code' => $data->code,
            'discount' => $data->discount,
            'min_cart_value' => $data->min_cart_value,
            'valid_until' => $data->valid_until,
        ]);
    }

    /**
     * @param Coupon $coupon
     * @param CouponData $data
     * @return Coupon
     */
    public function updateCoupon(Coupon $coupon, CouponData $data): Coupon
    {
        $coupon->update([
            'code' => $data->code,
            'discount' => $data->discount,
            'min_cart_value' => $data->min_cart_value,
            'valid_until' => $data->valid_until,
        ]);

        return $coupon;
    }
}

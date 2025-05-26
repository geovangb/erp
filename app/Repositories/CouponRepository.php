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

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CouponRepository
{
    /**
     * @return Collection
     */
    public function getAllOrderedByValidity(): Collection
    {
        return Coupon::orderBy('valid_until', 'desc')->get();
    }

    public function findValidCoupon(string $code): ?Coupon
    {
        return Coupon::where('code', $code)
            ->whereDate('valid_until', '>=', Carbon::now())
            ->first();
    }
}

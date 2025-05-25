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

namespace App\DTO;

class CouponData
{
    public string $code;
    public float $discount;
    public float $min_cart_value;
    public string $valid_until;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->code = $data['code'];
        $this->discount = (float) $data['discount'];
        $this->min_cart_value = (float) $data['min_cart_value'];
        $this->valid_until = $data['valid_until'];
    }
}

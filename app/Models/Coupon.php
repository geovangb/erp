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

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'valid_until',
        'min_cart_value',
    ];

    /**
     * @param $subtotal
     * @return bool
     */
    public function isValid($subtotal)
    {
        return $this->valid_until >= now()->toDateString() && $subtotal >= $this->min_cart_value;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'discount', 'min_cart_value', 'valid_until'];

    public function isValid($subtotal)
    {
        return $this->valid_until >= now()->toDateString() && $subtotal >= $this->min_cart_value;
    }
}

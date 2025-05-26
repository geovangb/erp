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

use App\Models\Order;

class OrderRepository
{
    public function getAllWithCustomer()
    {
        return Order::with('customer')->latest()->get();
    }

    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }
}

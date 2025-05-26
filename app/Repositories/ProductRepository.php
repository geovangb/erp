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

use App\Models\Product;

class ProductRepository
{
    public function paginateWithVariants(int $perPage = 10)
    {
        return Product::with('variants')->paginate($perPage);
    }
}

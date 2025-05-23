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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrdVariant extends Model
{
    use HasFactory;

    protected $table = 'prd_variants';

    protected $fillable = [
        'id_product', 'variant', 'name_variant', 'sku', 'stock', 'price', 'price_for', 'status'
    ];

    /**
     * @param array $data
     * @return void
     */
    private static function create(array $data)
    {
        $variant = self::create($data);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function createVariant(array $data)
    {
        return self::create($data);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function updateVariant(array $data)
    {
        $this->update($data);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function deleteVariant(): ?bool
    {
        return $this->delete();
    }

    /**
     * @return HasOne
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'variant_id');
    }
}

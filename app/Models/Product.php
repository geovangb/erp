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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'price_of',
        'price_for',
        'status',
        'image',
    ];

    /**
     * @return HasMany
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'id_product');
    }

    /**
     * @return HasMany
     */
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id');
    }

    /**
     * @param array $data
     * @param array $variants
     * @return null
     */
    public static function createWithVariants(array $data, array $variants = [])
    {
        $product = self::create($data);

        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }

        return $product;
    }

    /**
     * @param $id
     * @return Builder|Builder[]|Collection|Model|null
     */
    public static function findWithVariants($id)
    {
        return self::with('variants')->findOrFail($id);
    }

    /**
     * @param array $data
     * @param array $variants
     * @return $this
     */
    public function updateWithVariants(array $data, array $variants = []): Product
    {
        $this->update($data);

        // Remove antigas e adiciona novas
        $this->variants()->delete();

        foreach ($variants as $variant) {
            $this->variants()->create($variant);
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function deleteWithVariants(): ?bool
    {
        $this->variants()->delete();

        return $this->delete();
    }
}

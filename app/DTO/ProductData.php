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

use Illuminate\Http\Request;

class ProductData
{
    public string $sku;
    public string $name;
    public ?string $description;
    public float $price_of;
    public float $price_for;
    public bool $status;
    public ?string $image;
    public ?array $variants;
    public ?int $stock_min;
    public ?int $stock_current;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->sku = $data['sku'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
        $this->price_of = $data['price_of'];
        $this->price_for = $data['price_for'];
        $this->status = (bool) $data['status'];
        $this->image = $data['image'] ?? null;
        $this->variants = $data['variants'] ?? null;
        $this->stock_min = $data['stock_min'] ?? null;
        $this->stock_current = $data['stock_current'] ?? null;
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        $validated = $request->validate([
            'sku' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'price_of' => 'required|numeric',
            'price_for' => 'required|numeric',
            'status' => 'required|boolean',
            'image' => 'nullable|image',
            'variants' => 'nullable|array',
            'stock_min' => 'nullable|integer',
            'stock_current' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        return new self($validated);
    }
}

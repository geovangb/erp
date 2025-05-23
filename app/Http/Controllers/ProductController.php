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

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PrdVariant;
use App\Models\Stock;

class ProductController extends Controller
{
    const PRODUCTS_INDEX = 'products.index';
    const PRODUCTS_CREATE = 'products.create';
    const PRODUCTS_EDIT = 'products.edit';

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $products = Product::with('variants')->paginate(10);

        return view(self::PRODUCTS_INDEX, compact('products'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view(self::PRODUCTS_CREATE);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {

        $validated = $request->validate([
            'sku' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'price_of' => 'required|numeric',
            'price_for' => 'required|numeric',
            'status' => 'required|boolean',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $v = new PrdVariant($variant);
                $v->id_product = $product->id;
                $v->save();

                if (isset($variant['stock_min']) || isset($variant['stock_current'])) {
                    $product->stock()->create([
                        'variant_id' => $v->id,
                        'qtd_min' => $variant['stock_min'] ?? 0,
                        'qtd_atual' => $variant['stock_current'] ?? 0,
                    ]);
                }
            }
        } else {
            $product->stock()->create([
                'qtd_min' => $request->stock_min ?? 0,
                'qtd_atual' => $request->stock_current ?? 0,
            ]);
        }

        return redirect()->route(self::PRODUCTS_INDEX)->with('success', 'Produto criado com sucesso!');
    }

    /**
     * @param Product $product
     * @return Application|Factory|View
     */
    public function show(Product $product)
    {
        $product->load('variants');

        return view('products.show', compact('product'));
    }

    /**
     * @param Product $product
     * @return Application|Factory|View
     */
    public function edit(Product $product)
    {
        $product->load('variants');

        return view('products.edit', compact('product'));
    }

    /***
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required',
            'name' => 'required',
            'description' => 'nullable',
            'price_of' => 'required|numeric',
            'price_for' => 'required|numeric',
            'status' => 'required|boolean',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);
        $product->variants()->delete();

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $v = new PrdVariant($variant);
                $v->id_product = $product->id;
                $v->save();

                if (isset($variant['stock_min']) || isset($variant['stock_current'])) {
                    $product->stock()->create([
                        'variant_id' => $v->id,
                        'qtd_min' => $variant['stock_min'] ?? 0,
                        'qtd_atual' => $variant['stock_current'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->variants()->delete();
        $product->stock()->delete();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto deletado com sucesso!');
    }
}

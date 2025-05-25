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

use App\DTO\ProductData;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    const PRODUCTS_INDEX = 'products.index';
    const PRODUCTS_CREATE = 'products.create';
    const PRODUCTS_EDIT = 'products.edit';

    protected ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $products = Product::with('variants')->paginate(10);
        return view(self::PRODUCTS_INDEX, compact('products'));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return view(self::PRODUCTS_CREATE);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $data = ProductData::fromRequest($request);
        $this->productService->create($data);

        return redirect()->route(self::PRODUCTS_INDEX)->with('success', 'Produto criado com sucesso!');
    }

    /**
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $product->load('variants');
        return view('products.show', compact('product'));
    }

    /**
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        $product->load('variants');
        return view(self::PRODUCTS_EDIT, compact('product'));
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = ProductData::fromRequest($request);
        $this->productService->update($product, $data);

        return redirect()->route(self::PRODUCTS_INDEX)->with('success', 'Produto atualizado com sucesso!');
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

        return redirect()->route(self::PRODUCTS_INDEX)->with('success', 'Produto deletado com sucesso!');
    }
}

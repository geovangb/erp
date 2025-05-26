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

use App\DTO\CouponData;
use App\Services\CouponService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Repositories\CouponRepository;

class CouponController extends Controller
{
    protected CouponService $couponService;

    /**
     * @param CouponService $couponService
     */
    public function __construct(
        CouponService $couponService,
        protected CouponRepository $couponRepository
    ) {
        $this->couponService = $couponService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $coupons = $this->couponRepository->getAllOrderedByValidity();

        return view('coupons.index', compact('coupons'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create(): Factory|View|Application
    {
        return view('coupons.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'discount' => 'required|numeric|min:0',
            'min_cart_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after_or_equal:today',
        ]);

        $couponData = new CouponData($data);
        $this->couponService->createCoupon($couponData);

        return redirect()->route('coupons.index')->with('success', 'Cupom criado com sucesso!');
    }

    /**
     * @param Coupon $coupon
     * @return Application|Factory|View
     */
    public function edit(Coupon $coupon): Factory|View|Application
    {
        return view('coupons.edit', compact('coupon'));
    }

    /**
     * @param Request $request
     * @param Coupon $coupon
     * @return RedirectResponse
     */
    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric|min:0',
            'min_cart_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after_or_equal:today',
        ]);

        $couponData = new CouponData($data);
        $this->couponService->updateCoupon($coupon, $couponData);

        return redirect()->route('coupons.index')->with('success', 'Cupom atualizado com sucesso!');
    }

    /**
     * @param Coupon $coupon
     * @return RedirectResponse
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('coupons.index')->with('success', 'Cupom excluído com sucesso!');
    }
}

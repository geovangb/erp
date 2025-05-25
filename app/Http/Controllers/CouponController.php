<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('valid_until', 'desc')->get();
        return view('coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'discount' => 'required|numeric|min:0',
            'min_cart_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after_or_equal:today',
        ]);

        Coupon::create($request->all());

        return redirect()->route('coupons.index')->with('success', 'Cupom criado com sucesso!');
    }

    public function edit(Coupon $coupon)
    {
        return view('coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric|min:0',
            'min_cart_value' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after_or_equal:today',
        ]);

        $coupon->update($request->all());

        return redirect()->route('coupons.index')->with('success', 'Cupom atualizado com sucesso!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('coupons.index')->with('success', 'Cupom excluído com sucesso!');
    }
}

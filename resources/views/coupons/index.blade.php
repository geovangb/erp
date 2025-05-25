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
?>

@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h2 class="mb-4">Lista de Cupons</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3 text-end">
            <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Novo Cupom
            </a>
        </div>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Código</th>
                <th>Desconto (R$)</th>
                <th>Valor Mínimo (R$)</th>
                <th>Validade</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($coupons as $coupon)
                <tr>
                    <td>{{ $coupon->code }}</td>
                    <td>R$ {{ number_format($coupon->discount, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($coupon->min_cart_value, 2, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($coupon->valid_until)->format('d/m/Y') }}</td>
                    <td class="d-flex gap-2">
                        <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cupom?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Nenhum cupom cadastrado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection

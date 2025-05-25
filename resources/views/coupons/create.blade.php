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
        <h2 class="mb-4">Novo Cupom</h2>

        <form action="{{ route('coupons.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="code" class="form-label">Código</label>
                <input type="text" name="code" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label">Desconto (R$)</label>
                <input type="number" step="0.01" name="discount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="min_cart_value" class="form-label">Valor Mínimo no Carrinho (R$)</label>
                <input type="number" step="0.01" name="min_cart_value" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="valid_until" class="form-label">Validade</label>
                <input type="date" name="valid_until" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection

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
        <h2>Lista de Produtos</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-end mb-3 gap-2">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Novo Produto
            </a>
            <a href="{{ route('cart.view') }}" class="btn btn-outline-primary">
                <i class="bi bi-cart3"></i> Ver Carrinho
            </a>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Img</th>
                <th>SKU</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Preço Por</th>
                <th>Status</th>
                <th>Cart</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        @endif
                    </td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>R$ {{ number_format($product->price_of, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($product->price_for, 2, ',', '.') }}</td>
                    <td>
                        @if($product->status)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-secondary">Inativo</span>
                        @endif
                    </td>
                    <td>
                        @if($product->variants->count())
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                <select name="variant_id" class="form-select form-select-sm w-auto" required>
                                    <option value="">Variação</option>
                                    @foreach($product->variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->variant }} - {{ $variant->name_variant }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </form>
                        @else
                            <small class="text-muted">Sem variações</small>
                        @endif
                    </td>
                    <td class="d-flex justify-content-between">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-start gap-1 mb-1">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

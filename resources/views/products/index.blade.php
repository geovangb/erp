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

        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Novo Produto</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Preço Por</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
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
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

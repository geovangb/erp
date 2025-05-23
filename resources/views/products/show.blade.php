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

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Detalhes do Produto</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">{{ $product->name }}</h4>

                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                <p><strong>Descrição:</strong> {{ $product->description }}</p>
                <p><strong>Preço De:</strong> R$ {{ number_format($product->price_de, 2, ',', '.') }}</p>
                <p><strong>Preço Por:</strong> R$ {{ number_format($product->price_por, 2, ',', '.') }}</p>
                <p><strong>Status:</strong> {{ $product->status ? 'Ativo' : 'Inativo' }}</p>
                <p><strong>Criado em:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>

                @if ($product->image)
                    <div class="mt-3">
                        <strong>Imagem:</strong><br>
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-width: 300px;">
                    </div>
                @endif
            </div>
        </div>

        <h4>Variações</h4>
        @if ($product->variants->count())
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nome da Variação</th>
                        <th>SKU</th>
                        <th>Estoque</th>
                        <th>Preço</th>
                        <th>Preço Por</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($product->variants as $variant)
                        <tr>
                            <td>{{ $variant->variant }}</td>
                            <td>{{ $variant->name_variant }}</td>
                            <td>{{ $variant->sku }}</td>
                            <td>{{ $variant->stock }}</td>
                            <td>R$ {{ number_format($variant->price, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($variant->price_for, 2, ',', '.') }}</td>
                            <td>{{ $variant->status ? 'Ativo' : 'Inativo' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">Nenhuma variação cadastrada.</p>
        @endif

        <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Voltar à Lista</a>
    </div>
@endsection

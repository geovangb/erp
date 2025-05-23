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
        <h2>Editar Produto</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">SKU</label>
                <input type="text" name="sku" value="{{ $product->sku }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control">{{ $product->description }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Preço De</label>
                <input type="number" name="price_of" value="{{ $product->price_of }}" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Preço Por</label>
                <input type="number" name="price_for" value="{{ $product->price_for }}" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $product->status ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ !$product->status ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagem Atual</label><br>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Imagem" width="120">
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">Nova Imagem</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Estoque Mínimo</label>
                <input type="number" name="stock_min" value="{{ $product->stock_min ?? '' }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Estoque Atual</label>
                <input type="number" name="stock_current" value="{{ $product->stock_current ?? '' }}" class="form-control">
            </div>

            <hr>
            <h4>Variações</h4>
            <div id="variants-container">
                @foreach($product->variants as $i => $variant)
                    <div class="card mb-3 p-3">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <input name="variants[{{ $i }}][variant]" value="{{ $variant->variant }}" class="form-control" placeholder="Tipo">
                            </div>
                            <div class="col-md-3">
                                <input name="variants[{{ $i }}][name_variant]" value="{{ $variant->name_variant }}" class="form-control" placeholder="Nome da Variação">
                            </div>
                            <div class="col-md-2">
                                <input name="variants[{{ $i }}][sku]" value="{{ $variant->sku }}" class="form-control" placeholder="SKU">
                            </div>
                            <div class="col-md-1">
                                <input name="variants[{{ $i }}][stock]" type="number" value="{{ $variant->stock }}" class="form-control" placeholder="Estoque">
                            </div>
                            <div class="col-md-2">
                                <input name="variants[{{ $i }}][price]" type="number" step="0.01" value="{{ $variant->price }}" class="form-control" placeholder="Preço">
                            </div>
                            <div class="col-md-2">
                                <input name="variants[{{ $i }}][price_for]" type="number" step="0.01" value="{{ $variant->price_for }}" class="form-control" placeholder="Preço Por">
                            </div>
                            <div class="col-md-2">
                                <select name="variants[{{ $i }}][status]" class="form-control">
                                    <option value="1" {{ $variant->status ? 'selected' : '' }}>Ativo</option>
                                    <option value="0" {{ !$variant->status ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-secondary mb-3" onclick="addVariant()">+ Adicionar Variação</button>
            <button type="submit" class="btn btn-primary">Atualizar Produto</button>
        </form>
    </div>

    <script>
        function addVariant() {
            const container = document.getElementById('variants-container');
            const index = container.children.length;
            const html = `
        <div class="card mb-3 p-3">
            <div class="row g-2">
                <div class="col-md-2">
                    <input name="variants[${index}][variant]" class="form-control" placeholder="Tipo">
                </div>
                <div class="col-md-3">
                    <input name="variants[${index}][name_variant]" class="form-control" placeholder="Nome da Variação">
                </div>
                <div class="col-md-2">
                    <input name="variants[${index}][sku]" class="form-control" placeholder="SKU">
                </div>
                <div class="col-md-1">
                    <input name="variants[${index}][stock]" type="number" class="form-control" placeholder="Estoque">
                </div>
                <div class="col-md-2">
                    <input name="variants[${index}][price]" type="number" step="0.01" class="form-control" placeholder="Preço">
                </div>
                <div class="col-md-2">
                    <input name="variants[${index}][price_for]" type="number" step="0.01" class="form-control" placeholder="Preço Por">
                </div>
                <div class="col-md-2">
                    <select name="variants[${index}][status]" class="form-control">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
            </div>
        </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
@endsection

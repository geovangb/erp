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
        <h2 class="mb-4">Detalhes do Pedido #{{ $order->id }}</h2>
        <div class="mb-4">
            <h4>Atualizar Status do Pedido</h4>
            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                @method('PUT')

                <select name="status" class="form-select w-auto">
                    <option value="recebido" {{ $order->status == 'recebido' ? 'selected' : '' }}>Recebido</option>
                    <option value="processando" {{ $order->status == 'processando' ? 'selected' : '' }}>Processando</option>
                    <option value="enviado" {{ $order->status == 'enviado' ? 'selected' : '' }}>Enviado</option>
                    <option value="concluido" {{ $order->status == 'concluido' ? 'selected' : '' }}>Concluído</option>
                </select>

                <button type="submit" class="btn btn-sm btn-primary">
                    Atualizar
                </button>
            </form>
        </div>
        <div class="mb-4">
            <h4>Informações do Cliente</h4>
            <p><strong>Nome:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Telefone:</strong> {{ $order->customer_phone }}</p>
            <p><strong>CEP:</strong> {{ $order->customer_cep }}</p>
            <p><strong>Endereço:</strong> {{ $order->customer_address }}</p>
        </div>

        <div class="mb-4">
            <h4>Informações do Pedido</h4>
            <p><strong>Método de Pagamento:</strong> {{ ucfirst($order->payment_method) }}</p>
            <p><strong>Status:</strong>
                @if ($order->status === 'recebido')
                    <span class="badge bg-success">Recebido</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                @endif
            </p>
            <p><strong>Data do Pedido:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div>
            <h4>Itens do Pedido</h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Produto</th>
                    <th>Variação</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->variant_name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Frete:</strong></td>
                    <td>R$ {{ number_format($order->shipping, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td><strong>R$ {{ number_format($order->total, 2, ',', '.') }}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>

        <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Voltar à Lista
        </a>
    </div>
@endsection


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
        <h2 class="mb-4">Lista de Pedidos</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($orders->count())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>E-mail</th>
                    <th>Subtotal</th>
                    <th>Frete</th>
                    <th>Total</th>
                    <th>Método de Pagamento</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_email }}</td>
                        <td>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($order->shipping, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td>{{ ucfirst($order->payment_method) }}</td>
                        <td>
                            @if ($order->status === 'recebido')
                                <span class="badge bg-success">Recebido</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">Nenhum pedido encontrado.</div>
        @endif
    </div>
@endsection

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
        <h2 class="mb-4">Carrinho de Compras</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- CEP e Frete --}}
        <div class="mb-4">
            <label for="cep" class="form-label">Calcular Frete por CEP:</label>
            <div class="d-flex gap-2">
                <input type="text" id="cep" class="form-control w-auto" maxlength="9" placeholder="00000-000">
                <button onclick="buscarCep()" class="btn btn-primary">Buscar</button>
            </div>
            <div id="cep-info" class="mt-2 text-muted"></div>
        </div>

        {{-- Método de pagamento --}}
        <form action="{{ route('cart.setPaymentMethod') }}" method="POST" class="mb-4">
            @csrf
            <label for="payment_method" class="form-label">Método de Pagamento:</label>
            <select name="payment_method" id="payment_method" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                <option value="">Selecione...</option>
                <option value="pix" {{ session('payment_method') === 'pix' ? 'selected' : '' }}>PIX</option>
                <option value="boleto" {{ session('payment_method') === 'boleto' ? 'selected' : '' }}>Boleto</option>
                <option value="credito" {{ session('payment_method') === 'credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                <option value="debito" {{ session('payment_method') === 'debito' ? 'selected' : '' }}>Cartão de Débito</option>
            </select>
        </form>

        @if (count($cart) > 0)
            <div class="mb-4">
                <form action="{{ route('cart.applyCoupon') }}" method="POST" class="mb-3 d-flex gap-2">
                    @csrf
                    <input type="text" name="coupon_code" class="form-control w-auto" placeholder="Digite o cupom" required>
                    <button type="submit" class="btn btn-outline-success">Aplicar Cupom</button>
                </form>

                @if (session('coupon'))
                    <div class="alert alert-info">
                        Cupom <strong>{{ session('coupon')['code'] }}</strong> aplicado -
                        Desconto de <strong>R$ {{ number_format(session('coupon')['discount'], 2, ',', '.') }}</strong>
                        <form action="{{ route('cart.removeCoupon') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-link text-danger">Remover</button>
                        </form>
                    </div>
                @endif
            </div>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Produto</th>
                    <th>Variação</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @php $total = 0; @endphp
                @foreach ($cart as $key => $item)
                    @php
                        $subtotal = $item['quantity'] * $item['price'];
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['variant_id'] ?? '-' }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $key) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Remover</button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                {{-- Totais --}}
                @php
                    $frete = $frete ?? 0;
                    $subtotal = $total;
                    $total_completo = $subtotal + $frete;
                @endphp

                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td colspan="2"><strong>R$ {{ number_format($subtotal, 2, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Frete:</strong></td>
                    <td colspan="2"><strong>R$ {{ number_format($frete, 2, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong>R$ {{ number_format($total_completo, 2, ',', '.') }}</strong></td>
                </tr>
                @if (session('coupon'))
                    <tr>
                        <td colspan="4" class="text-end">Desconto ({{ session('coupon')['code'] }}):</td>
                        <td colspan="2">- R$ {{ number_format(session('coupon')['discount'], 2, ',', '.') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>

            {{-- Exibir método selecionado --}}
            @if(session('payment_method'))
                <div class="mt-3">
                    <strong>Método de Pagamento Selecionado:</strong>
                    <span class="text-primary text-capitalize">
                    {{ session('payment_method') }}
                </span>
                </div>
            @endif
            <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#finalizarPedidoModal">
                Finalizar Pedido
            </button>

        @else
            <div class="alert alert-info">Seu carrinho está vazio.</div>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal fade" id="finalizarPedidoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dados do Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Telefone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>CEP</label>
                            <input type="text" name="cep" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Endereço</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Confirmar Pedido</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- Script ViaCEP --}}
    <script>
        function buscarCep() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            const cepInfo = document.getElementById('cep-info');

            if (cep.length !== 8) {
                cepInfo.innerHTML = '<span class="text-danger">CEP inválido.</span>';
                return;
            }

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        cepInfo.innerHTML = '<span class="text-danger">CEP não encontrado.</span>';
                    } else {
                        cepInfo.innerHTML = `Endereço: ${data.localidade} - ${data.uf}`;

                        // Chamada ao backend para calcular o frete com base no UF
                        fetch("{{ route('cart.calculateFreight') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ uf: data.uf })
                        }).then(response => response.json()).then(() => {
                            location.reload();
                        });
                    }
                })
                .catch(() => {
                    cepInfo.innerHTML = '<span class="text-danger">Erro ao buscar o CEP.</span>';
                });
        }
    </script>
@endsection

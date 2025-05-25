<h2>Pedido Confirmado</h2>

<p>Olá, {{ $order->name }}!</p>

<p>Obrigado por sua compra.</p>

<p><strong>Resumo do Pedido:</strong></p>
<ul>
    <li><strong>ID do Pedido:</strong> {{ $order->id }}</li>
    <li><strong>Valor Total:</strong> R$ {{ number_format($order->total + $order->shipping, 2, ',', '.') }}</li>
</ul>

<h4>Itens:</h4>
<ul>
    @foreach ($order->items as $item)
        <li>{{ $item->product_name }} ({{ $item->variant_name ?? 'Sem variação' }}) - Quantidade: {{ $item->quantity }}</li>
    @endforeach
</ul>

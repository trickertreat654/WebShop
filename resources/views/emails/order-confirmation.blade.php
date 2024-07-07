<!-- <!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body> -->

@component('mail::message')

# Thank you for your order, {{ $order->user->name }}!

    We are pleased to confirm your order. Below are the details:

## Order Summary
    Order ID: {{ $order->id }}
    Order Date: {{ $order->created_at->format('F j, Y, g:i a') }}

### Shipping Address
    {{ json_decode($order->shipping_address)->line1 }}
    {{ json_decode($order->shipping_address)->city }}, {{ json_decode($order->shipping_address)->state }} {{ json_decode($order->shipping_address)->postal_code }}

### Billing Address
    {{ json_decode($order->billing_address)->line1 }}
    {{ json_decode($order->billing_address)->city }}, {{ json_decode($order->billing_address)->state }} {{ json_decode($order->billing_address)->postal_code }}

### Order Items
<table style="width: 100%;">
    <thead>
        <tr>
            <th style="text-align: start;">Product</th>
            <th style="text-align: start;">Description</th>
            <th style="text-align: start;">Quantity</th>
            <th style="text-align: start;">Price</th>
            <th style="text-align: start;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price / 100, 2) }}</td>
                <td>${{ number_format($item->amount_total / 100, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

### Order Total
    Subtotal: ${{ number_format($order->amount_subtotal / 100, 2) }}
    Tax: ${{ number_format($order->amount_tax / 100, 2) }}
    Shipping: ${{ number_format($order->amount_shipping / 100, 2) }}
    Total: ${{ number_format($order->amount_total / 100, 2) }}

@component('mail::button', ['url' => route('order.show', $order->id), 'color' => 'primary'])
        View Order
@endcomponent

    If you have any questions about your order, please contact us at support@example.com.

    Thank you for shopping with us!




@endcomponent
<!-- </body>
</html> -->

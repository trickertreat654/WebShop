
@component('mail::message')

# We Miss You!
It looks like you left some items in your cart. Complete your purchase now before they're gone!

## Your Cart
    
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach ($cart->items as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->description }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($item->product->price / 100, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">${{ number_format(($item->product->price * $item->quantity) / 100, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
 


@component('mail::button', ['url' => route('login'), 'color' => 'primary'])
        Complete Purchase
@endcomponent
If you have any questions, please contact us at support@example.com.
    
Thank you for shopping with us!

@endcomponent
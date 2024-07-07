<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;


Route::get('/', function () {


   
        $products = Product::all();
        return Inertia::render('Dashboard', [
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'image' => $product->image->path,
                ];
            }),
            'canLogin' => auth()->guest(),
        ]);
})->name('home');

Route::get('/dashboard', function () {
    return to_route('home');
})->name('dashboard');

Route::get('/checkout-success', function (Request $request) {
    $request->validate([
        'session_id' => ['required'],
    ]);
    return Inertia::render('CheckoutSuccess', [
        'sessionId' => $request->session_id,
    ]);
})->middleware(['auth'])->name('checkout.success');

Route::get('orders', function () {
    $orders = auth()->user()->orders;
    return Inertia::render('Orders', [
        'orders' => $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'amount_shipping' => $order->amount_shipping,
                'amount_discount' => $order->amount_discount,
                'amount_tax' => $order->amount_tax,
                'amount_total' => $order->amount_total,
                'billing_address' => $order->billing_address,
                'shipping_address' => $order->shipping_address,
                'created_at' => $order->created_at,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'amount_total' => $item->amount_total,
                    ];
                }),
            ];
        }),
    ]);
})->middleware('auth')->name('orders');

Route::get('/order/{orderId}', function ($orderId) {
    $order = App\Models\Order::find($orderId);
    $user = auth()->user();
    if(!$order || ($user && $order->user_id !== $user->id)) {
        return redirect()->route('home');
    }
    return Inertia::render('Order', [
        'order' => [
            'id' => $order->id,
            'amount_shipping' => $order->amount_shipping,
            'amount_discount' => $order->amount_discount,
            'amount_tax' => $order->amount_tax,
            'amount_total' => $order->amount_total,
            'billing_address' => $order->billing_address,
            'shipping_address' => $order->shipping_address,
            'created_at' => $order->created_at,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'amount_total' => $item->amount_total,
                ];
            }),
        ],
    ]);
})->middleware('auth')->name('order.show');


Route::get('product/{product}', function (Product $product) {

    

    return Inertia::render('Product', [
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => $product->image->path,
            'images' => $product->images->map(function ($image) {
                return $image->path;
            }),
            'variants' => $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'color' => $variant->color,
                    'size' => $variant->size,
                ];
            }),
        ],
    
    ]);

})->name('product.show');

Route::delete('cart/{cartItem}', function (CartItem $cartItem) {
    $cartItem->delete();
    return redirect()->route('cart');
})->name('cart.remove');

Route::patch('cart/{cartItem}/decrement', function (CartItem $cartItem) {
    if($cartItem->quantity === 1) {
        $cartItem->delete();
        return redirect()->route('cart');
    }
    $cartItem->decrement('quantity');
    return redirect()->route('cart');
})->name('cart.decrement');

Route::patch('cart/{cartItem}/increment', function (CartItem $cartItem) {
    $cartItem->increment('quantity');
    return redirect()->route('cart');
})->name('cart.increment');


Route::get('/preview-email', function () {

    // $order = App\Models\Order::find(4)->get();
    $cart = App\Models\User::find(2)->cart;
   
    return new App\Mail\AbandondedCartReminder($cart);
});



Route::post('product/add-to-cart', function (Request $request) {
    $request->validate([
        'variant' => ['required', 'exists:product_variants,id'],
    ]);


    $cart = match(auth()->guest()){
        true => Cart::firstOrCreate([
            'session_id' => session()->getId(),
        ]),
        false => auth()->user()->cart ?: auth()->user()->cart()->create(),
    };
    
    $cart->items()->firstOrCreate([
        'product_variant_id' => $request->variant
    ], [
        'quantity' => 0,
    ]
)->increment('quantity');
   
    return redirect()->route('home');
})->name('product.add-to-cart');

Route::get('/cart', function() {


   $cart = auth()->user() ? auth()->user()->cart : Cart::where('session_id', session()->getId())->first();
   if(!$cart) {
       return Inertia::render('Cart', [
            'cart' => null,
            'items' => [],
       ]);
    }
    $items = $cart->items;

    return Inertia::render('Cart', [
        'cart' => $cart,
        'items' => $items->map(function($item) {
            return [
                'id' => $item->id,
                'image' => $item->product->image->path,
                'product' => $item->product->name,
                'variant' => $item->variant->color . ' ' . $item->variant->size,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ];
        }),
    ]);
})->name('cart');

Route::get('/checkout', function() {

    $cart = auth()->user()->cart;
    if(!$cart) {
        return redirect()->route('cart');
    }

    $items = $cart->items;
    return auth()->user()->allowPromotionCodes()->checkout($items->loadMissing('product','variant')->map(function(CartItem $item)
     {
        return 
        [
            
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $item->product->price * 1,
                'product_data' => [
                    'name' => $item->product->name,
                    'description' => 'Size: ' . $item->variant->size . ', Color: ' . $item->variant->color,
                    
                    'metadata' => [
                        'product_id' => $item->product->id,
                        'product_variant_id' => $item->product_variant_id,
                        ]
                    ],
                ],
            'quantity' => $item->quantity,
        ];
    })->toArray()
    ,['customer_update' => [
                'shipping' => 'auto',
            ],
            'shipping_address_collection' => [
                'allowed_countries' => ['US', 'CA'],
            ],
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart'),
            'metadata' => [
                'user_id' => auth()->id(),
                'cart_id' => $cart->id,
            ],
    ]
);
})->middleware(['auth'])->name('checkout');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

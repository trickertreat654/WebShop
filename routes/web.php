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
        ]);
})->name('home');

Route::get('/dashboard', function () {
    return to_route('home');
})->name('dashboard');


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


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Cart;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        if ($request->user()) {
            $cart = $request->user()->cart;
        } else {
            // the cart has a seesion_id column
            $cart = Cart::where('session_id', session()->getId())->first();

        }
        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            // sum of the quantity of all items in the cart
            'cartCount' => $cart ? $cart->items->sum('quantity') : 0,

            // 'cartCount' => $cart ? $cart->items->count() : 0,
        ];
    }
}

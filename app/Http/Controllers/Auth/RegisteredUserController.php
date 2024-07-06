<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;



class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $session = session()->getId();
        Log::info($session);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        event(new Registered($user));
        
        
        
        Auth::login($user);
        
        $sessionCart = Cart::where('session_id', $session)->first();
        //create cart for user
        $userCart = $user->cart()->create();
        if (! $sessionCart) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        $sessionCart->items->each(fn($item) => $userCart->items()->updateOrCreate([
            'product_variant_id' => $item->product_variant_id,            
        ], [
            'quantity' => $item->quantity,
            
        ]));
        
        
     
        
        $sessionCart->items->each->delete();
        $sessionCart->delete();
        
        $request->session()->regenerate();







        return redirect(route('dashboard', absolute: false));
    }
}

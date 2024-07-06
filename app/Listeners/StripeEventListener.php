<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Cashier;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;




class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {

        DB::transaction(function() use ($event) {

            if ($event->payload['type'] === 'checkout.session.completed') {
                Log::info('Checkout session completed24152454');
                // dd($event->payload['data']['object']['id']);
                // Handle the incoming event...
                $session = Cashier::stripe()->checkout->sessions->retrieve(
                    $event->payload['data']['object']['id'],
                    []
                );
                $user = User::find($session->metadata->user_id);
                $cart = Cart::find($session->metadata->cart_id);

                Log::info($session);
                $order = $user->orders()->create([
                        'stripe_checkout_session_id' => $session->id,
                        'amount_shipping' => $session->total_details->amount_shipping,
                        'amount_discount' => $session->total_details->amount_discount,
                        'amount_tax' => $session->total_details->amount_tax,
                        'amount_subtotal' => $session->amount_subtotal,
                        'amount_total' => $session->amount_total,
                        'billing_address' => [
                            'name' => $session->customer_details->name,
                            'line1' => $session->customer_details->address->line1,
                            'line2' => $session->customer_details->address->line2,
                            'city' => $session->customer_details->address->city,
                            'postal_code' => $session->customer_details->address->postal_code,
                            'state' => $session->customer_details->address->state,
                            'country' => $session->customer_details->address->country,
                        
                        ],
                        'shipping_address' => [
                            'name' => $session->shipping_details->name,
                            'line1' => $session->shipping_details->address->line1,
                            'line2' => $session->shipping_details->address->line2,
                            'city' => $session->shipping_details->address->city,
                            'postal_code' => $session->shipping_details->address->postal_code,
                            'state' => $session->shipping_details->address->state,
                            'country' => $session->shipping_details->address->country,
                        ],
                    ]);
                    $lineItems = Cashier::stripe()->checkout->sessions->allLineItems(
                        $session->id);
    
                    $orderItems = collect($lineItems->all())->map(function ($lineItem) {
                        $product = Cashier::stripe()->products->retrieve(
                            $lineItem->price->product,
                            []
                        );
                        return new OrderItem([
                            'product_id' => $lineItem->price->product,
                            'name' => $lineItem->description,
                            'description' => $lineItem->description,
                            'price' => $lineItem->amount_total,
                            'quantity' => $lineItem->quantity,
                            'amount_discount' => $lineItem->amount_discount,
                            'amount_tax' => $lineItem->amount_tax,
                            'amount_subtotal' => $lineItem->amount_subtotal,
                            'amount_total' => $lineItem->amount_total,
                        ]);
                    });
    
                    $order->items()->saveMany($orderItems);
                    $cart->items()->delete();
                    $cart->delete();
                
            }

        });
       
    }
}

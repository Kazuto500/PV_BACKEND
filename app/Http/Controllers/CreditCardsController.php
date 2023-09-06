<?php

namespace App\Http\Controllers;

use App\Models\CreditCards;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Exception\CardException;
use Stripe\Stripe;

class CreditCardsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = $request->user();

        try {
            // Agrega la tarjeta de crédito al cliente en Stripe
            $stripePaymentMethod = \Stripe\PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $request->token, // Token de la tarjeta de crédito
                ],
            ]);

            // Adjunta la tarjeta al cliente
            \Stripe\Customer::update($user->stripe_id, [
                'invoice_settings' => [
                    'default_payment_method' => $stripePaymentMethod->id,
                ],
            ]);

            // Guarda el ID del método de pago en tu base de datos
            $user->creditCards()->create([
                'stripe_payment_method_id' => $stripePaymentMethod->id,
                'last_four' => $stripePaymentMethod->card->last4,
                'brand' => $stripePaymentMethod->card->brand,
            ]);

            return response()->json(['data' => $user]);
        } catch (CardException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al agregar la tarjeta de crédito'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CreditCards $creditCards)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreditCards $creditCards)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditCards $creditCards)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCards $creditCards)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index()
    {
        // Configura la clave secreta de Stripe desde tu archivo .env
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Recupera el usuario autenticado
        $user = Auth::user();

        // Verifica si el usuario tiene un stripe_id en tu base de datos local
        if ($user->stripe_id) {
            try {
                // Utiliza el stripe_id para obtener el cliente de Stripe
                $stripeCustomer = Customer::retrieve($user->stripe_id);

                // Obtiene los métodos de pago del cliente de Stripe
                $paymentMethods = PaymentMethod::all([
                    'customer' => $stripeCustomer->id,
                    'type' => 'card',
                ]);

                // Retorna los métodos de pago como JSON
                return response()->json(['paymentMethods' => $paymentMethods], 200);
            } catch (\Exception $ex) {
                return response()->json(['message' => 'Error al obtener los métodos de pago'], 500);
            }
        } else {
            return response()->json(['message' => 'El usuario no tiene un cliente de Stripe'], 404);
        }
    }

    public function create(Request $request)
    {
        // Configura la clave secreta de Stripe desde tu archivo .env
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Recupera el usuario autenticado
        $user = Auth::user();

        // Verifica si el usuario tiene un stripe_id en tu base de datos local
        if ($user->stripe_id) {
            try {
                // Crea el método de pago utilizando los detalles proporcionados en la solicitud
                $paymentMethod = PaymentMethod::create([
                    'type' => 'card',
                    'card' => [
                        'number' => $request->input('card_number'),
                        'exp_month' => $request->input('exp_month'),
                        'exp_year' => $request->input('exp_year'),
                        'cvc' => $request->input('cvc'),
                    ],
                    'billing_details' => [
                        'name' => $request->input('cardholder_name'),
                    ],
                ]);

                // Adjunta el método de pago al cliente de Stripe
                $stripeCustomer = Customer::retrieve($user->stripe_id);
                $paymentMethod->attach(['customer' => $stripeCustomer->id]);

                // Establece el método de pago como predeterminado (opcional)
                $stripeCustomer->update(['invoice_settings' => ['default_payment_method' => $paymentMethod->id]]);

                return response()->json(['message' => 'Método de pago creado con éxito'], 201);
            } catch (\Exception $ex) {
                return response()->json(['message' => 'Error al crear el método de pago'], 500);
            }
        } else {
            return response()->json(['message' => 'El usuario no tiene un cliente de Stripe'], 404);
        }
    }
}

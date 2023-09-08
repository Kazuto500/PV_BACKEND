<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Customer;

class SubscriptionController extends Controller
{
    public function show()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Consultar la suscripción actual del usuario desde Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = Customer::retrieve($user->stripe_id);

        if ($customer->subscriptions->data) {
            // Devolver la información de la suscripción actual
            $subscription = $customer->subscriptions->data[0];
            return response()->json(['subscription' => $subscription], 200);
        } else {
            return response()->json(['message' => 'El usuario no está suscrito a ningún plan'], 404);
        }
    }

    public function subscribe(Request $request)
    {
        // Validar la solicitud y el plan seleccionado
        $request->validate([
            'plan_id' => 'required', // Asegúrate de que el campo 'plan_id' esté presente en la solicitud
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Crear una suscripción en Stripe y asignar el plan al cliente
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = Customer::retrieve($user->stripe_id);
        $planId = $request->input('plan_id'); // Obtener el identificador del plan desde la solicitud

        $subscription = $customer->subscriptions->create([
            'items' => [
                [
                    'price' => $planId, // Usar el identificador del plan en Stripe
                ],
            ],
        ]);

        // Devolver la información de la nueva suscripción
        return response()->json(['subscription' => $subscription], 201);
    }

    public function changePlan(Request $request)
    {
        // Validar la solicitud y el nuevo plan seleccionado
        $request->validate([
            'plan_id' => 'required', // Asegúrate de que el campo 'plan_id' esté presente en la solicitud
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario tiene una suscripción en Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = Customer::retrieve($user->stripe_id);

        if ($customer->subscriptions->total_count === 0) {
            return response()->json(['message' => 'El usuario no tiene una suscripción activa'], 404);
        }

        $planId = $request->input('plan_id'); // Obtener el identificador del nuevo plan desde la solicitud

        // Obtener la suscripción actual del usuario
        $subscription = $customer->subscriptions->data[0];

        // Actualizar el precio del plan en la suscripción
        $subscription->items->retrieve($subscription->items->data[0]->id)->update([
            'price' => $planId, // Usar el identificador del nuevo plan en Stripe
        ]);

        // Devolver la información de la suscripción actualizada
        return response()->json(['subscription' => $subscription], 200);
    }

    public function unsubscribe()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Establecer la clave de API de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Obtener el cliente de Stripe
            $customer = Customer::retrieve($user->stripe_id);

            if ($customer->subscriptions->data) {
                // Obtener la suscripción actual (si existe)
                $subscription = $customer->subscriptions->data[0];

                // Verificar si la suscripción no está cancelada
                if (!$subscription->cancel_at_period_end) {
                    // Cancelar la suscripción
                    $subscription->cancel();

                    // Devolver un mensaje de confirmación
                    return response()->json(['message' => 'Suscripción cancelada con éxito'], 200);
                } else {
                    return response()->json(['message' => 'La suscripción ya está cancelada'], 400);
                }
            } else {
                return response()->json(['message' => 'El usuario no tiene una suscripción activa'], 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Error al cancelar la suscripción'], 500);
        }
    }
}

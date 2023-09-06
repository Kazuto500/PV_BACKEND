<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription('default');

        return response()->json(['subscription' => $subscription]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            // Crea la suscripción
            $subscription = $user->newSubscription('default', 'plan_id')->create();

            DB::commit();

            return response()->json(['message' => 'Suscripción exitosa', 'subscription' => $subscription]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error al crear la suscripción', 'error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request)
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            // Cancela la suscripción
            $user->subscription('default')->cancel();

            DB::commit();

            return response()->json(['message' => 'Suscripción cancelada']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error al cancelar la suscripción', 'error' => $e->getMessage()], 500);
        }
    }
}

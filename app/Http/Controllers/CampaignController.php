<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function toggleStateCampaign()
    {
        $user = Auth::user();

        $campaign = Campaign::where('userId', $user->id)->first();

        if (!$campaign) {
            return response()->json(['message' => 'Campaña no encontrada para este usuario'], 404);
        }

        $campaign->update(['state' => !$campaign->state]);

        return response()->json(['message' => 'Estado de campaña cambiado exitosamente', 'new_state' => $campaign->state]);
    }
}

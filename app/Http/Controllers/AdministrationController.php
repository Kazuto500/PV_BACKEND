<?php

namespace App\Http\Controllers;

use App\Models\Administration;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdministrationController extends Controller
{
    public function agentInfo()
    {
        $users = User::where('role', 'manager')->get();
        return response()->json($users);
    }

    public function createCampaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaignName' => 'required',
            'plan' => 'required',
            'preview' => 'required',
            'download' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $campaign = Campaign::create([
            'campaignName' => $request->campaign,
            'plan' => $request->plan,
            'preview' => $request->preview,
            'download' => $request->download
        ]);

        $campaign = Campaign::find($campaign->id);

        return response()
            ->json(['data' => $campaign]);
    }

    public function updateCampaign(Request $request, $id)
    {
        $campaign = Auth::user();

        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['message' => 'Campaña no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'campaignName' => 'required',
            'plan' => 'required',
            'preview' => 'required',
            'download' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $campaign->update([
            'campaignName' => $request->campaignName,
            'plan' => $request->plan,
            'preview' => $request->preview,
            'download' => $request->download
        ]);

        return response()->json(['data' => $campaign, 'message' => 'Campaña actualizada con éxito']);
    }

    public function deleteCampaign($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['message' => 'Campaña no encontrada'], 404);
        }

        $campaign->delete();

        return response()->json(['message' => 'Campaña eliminada con éxito']);
    }
}

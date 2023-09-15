<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Termwind\Components\Raw;

class AdminController extends Controller
{
    public function registerAgent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyName' => 'required',
            'firstName' => 'required',
            'lastName' => 'required',
            'countryCode' => 'required',
            'dialCode' => 'required',
            'telephone' => 'required',
            'email' => 'required',
            'password' => 'required',
            "role" => "required",
            "campaign" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'companyName' => $request->companyName,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'countryCode' => $request->countryCode,
            'dialCode' => $request->dialCode,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'campaign' => $request->campaign
        ]);

        $user = User::find($user->id);

        return response()
            ->json(['data' => $user]);
    }

    public function agentInfo()
    {
        $users = User::where('role', 'assistant')->get();
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

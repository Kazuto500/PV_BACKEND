<?php

namespace App\Http\Controllers;

use App\Models\CallRecord;
use Illuminate\Http\Request;

class CallRecordController extends Controller
{
    public function callRecordInfo($user_id, $campaign_id)
    {
        $calls = CallRecord::where('user_id', $user_id)
            ->where('campaign_id', $campaign_id)
            ->get();

        return response()->json($calls);
    }
}

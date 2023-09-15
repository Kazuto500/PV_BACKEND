<?php

namespace App\Http\Controllers;

use App\Models\WeeklyRecord;
use Illuminate\Http\Request;

class WeeklyRecordController extends Controller
{
    public function callRecordInfo($user_id, $campaign_id)
    {
        $calls = WeeklyRecord::where('user_id', $user_id)
            ->where('campaign_id', $campaign_id)
            ->get();

        return response()->json($calls);
    }
}

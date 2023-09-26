<?php

namespace App\Http\Controllers;

use App\Models\WeeklyRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyRecordController extends Controller
{
    public function callRecordInfo($user)
    {
        $user = Auth::user();

        $calls = WeeklyRecord::where('userId', $user->id)->get();

        return response()->json($calls);
    }
}

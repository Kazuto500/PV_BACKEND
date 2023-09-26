<?php

namespace App\Http\Controllers;

use App\Models\CallRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallRecordController extends Controller
{
    public function callRecordInfo($user)
    {
        $user = Auth::user();

        $calls = CallRecord::where('userId', $user->id)->get();

        return response()->json($calls);
    }
}

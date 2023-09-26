<?php

namespace App\Http\Controllers;

use App\Models\Management;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function opsInfo()
    {
        $manager = Auth::user();

        $management = Management::find($manager->id);

        return response()->json($management);
    }
}

<?php

namespace App\Http\Controllers\driver;
use App\Http\Controllers\Controller;
namespace App\Http\Controllers\Restaurant;
use App\Http\Controllers\Controller;
use App\Models\DriverRequest;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{

    public function index()
    {
        return \App\Models\User::where('role','driver')
            ->where('status','active')
            ->get();
    }

    public function apply()
    {
        DriverRequest::create([
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Request sent'
        ]);
    }
}

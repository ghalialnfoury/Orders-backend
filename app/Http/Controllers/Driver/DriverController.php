<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DriverController extends Controller
{
    public function index()
    {
        return User::where('role','driver')
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

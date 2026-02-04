<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index()
    {
        return response()->json(
            Restaurant::where('is_open', true)
                ->orderBy('name')
                ->get()
        );
    }
}

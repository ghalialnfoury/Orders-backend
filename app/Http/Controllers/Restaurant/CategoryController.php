<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $restaurant = Restaurant::where('user_id', auth('api')->id())->first();

        return response()->json(
            $restaurant->categories
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $restaurant = Restaurant::where('user_id', auth('api')->id())->first();

        $category = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
        ]);

        return response()->json($category, 201);
    }
}

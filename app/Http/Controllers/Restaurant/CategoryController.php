<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * أقسام المطعم مع المنتجات (لصاحب المطعم)
     */
    public function index()
    {
        $restaurant = auth('api')->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant profile not found'
            ], 404);
        }

        return response()->json(
            $restaurant->categories()
                ->with([
                    'products:id,category_id,name,price,is_available'
                ])
                ->orderBy('created_at')
                ->get()
        );
    }

    /**
     * إضافة قسم جديد (Restaurant)
     */
    public function store(Request $request)
    {
        $restaurant = auth('api')->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant profile not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message'  => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    /**
     * منيو المطعم (Public / Customer)
     */
    public function publicMenu($restaurantId)
    {
        return response()->json(
            Category::where('restaurant_id', $restaurantId)
                ->with([
                    'products:id,category_id,name,price,is_available'
                ])
                ->orderBy('created_at')
                ->get()
        );
    }
}

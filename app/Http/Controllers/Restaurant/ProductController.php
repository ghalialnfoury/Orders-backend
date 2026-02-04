<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $restaurant = auth('api')->user()->restaurant;

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant profile not found'], 404);
        }

        $products = Product::where('restaurant_id', $restaurant->id)
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->latest()
            ->get();

        return response()->json($products);
    }

  public function store(Request $request)
{
    $restaurant = auth('api')->user()->restaurant;

    if (!$restaurant) {
        return response()->json([
            'message' => 'Restaurant profile not found'
        ], 404);
    }

    $request->validate([
        'name'          => 'required|string|max:255',
        'price'         => 'required|numeric|min:0',
        'category_id'   => 'nullable|exists:categories,id',
        'category_name' => 'nullable|string|max:255',
        'description'   => 'nullable|string|max:1000',
    ]);

    // تحديد القسم
    if ($request->category_id) {

        $category = Category::where('id', $request->category_id)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

    } elseif ($request->category_name) {

        $category = Category::firstOrCreate([
            'restaurant_id' => $restaurant->id,
            'name' => strtolower(trim($request->category_name)),
        ]);

    } else {
        return response()->json([
            'message' => 'Category is required'
        ], 422);
    }

    // إنشاء المنتج
    $product = Product::create([
        'name' => $request->name,
        'price' => floatval($request->price),
        'description' => $request->description,
        'category_id' => $category->id,
        'restaurant_id' => $restaurant->id,
    ]);

    return response()->json($product, 201);
}


    public function update(Request $request, $id)
    {
        $restaurant = auth('api')->user()->restaurant;

        $product = Product::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',

            'category_id' => [
                'sometimes',
                Rule::exists('categories', 'id')
                    ->where('restaurant_id', $restaurant->id)
            ],

            'description' => 'nullable|string|max:1000',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $restaurant = auth('api')->user()->restaurant;

        $product = Product::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        $product->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}

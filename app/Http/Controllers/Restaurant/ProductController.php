<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display all products for the authenticated restaurant.
     * Optionally filter products by category.
     */
    public function index(Request $request)
    {
        $restaurant = auth('api')->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant profile not found'
            ], 404);
        }

        $products = Product::where('restaurant_id', $restaurant->id)
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->latest()
            ->get()
            ->map(function ($product) {
                return $product->append('image_url');
            });

        return response()->json($products);
    }

    /**
     * Store a newly created product.
     * Supports creating a new category or using an existing one.
     * Handles optional image upload.
     */
    public function store(Request $request)
    {
        $restaurant = auth('api')->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant profile not found'
            ], 404);
        }

        // Validate incoming request
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'category_id'   => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Determine category
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

        // Upload image if provided
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Create product
        $product = Product::create([
            'name' => $request->name,
            'price' => floatval($request->price),
            'description' => $request->description,
            'category_id' => $category->id,
            'restaurant_id' => $restaurant->id,
            'image' => $imagePath,
        ]);

        $product->append('image_url');

        return response()->json($product, 201);
    }

    /**
     * Update an existing product.
     * Allows updating product data and replacing the image.
     */
    public function update(Request $request, $id)
    {
        $restaurant = auth('api')->user()->restaurant;

        $product = Product::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        // Validate update data
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'category_id' => [
                'sometimes',
                Rule::exists('categories', 'id')
                    ->where('restaurant_id', $restaurant->id)
            ],
        ]);

        // Replace image if a new one is uploaded
        if ($request->hasFile('image')) {

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] =
                $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        $product->append('image_url');

        return response()->json($product);
    }

    /**
     * Delete a product and remove its image from storage.
     */
    public function destroy($id)
    {
        $restaurant = auth('api')->user()->restaurant;

        $product = Product::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        // Remove stored image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}

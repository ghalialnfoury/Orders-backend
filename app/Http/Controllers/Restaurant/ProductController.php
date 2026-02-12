<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;

/**
 * ProductController
 *
 * Handles restaurant product management including:
 * - Listing products
 * - Creating new products with optional image upload
 * - Updating product details and images
 * - Deleting products and their associated images
 *
 * All operations are restricted to the authenticated restaurant owner.
 */
class ProductController extends Controller
{

    /**
     * Display a list of products belonging to the authenticated restaurant.
     * Allows optional filtering by category.
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
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->latest()
            ->get();

        return response()->json($products);
    }

    /**
     * Store a newly created product.
     * Supports creating a new category if category_name is provided.
     * Handles optional image upload and storage.
     */
    public function store(Request $request)
    {
        $restaurant = auth('api')->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant profile not found'
            ], 404);
        }

        // Validate request data
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'category_id'   => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Determine product category
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

        // Store uploaded image if exists
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Create product record
        $product = Product::create([
            'name' => $request->name,
            'price' => floatval($request->price),
            'description' => $request->description,
            'category_id' => $category->id,
            'restaurant_id' => $restaurant->id,
            'image' => $imagePath,
        ]);

        return response()->json($product, 201);
    }

    /**
     * Update an existing product.
     * Allows updating product details and replacing product image.
     */
    public function update(Request $request, $id)
    {
        $restaurant = auth('api')->user()->restaurant;

        $product = Product::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        // Validate incoming update data
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

        // Replace product image if a new image is uploaded
        if ($request->hasFile('image')) {

            // Delete old image from storage
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] =
                $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json($product);
    }

    /**
     * Delete a product and its associated image from storage.
     */
    public function destroy($id)
    {
        $restaurant = auth('api')->user()->restaurant;

        $product = Product::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        // Delete product image from storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}

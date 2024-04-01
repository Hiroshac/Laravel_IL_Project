<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category');
    
        $query = Product::query();
    
        if ($categoryId) {
            // Filter products by category
            $query->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId); // Specify the table name for the id column
            });
        }
    
        $products = $query->get();
        $categories = Category::all(); // Fetch all categories for dropdown
    
        return view('products.index', compact('products', 'categories', 'categoryId'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $imagePath = $image->storeAs('public/images', $imageName);
        } else {
            $imagePath = null;
        }

        $product = new Product([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        $product->save();

        if ($request->has('categories')) {
            $productCategories = [];
            foreach ($request->categories as $categoryId) {
                $productCategories[] = [
                    'product_id' => $product->id,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('category_product')->insert($productCategories);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
    public function edit(Product $product)
    {
        // Fetch all categories
        $allCategories = DB::table('categories')->get();
        
        // Fetch IDs of categories associated with the product
        $productCategories = DB::table('category_product')
                                ->where('product_id', $product->id)
                                ->pluck('category_id')
                                ->toArray();
    
        return view('products.edit', compact('product', 'allCategories', 'productCategories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->getClientOriginalName();
            $imagePath = $image->storeAs('public/images', $imageName);
            $product->image = $imagePath;
        }

        $product->name = $request->name;
        $product->description = $request->description;

        $product->save();

        // Update categories
        if ($request->has('categories')) {
            $newCategories = $request->categories;
            
            // delete old categories
            DB::table('category_product')->where('product_id', $product->id)->delete();

            // add new categories
            foreach ($newCategories as $categoryId) {
                DB::table('category_product')->insert([
                    'product_id' => $product->id,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            // If no new categories are selected, detach all existing categories
            DB::table('category_product')->where('product_id', $product->id)->delete();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);
    
        try {
            Category::create($request->all());
            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create category. Please try again.');
        }
    }

    // public function show(Category $category)
    // {
    //     return view('categories.show', compact('category'));
    // }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully');
    }

    // public function addChild(Request $request, Category $parentCategory)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'nullable',
    //     ]);
    
    //     try {
    //         $childCategory = new Category([
    //             'name' => $request->name,
    //             'description' => $request->description,
    //         ]);
    
    //         $parentCategory->descendants()->save($childCategory);
    
    //         return redirect()->route('categories.show', $parentCategory->id)->with('success', 'Child category added successfully');
    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //         return redirect()->back()->withInput()->with('error', 'Failed to add child category. Please try again.');
    //     }
    // }

    public function addChild(Request $request, Category $parentCategory)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $childCategory = new Category([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $childCategory->save();

            // Add the child category as a descendant of the parent category with depth 1
            DB::table('category_closure')->insert([
                'ancestor' => $parentCategory->id,
                'descendant' => $childCategory->id,
                'depth' => 1,
            ]);

            DB::commit();

            return redirect()->route('categories.showChildren', $parentCategory->id)->with('success', 'Child category added successfully');
            
        } catch (\Exception $e) {
            // dd($e->getMessage());
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Failed to add child category. Please try again.');
        }
    }

    public function showChildren(Category $category)
    {
        $children = $category->descendants;

        return view('categories.children', compact('category', 'children'));
    }

    public function addChildPage(Category $parentCategory)
    {
        return view('categories.add_child', compact('parentCategory'));
    }
}

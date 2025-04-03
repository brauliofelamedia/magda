<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->user_id = auth()->user()->id;
        $category->save();

        return redirect()->back()->with('success', 'Se ha agregado el filtro');
    }

    public function assignCategory(Request $request)
    {
        $user = User::find($request->user_id);
        $user->category_id = $request->category_id;
        $user->save();
    }

    public function updateCategory(Request $request)
    {   
        $category = Category::find($request->category_id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
    }

    public function getCategory(Request $request)
    {
        $category = Category::find($request->category_id);
        return response()->json($category);
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::find($request->category_id);
        
        // Set category_id to null for all associated users
        User::where('category_id', $request->category_id)
            ->update(['category_id' => null]);

        $category->delete();
        return response()->json(['success' => 'Se ha eliminado el filtro']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        return view('panel.pages.categories');
    }

    public function create()
    {
        return view('panel.pages.category-create-page');
    }

    public function store(Request $request)
    {
        $request->validate(['categoryName' => 'required|max:50|unique:categories,name']);

        Category::create([
            'name' => $request->categoryName
        ]);
        return redirect()->route('panel.category.index');
    }

    public function edit(Category $category)
    {
        return view('panel.pages.category-edit-page', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['categoryName' => 'required|max:50|unique:categories,name,' . $category->id]);

        $category->update([
            'name' => $request->categoryName
        ]);

        return redirect()->route('panel.category.index');
    }
}

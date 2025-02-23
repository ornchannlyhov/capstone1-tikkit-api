<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('dashboard.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('dashboard.categories.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            Category::create($validated);

            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create category.')->withInput();
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return view('dashboard.categories.show', compact('category'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('categories.index')->with('error', 'Category not found.');
        }
    }

    public function edit($id)
    {
        try {
            $category = Category::findOrFail($id);
            return view('dashboard.categories.edit', compact('category'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('categories.index')->with('error', 'Category not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = Category::findOrFail($id);
            $category->update($validated);

            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('categories.index')->with('error', 'Category not found.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update category.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('categories.index')->with('error', 'Category not found.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Failed to delete category.');
        }
    }
}

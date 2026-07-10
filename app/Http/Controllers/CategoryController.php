<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::withCount('products')
            ->when($request->filled('search'), fn ($query) => $query
                ->where('name', 'like', '%'.trim($request->input('search')).'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $category = Category::create($validated);
        AuditLog::record('category.created', "Added category {$category->name}");

        return back()->with('success', 'Category added.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        AuditLog::record('category.deleted', "Deleted category {$category->name} and its products");

        return back()->with('success', 'Category deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                // Every word must match somewhere: name, SKU, category, or remarks.
                foreach (preg_split('/\s+/', trim($request->input('search'))) as $term) {
                    $query->where(function ($query) use ($term) {
                        $query->where('name', 'like', "%{$term}%")
                            ->orWhere('sku', 'like', "%{$term}%")
                            ->orWhere('remarks', 'like', "%{$term}%")
                            ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$term}%"));
                    });
                }
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $product = Product::create($validated);
        AuditLog::record('product.created', "Added product {$product->name} ({$product->sku})");

        return redirect()->route('products.index')->with('success', 'Product added.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validated($request, $product);

        $product->update($validated);
        AuditLog::record('product.updated', "Updated product {$product->name} ({$product->sku})");

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        AuditLog::record('product.deleted', "Deleted product {$product->name} ({$product->sku})");

        return back()->with('success', 'Product deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'.($product ? ",{$product->id}" : '')],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}

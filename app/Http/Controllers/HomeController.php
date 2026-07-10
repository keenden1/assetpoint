<?php

namespace App\Http\Controllers;

use App\Models\Bo;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        if (! Auth::check()) {
            return view('home');
        }

        return view('home', [
            'productCount' => Product::where('status', 'active')->count(),
            'storeCount' => Store::count(),
            'categoryCount' => Category::count(),
            'boCount' => Bo::whereNull('archived_at')->count(),
            'recentProducts' => Product::with('category')->latest('updated_at')->take(5)->get(),
            'recentBos' => Bo::with('user')
                ->withCount('items')
                ->withSum('items as total', 'total')
                ->whereNull('archived_at')
                ->latest('date')
                ->latest('id')
                ->take(5)
                ->get(),
        ]);
    }
}

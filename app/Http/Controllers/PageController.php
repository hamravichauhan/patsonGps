<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        // Fetch featured flagship items for the home page
        $featuredProducts = ProductCatalog::available()
            ->where('is_featured', 1)
            ->get()
            ->groupBy('slug');

        $categories = \Illuminate\Support\Facades\Cache::remember('active_catalog_categories', 3600, function () {
            return ProductCatalog::available()
                ->select('category')
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values();
        });

        return view('pages.home', compact('featuredProducts', 'categories'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}